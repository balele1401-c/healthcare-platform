import { useState, useEffect } from 'react';
import {
  AreaChart, Area, BarChart, Bar, XAxis, YAxis,
  CartesianGrid, Tooltip, ResponsiveContainer,
} from 'recharts';
import {
  TrendingUp, MoonStar, Droplets, ShieldCheck, Footprints, Loader2,
} from 'lucide-react';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';
import AppHeader from '../components/AppHeader.jsx';
import Navigation from '../components/Navigation.jsx';
import LoadingSkeleton from '../components/LoadingSkeleton.jsx';

function shortDate(iso) {
  const [y, m, d] = iso.split('-').map(Number);
  return new Date(y, m - 1, d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function ChartTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="min-w-[160px] rounded-2xl border border-slate-200 bg-white/95 px-4 py-3 shadow-xl backdrop-blur">
      <p className="mb-2 font-display text-xs font-bold text-slate-500">{label}</p>
      {payload.map((entry) => (
        <div key={entry.dataKey} className="flex items-center justify-between gap-6 py-0.5">
          <span className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
            <span className="h-2 w-2 rounded-full" style={{ background: entry.color }} />
            {entry.name}
          </span>
          <span className="font-display text-sm font-extrabold text-slate-900">
            {entry.name === 'Water' ? `${Math.round(entry.value).toLocaleString()} ml` : `${entry.value} h`}
          </span>
        </div>
      ))}
    </div>
  );
}

function StepsTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="min-w-[140px] rounded-2xl border border-slate-200 bg-white/95 px-4 py-3 shadow-xl backdrop-blur">
      <p className="mb-1 font-display text-xs font-bold text-slate-500">{label}</p>
      <p className="flex items-center justify-between gap-6 text-xs font-medium text-slate-500">
        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-amber-500" />Steps</span>
        <span className="font-display text-sm font-extrabold text-slate-900">
          {Math.round(payload[0].value).toLocaleString()}
        </span>
      </p>
    </div>
  );
}

function SummaryCard({ icon: Icon, label, value, suffix, sub, tone, progress }) {
  const tones = {
    indigo: { chip: 'bg-indigo-50 text-indigo-500', bar: 'bg-indigo-500', text: 'text-indigo-600' },
    teal: { chip: 'bg-teal-50 text-teal-600', bar: 'bg-teal-500', text: 'text-teal-600' },
    emerald: { chip: 'bg-emerald-50 text-emerald-600', bar: 'bg-emerald-500', text: 'text-emerald-600' },
  };
  const t = tones[tone];
  return (
    <div className="card card-hover p-5">
      <div className="flex items-start justify-between">
        <span className={`grid h-10 w-10 place-items-center rounded-xl ${t.chip}`}>
          <Icon size={20} strokeWidth={2.2} />
        </span>
        <span className="rounded-full bg-slate-50 px-2 py-0.5 text-xs font-bold text-slate-500">
          {Math.round(Math.min(progress, 100))}%
        </span>
      </div>
      <p className="mt-4 font-display text-[28px] font-extrabold leading-none tracking-tight text-slate-900">
        {value}<span className="ml-1 text-base font-bold text-slate-400">{suffix}</span>
      </p>
      <p className="mt-1.5 text-sm font-semibold text-slate-700">{label}</p>
      <p className="text-xs text-slate-400">{sub}</p>
      <div className="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
        <div className={`h-full rounded-full ${t.bar}`} style={{ width: `${Math.min(progress, 100)}%` }} />
      </div>
    </div>
  );
}

