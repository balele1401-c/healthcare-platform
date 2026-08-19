import { useState } from 'react';
import { Droplets, Loader2 } from 'lucide-react';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';

const GOAL = 2000;
const R = 56;
const CIRCUMFERENCE = 2 * Math.PI * R;

export default function WaterTracker({ todayLog, onUpdate }) {
  const [loading, setLoading] = useState(false);
  const [bumping, setBumping] = useState(false);

  const intake = todayLog?.water_intake_ml || 0;
  const pct = Math.min(intake / GOAL, 1);
  const offset = CIRCUMFERENCE * (1 - pct);

  const quickActions = [
    { label: '+250 ml', delta: 250 },
    { label: '+500 ml', delta: 500 },
    { label: '+1 L', delta: 1000 },
  ];

  const updateWater = async (delta) => {
    if (loading) return;
    setLoading(true);
    setBumping(true);
    setTimeout(() => setBumping(false), 700);
    try {
      await apiClient.post('/logs/water', { delta_ml: delta });
      toast.success(`Logged ${delta} ml`);
      onUpdate();
    } catch {
      toast.error('Could not update water intake');
    } finally {
      setLoading(false);
    }
  };

  const remaining = Math.max(0, GOAL - intake);

  return (
    <section className="card card-hover p-6">
      <div className="mb-5 flex items-center gap-2">
        <span className="grid h-9 w-9 place-items-center rounded-xl bg-teal-50 text-teal-600">
          <Droplets size={18} strokeWidth={2.3} />
        </span>
        <div>
          <h3 className="font-display text-base font-bold text-slate-900">Water Intake</h3>
          <p className="text-xs text-slate-400">{remaining > 0 ? `${(remaining / 1000).toFixed(2)} L to goal` : 'Goal reached, keep it up!'}</p>
        </div>
      </div>

      <div className="flex flex-col items-center gap-6 sm:flex-row sm:justify-between sm:gap-8">
        {/* Circular ring */}
        <div className={`relative shrink-0 ${bumping ? 'water-bump' : ''}`}>
          <svg viewBox="0 0 140 140" className="h-44 w-44 -rotate-90">
            <defs>
              <linearGradient id="waterGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#14B8A6" />
                <stop offset="100%" stopColor="#059669" />
              </linearGradient>
            </defs>
            <circle cx="70" cy="70" r={R} fill="none" stroke="#E2E8F0" strokeWidth="10" />
            <circle
              cx="70" cy="70" r={R} fill="none"
              stroke="url(#waterGrad)" strokeWidth="10" strokeLinecap="round"
              strokeDasharray={CIRCUMFERENCE}
              strokeDashoffset={offset}
              style={{ transition: 'stroke-dashoffset 700ms cubic-bezier(0.33,1,0.68,1)' }}
            />
          </svg>
          <div className="absolute inset-0 flex flex-col items-center justify-center">
            <span className="font-display text-2xl font-extrabold tracking-tight text-slate-900">
              {intake.toLocaleString()}
              <span className="ml-0.5 text-sm font-bold text-slate-400">ml</span>
            </span>
            <span className="mt-0.5 text-xs font-semibold text-slate-400">
              {Math.round(pct * 100)}% of {GOAL.toLocaleString()} ml
            </span>
          </div>
        </div>

        {/* Quick actions */}
        <div className="flex w-full max-w-[240px] flex-col gap-2.5">
          {quickActions.map(({ label, delta }) => (
            <button
              key={delta}
              onClick={() => updateWater(delta)}
              disabled={loading}
              className="btn-ghost w-full justify-between !rounded-2xl !py-3"
            >
              <span className="flex items-center gap-2">
                <span className="grid h-6 w-6 place-items-center rounded-lg bg-teal-50 text-teal-600">
                  <Droplets size={14} />
                </span>
                {label}
              </span>
              {loading && <Loader2 size={16} className="animate-spin text-slate-400" />}
            </button>
          ))}
          <button
            onClick={() => updateWater(-250)}
            disabled={loading || intake === 0}
            className="mt-1 cursor-pointer text-center text-xs font-semibold text-slate-400 transition-colors duration-150 hover:text-rose-500 disabled:pointer-events-none disabled:opacity-40"
          >
            Undo −250 ml
          </button>
        </div>
      </div>
    </section>
  );
}