import { useState } from 'react';
import { MoonStar, Sunrise, Timer, Loader2 } from 'lucide-react';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';

export default function SleepLog({ todayLog, onUpdate }) {
  const [start, setStart] = useState('');
  const [end, setEnd] = useState('');
  const [loading, setLoading] = useState(false);

  const loggedHours = todayLog?.sleep_hours || 0;

  const duration = (() => {
    if (!start || !end) return null;
    let s = new Date(`1970-01-01T${start}`);
    let e = new Date(`1970-01-01T${end}`);
    if (e <= s) e = new Date(e.getTime() + 24 * 60 * 60 * 1000);
    const hours = (e - s) / (1000 * 60 * 60);
    return {
      h: Math.floor(hours),
      m: Math.round((hours - Math.floor(hours)) * 60),
    };
  })();

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!start || !end) {
      toast.error('Pick both a sleep and wake time');
      return;
    }
    setLoading(true);
    try {
      const day = new Date().toISOString().split('T')[0];
      await apiClient.post('/logs/sleep', {
        start_time: new Date(`${day}T${start}`).toISOString(),
        end_time: new Date(`${day}T${end}`).toISOString(),
      });
      toast.success('Sleep logged');
      setStart('');
      setEnd('');
      onUpdate();
    } catch {
      toast.error('Could not save sleep log');
    } finally {
      setLoading(false);
    }
  };

  const hour = ('0' + (duration?.h ?? 0)).slice(-2);
  const min = ('0' + (duration?.m ?? 0)).slice(-2);

  return (
    <section className="card card-hover p-6">
      <div className="mb-5 flex items-center gap-2">
        <span className="grid h-9 w-9 place-items-center rounded-xl bg-indigo-50 text-indigo-500">
          <MoonStar size={18} strokeWidth={2.3} />
        </span>
        <div>
          <h3 className="font-display text-base font-bold text-slate-900">Sleep Log</h3>
          <p className="text-xs text-slate-400">
            {loggedHours > 0 ? `${loggedHours.toFixed(1)} h logged today` : 'No sleep logged today yet'}
          </p>
        </div>
      </div>

      {loggedHours > 0 && (
        <div className="mb-5 flex items-center justify-between rounded-2xl bg-indigo-50/60 px-4 py-3">
          <span className="text-xs font-semibold text-indigo-600">Today's rest</span>
          <span className="font-display text-lg font-extrabold text-indigo-600">
            {loggedHours.toFixed(1)} h
          </span>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="grid grid-cols-2 gap-3">
          <label className="block">
            <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Sleep</span>
            <span className="input-group">
              <MoonStar size={16} className="text-indigo-400" />
              <input type="time" value={start} onChange={(e) => setStart(e.target.value)} className="input-plain" aria-label="Sleep time" />
            </span>
          </label>
          <label className="block">
            <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Wake</span>
            <span className="input-group">
              <Sunrise size={16} className="text-amber-500" />
              <input type="time" value={end} onChange={(e) => setEnd(e.target.value)} className="input-plain" aria-label="Wake time" />
            </span>
          </label>
        </div>

        <div className="flex items-center justify-between rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3">
          <span className="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <Timer size={14} className="text-indigo-500" />
            Sleep duration
          </span>
          <span className="font-display text-base font-extrabold text-slate-900">
            {start && end ? `${hour}h ${min}m` : '—'}
          </span>
        </div>

        <button type="submit" disabled={loading} className="btn-primary w-full">
          {loading && <Loader2 size={16} className="animate-spin" />}
          {loading ? 'Saving…' : 'Log Sleep'}
        </button>
      </form>
    </section>
  );
}