export default function Analytics() {
  const [weekly, setWeekly] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const res = await apiClient.get('/analytics/weekly');
        setWeekly(res.data.data);
      } catch {
        toast.error('Could not load analytics');
      } finally {
        setLoading(false);
      }
    })();
  }, []);

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

  if (!weekly) {
    return (
      <div className="min-h-screen bg-app-bg">
        <AppHeader />
        <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
          <div className="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-200 bg-white py-20 text-center">
            <Loader2 size={28} className="mb-3 animate-spin text-slate-300" />
            <p className="text-sm font-semibold text-slate-500">No analytics available yet</p>
            <p className="mt-1 text-xs text-slate-400">Start logging to see your weekly trends.</p>
          </div>
        </main>
        <Navigation />
      </div>
    );
  }

  const { water_trend = [], sleep_trend = [], steps_trend = [], summary, period } = weekly;

  const trendData = water_trend.map((w, i) => ({
    label: shortDate(w.date),
    date: w.date,
    water_ml: w.water_ml,
    sleep_hours: sleep_trend[i]?.hours || 0,
  }));

  const stepsData = steps_trend.map((s) => ({ label: shortDate(s.date), steps: s.steps }));

  const summaryCards = [
    {
      icon: MoonStar, label: 'Avg sleep · 7 days', tone: 'indigo',
      value: summary.avg_sleep_hours.toFixed(1), suffix: 'h',
      sub: 'Target 8.0 h per night', progress: (summary.avg_sleep_hours / 8) * 100,
    },
    {
      icon: Droplets, label: 'Total water · 7 days', tone: 'teal',
      value: (summary.total_water_ml / 1000).toFixed(1), suffix: 'L',
      sub: 'Target 14 L across the week', progress: (summary.total_water_ml / 14000) * 100,
    },
    {
      icon: ShieldCheck, label: 'Medication adherence', tone: 'emerald',
      value: summary.medication_adherence_percent.toFixed(0), suffix: '%',
      sub: `${summary.total_steps.toLocaleString()} steps this week`,
      progress: summary.medication_adherence_percent,
    },
  ];

  return (
    <div className="min-h-screen bg-app-bg pb-24 md:pb-0">
      <AppHeader />

      <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div className="mb-8">
          <p className="mb-1 text-xs font-bold uppercase tracking-wider text-indigo-500">Weekly insights</p>
          <h1 className="flex items-center gap-2.5 font-display text-3xl font-extrabold tracking-tight text-slate-900">
            <TrendingUp size={28} className="text-emerald-600" />
            Your week at a glance
          </h1>
          <p className="mt-1 text-sm text-slate-500">{period}</p>
        </div>

        {/* Summary cards */}
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          {summaryCards.map((c) => <SummaryCard key={c.label} {...c} />)}
        </div>

        {/* Water & Sleep - dual axis area */}
        <div className="card mt-6 p-6">
          <div className="mb-5 flex flex-wrap items-center justify-between gap-2">
            <div className="flex items-center gap-2">
              <span className="grid h-9 w-9 place-items-center rounded-xl bg-teal-50 text-teal-600">
                <Droplets size={18} strokeWidth={2.3} />
              </span>
              <div>
                <h3 className="font-display text-base font-bold text-slate-900">Hydration & Sleep</h3>
                <p className="text-xs text-slate-400">Daily water intake vs. hours of rest</p>
              </div>
            </div>
            <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
              <span className="flex items-center gap-1"><span className="h-2.5 w-2.5 rounded-full bg-teal-500" /> Water (ml)</span>
              <span className="flex items-center gap-1"><span className="h-2.5 w-2.5 rounded-full bg-indigo-500" /> Sleep (h)</span>
            </div>
          </div>

          <div className="h-80">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={trendData} margin={{ top: 8, right: 0, left: -8, bottom: 0 }}>
                <defs>
                  <linearGradient id="gradWater" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#14B8A6" stopOpacity={0.35} />
                    <stop offset="100%" stopColor="#14B8A6" stopOpacity={0} />
                  </linearGradient>
                  <linearGradient id="gradSleep" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#6366F1" stopOpacity={0.35} />
                    <stop offset="100%" stopColor="#6366F1" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="4 8" stroke="#E2E8F0" vertical={false} />
                <XAxis
                  dataKey="label" axisLine={false} tickLine={false}
                  tick={{ fontSize: 11, fill: '#94A3B8', fontWeight: 600 }}
                  dy={6}
                />
                <YAxis
                  yAxisId="water" orientation="left" axisLine={false} tickLine={false}
                  tick={{ fontSize: 11, fill: '#94A3B8' }} width={44}
                  tickFormatter={(v) => (v >= 1000 ? `${v / 1000}k` : v)}
                />
                <YAxis
                  yAxisId="sleep" orientation="right" domain={[0, 12]} axisLine={false} tickLine={false}
                  tick={{ fontSize: 11, fill: '#94A3B8' }} width={28}
                />
                <Tooltip content={<ChartTooltip />} cursor={{ stroke: '#CBD5E1', strokeDasharray: '4 4' }} />
                <Area
                  yAxisId="water" type="monotone" dataKey="water_ml" name="Water"
                  stroke="#14B8A6" strokeWidth={2.5} fill="url(#gradWater)"
                  activeDot={{ r: 5, strokeWidth: 2, stroke: '#fff' }}
                />
                <Area
                  yAxisId="sleep" type="monotone" dataKey="sleep_hours" name="Sleep"
                  stroke="#6366F1" strokeWidth={2.5} fill="url(#gradSleep)"
                  activeDot={{ r: 5, strokeWidth: 2, stroke: '#fff' }}
                />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Steps bar chart */}
        <div className="card mt-6 p-6">
          <div className="mb-5 flex items-center gap-2">
            <span className="grid h-9 w-9 place-items-center rounded-xl bg-amber-50 text-amber-500">
              <Footprints size={18} strokeWidth={2.3} />
            </span>
            <div>
              <h3 className="font-display text-base font-bold text-slate-900">Daily Steps</h3>
              <p className="text-xs text-slate-400">Steps per day over the last week</p>
            </div>
          </div>

          <div className="h-72">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={stepsData} margin={{ top: 8, right: 0, left: -8, bottom: 0 }}>
                <defs>
                  <linearGradient id="gradSteps" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#FBBF24" stopOpacity={0.95} />
                    <stop offset="100%" stopColor="#F59E0B" stopOpacity={0.75} />
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="4 8" stroke="#E2E8F0" vertical={false} />
                <XAxis dataKey="label" axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#94A3B8', fontWeight: 600 }} dy={6} />
                <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 11, fill: '#94A3B8' }} width={44} tickFormatter={(v) => (v >= 1000 ? `${v / 1000}k` : v)} />
                <Tooltip content={<StepsTooltip />} cursor={{ fill: '#F1F5F9' }} />
                <Bar dataKey="steps" name="Steps" fill="url(#gradSteps)" radius={[8, 8, 0, 0]} maxBarSize={44} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </main>

      <Navigation />
    </div>
  );
}