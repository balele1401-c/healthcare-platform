import { useState, useEffect } from 'react';
import { Droplets, MoonStar, Footprints, Pill, CalendarDays } from 'lucide-react';
import { useAuthStore } from '../stores/authStore.js';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';
import AppHeader from '../components/AppHeader.jsx';
import Navigation from '../components/Navigation.jsx';
import LoadingSkeleton from '../components/LoadingSkeleton.jsx';
import MetricCard from '../components/MetricCard.jsx';
import WaterTracker from '../components/WaterTracker.jsx';
import SleepLog from '../components/SleepLog.jsx';
import VitalMetrics from '../components/VitalMetrics.jsx';
import MedicationChecklist from '../components/MedicationChecklist.jsx';

export default function Dashboard() {
  const { user } = useAuthStore();
  const [todayLog, setTodayLog] = useState(null);
  const [medications, setMedications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [stepsLoading, setStepsLoading] = useState(false);

  const fetchAll = async () => {
    try {
      const [logRes, medRes] = await Promise.all([
        apiClient.get('/logs/today'),
        apiClient.get('/medications/today'),
      ]);
      setTodayLog(logRes.data.data);
      setMedications(medRes.data.data || []);
    } catch {
      toast.error('Could not load your daily data');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchAll(); }, []);

  const bumpSteps = async (delta) => {
    if (stepsLoading) return;
    setStepsLoading(true);
    try {
      await apiClient.post('/logs/steps', { steps: (todayLog?.steps_count || 0) + delta });
      toast.success(`Logged ${delta.toLocaleString()} steps`);
      await fetchAll();
    } catch {
      toast.error('Could not update steps');
    } finally {
      setStepsLoading(false);
    }
  };

  const hour = new Date().getHours();
  const greeting = hour < 5 ? 'Good night' : hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
  const firstName = (user?.name || 'there').split(' ')[0];
  const longDate = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

  const metricCards = [
    {
      id: 'water',
      icon: Droplets,
      label: 'Water today',
      value: todayLog?.water_intake_ml?.toLocaleString() || 0,
      unit: 'ml',
      target: 'Daily goal 2,000 ml',
      progress: ((todayLog?.water_intake_ml || 0) / 2000) * 100,
      tone: 'teal',
    },
    {
      id: 'sleep',
      icon: MoonStar,
      label: 'Sleep logged',
      value: (todayLog?.sleep_hours || 0).toFixed(1),
      unit: 'h',
      target: 'Daily goal 8.0 h',
      progress: ((todayLog?.sleep_hours || 0) / 8) * 100,
      tone: 'indigo',
    },
    {
      id: 'steps',
      icon: Footprints,
      label: 'Steps today',
      value: (todayLog?.steps_count || 0).toLocaleString(),
      unit: 'steps',
      target: 'Suggested 10,000',
      progress: ((todayLog?.steps_count || 0) / 10000) * 100,
      tone: 'amber',
      extra: (
        <div className="mt-4 flex gap-2">
          <button onClick={() => bumpSteps(1000)} disabled={stepsLoading} className="btn-pill border border-slate-200 bg-white text-slate-600 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600">
            +1,000
          </button>
          <button onClick={() => bumpSteps(2500)} disabled={stepsLoading} className="btn-pill border border-slate-200 bg-white text-slate-600 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600">
            +2,500
          </button>
        </div>
      ),
    },
    {
      id: 'meds',
      icon: Pill,
      label: 'Medications',
      value: medications.filter((m) => m.status === 'taken').length,
      unit: `of ${medications.length} taken`,
      target: 'Daily adherence',
      progress: medications.length
        ? (medications.filter((m) => m.status === 'taken').length / medications.length) * 100
        : 100,
      tone: 'violet',
    },
  ];

  if (loading) {
    return (
      <div className="min-h-screen bg-app-bg">
        <AppHeader />
        <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
          <LoadingSkeleton />
        </main>
        <Navigation />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app-bg pb-24 md:pb-0">
      <AppHeader />

      <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {/* Greeting hero */}
        <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
          <div>
            <p className="mb-1 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-emerald-600">
              <CalendarDays size={14} />
              {longDate}
            </p>
            <h1 className="font-display text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              {greeting}, {firstName}
            </h1>
            <p className="mt-1 text-sm text-slate-500">
              Keep your rhythm — 2 L of water, 8 hrs of rest, 10k steps.
            </p>
          </div>
        </div>

        {/* Summary metric cards */}
        <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
          {metricCards.map((card) => (
            <MetricCard key={card.id} {...card} />
          ))}
        </div>

        {/* Quick logging: Water (wide) + Sleep */}
        <div className="mt-6 grid gap-6 lg:grid-cols-3">
          <div className="lg:col-span-2">
            <WaterTracker todayLog={todayLog} onUpdate={fetchAll} />
          </div>
          <SleepLog todayLog={todayLog} onUpdate={fetchAll} />
        </div>

        {/* Vitals + Medications */}
        <div className="mt-6 grid gap-6 lg:grid-cols-2">
          <VitalMetrics onUpdate={fetchAll} />
          <MedicationChecklist />
        </div>
      </main>

      <Navigation />
    </div>
  );
}