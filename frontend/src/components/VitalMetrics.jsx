import { useState } from 'react';
import { Scale, HeartPulse, Droplet, Loader2 } from 'lucide-react';
import { useAuthStore } from '../stores/authStore.js';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';

const tabs = [
  { id: 'weight', label: 'Weight', icon: Scale, tone: 'text-violet-600 bg-violet-50', accent: 'text-violet-600' },
  { id: 'blood_pressure', label: 'Blood Pressure', icon: HeartPulse, tone: 'text-rose-500 bg-rose-50', accent: 'text-rose-500' },
  { id: 'glucose', label: 'Glucose', icon: Droplet, tone: 'text-amber-500 bg-amber-50', accent: 'text-amber-500' },
];

export default function VitalMetrics({ onUpdate }) {
  const { user } = useAuthStore();
  const [tab, setTab] = useState('weight');
  const [loading, setLoading] = useState(false);

  const [weight, setWeight] = useState('');
  const [systolic, setSystolic] = useState('');
  const [diastolic, setDiastolic] = useState('');
  const [glucose, setGlucose] = useState('');

  const heightCm = user?.height_cm;
  const bmi = weight && heightCm
    ? (parseFloat(weight) / ((heightCm / 100) ** 2)).toFixed(1)
    : null;

  const active = tabs.find((t) => t.id === tab);

  const submit = async () => {
    setLoading(true);
    try {
      if (tab === 'weight') {
        if (!weight) return toast.error('Enter your weight');
        await apiClient.post('/metrics', { metric_type: 'weight', value_primary: parseFloat(weight) });
        toast.success('Weight saved');
        setWeight('');
      } else if (tab === 'blood_pressure') {
        if (!systolic || !diastolic) return toast.error('Enter both systolic and diastolic');
        await apiClient.post('/metrics', {
          metric_type: 'blood_pressure',
          value_primary: parseFloat(systolic),
          value_secondary: parseFloat(diastolic),
        });
        toast.success('Blood pressure saved');
        setSystolic(''); setDiastolic('');
      } else {
        if (!glucose) return toast.error('Enter your glucose level');
        await apiClient.post('/metrics', { metric_type: 'glucose', value_primary: parseFloat(glucose) });
        toast.success('Glucose saved');
        setGlucose('');
      }
      onUpdate();
    } catch {
      toast.error('Could not save metric');
    } finally {
      setLoading(false);
    }
  };

  return (
    <section className="card card-hover p-6">
      <div className="mb-5 flex items-center gap-2">
        <span className="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
          <HeartPulse size={18} strokeWidth={2.3} />
        </span>
        <div>
          <h3 className="font-display text-base font-bold text-slate-900">Vital Metrics</h3>
          <p className="text-xs text-slate-400">Quick log weight, pressure or glucose</p>
        </div>
      </div>

      {/* Segmented tabs */}
      <div className="mb-4 flex rounded-2xl border border-app-border bg-slate-50 p-1">
        {tabs.map(({ id, label, icon: Icon }) => (
          <button
            key={id}
            onClick={() => setTab(id)}
            className={`flex flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl px-2 py-2 text-xs font-bold transition-all duration-200 ${
              tab === id ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'
            }`}
          >
            <Icon size={14} className={tab === id ? active.tone.split(' ')[0] : 'text-slate-400'} />
            <span className="hidden sm:inline">{label}</span>
            <span className="sm:hidden">{label.split(' ')[0]}</span>
          </button>
        ))}
      </div>

      {tab === 'weight' && (
        <div className="grid gap-3 sm:grid-cols-2">
          <label className="block">
            <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Body weight</span>
            <span className="input-group">
              <Scale size={16} className="text-violet-500" />
              <input
                type="number" inputMode="decimal" step="0.1" placeholder="70.5"
                value={weight} onChange={(e) => setWeight(e.target.value)}
                className="input-plain" aria-label="Weight in kg"
              />
              <span className="text-xs font-bold text-slate-400">kg</span>
            </span>
          </label>
          <div className="flex items-end">
            <div className="w-full rounded-2xl bg-violet-50/70 px-4 py-3">
              <p className="text-[11px] font-bold uppercase tracking-wider text-violet-400">BMI preview</p>
              <p className="font-display text-lg font-extrabold text-violet-600">
                {bmi ? bmi : '—'}
                {bmi && <span className="ml-1 text-xs font-bold text-violet-400">
                  {bmi < 18.5 ? 'Under' : bmi < 25 ? 'Healthy' : bmi < 30 ? 'Over' : 'Obese'}
                </span>}
              </p>
              {!heightCm && <p className="text-[11px] text-violet-400">Set height in profile for BMI</p>}
            </div>
          </div>
        </div>
      )}

      {tab === 'blood_pressure' && (
        <div className="grid grid-cols-2 gap-3">
          <label className="block">
            <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Systolic</span>
            <span className="input-group">
              <input type="number" placeholder="120" value={systolic} onChange={(e) => setSystolic(e.target.value)} className="input-plain" aria-label="Systolic" />
              <span className="text-xs font-bold text-slate-400">mmHg</span>
            </span>
          </label>
          <label className="block">
            <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Diastolic</span>
            <span className="input-group">
              <input type="number" placeholder="80" value={diastolic} onChange={(e) => setDiastolic(e.target.value)} className="input-plain" aria-label="Diastolic" />
              <span className="text-xs font-bold text-slate-400">mmHg</span>
            </span>
          </label>
        </div>
      )}

      {tab === 'glucose' && (
        <label className="block">
          <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Glucose level</span>
          <span className="input-group">
            <Droplet size={16} className="text-amber-500" />
            <input type="number" placeholder="95" value={glucose} onChange={(e) => setGlucose(e.target.value)} className="input-plain" aria-label="Glucose" />
            <span className="text-xs font-bold text-slate-400">mg/dL</span>
          </span>
        </label>
      )}

      <button onClick={submit} disabled={loading} className="btn-primary mt-4 w-full">
        {loading && <Loader2 size={16} className="animate-spin" />}
        {loading ? 'Saving…' : 'Save Metric'}
      </button>
    </section>
  );
}