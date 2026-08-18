import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import {
  HeartPulse, Mail, Lock, UserRound, Ruler,
  Droplets, MoonStar, Loader2, ArrowRight,
} from 'lucide-react';
import { useAuthStore } from '../stores/authStore.js';
import { toast } from 'sonner';

const initial = { name: '', email: '', password: '', height_cm: '', water_goal_ml: '2000', sleep_goal_hours: '8' };

export default function Register() {
  const [form, setForm] = useState(initial);
  const navigate = useNavigate();
  const { register, isLoading, error } = useAuthStore();

  const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

  const handleSubmit = async (e) => {
    e.preventDefault();
    const payload = {
      name: form.name.trim(),
      email: form.email.trim(),
      password: form.password,
    };
    if (form.height_cm) payload.height_cm = Number(form.height_cm);
    if (form.water_goal_ml) payload.water_goal_ml = Number(form.water_goal_ml);
    if (form.sleep_goal_hours) payload.sleep_goal_hours = Number(form.sleep_goal_hours);

    const res = await register(payload);
    if (res.success) {
      toast.success('Account created, welcome!');
      navigate('/dashboard');
    } else {
      toast.error(res.error || 'Registration failed');
    }
  };

  return (
    <div className="relative flex min-h-screen items-center justify-center overflow-hidden bg-app-bg px-4 py-10">
      <div className="pointer-events-none absolute -top-32 right-0 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-32 left-0 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl" />

      <div className="relative w-full max-w-md">
        <div className="card overflow-hidden p-8 shadow-xl">
          <div className="mb-8 text-center">
            <span className="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md">
              <HeartPulse size={28} strokeWidth={2.5} />
            </span>
            <h1 className="font-display text-2xl font-extrabold tracking-tight text-slate-900">
              Create account
            </h1>
            <p className="mt-1 text-sm text-slate-500">Start tracking your health in seconds</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <label className="block">
              <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Full name</span>
              <span className="input-group">
                <UserRound size={16} className="text-slate-400" />
                <input type="text" value={form.name} onChange={set('name')} placeholder="Jane Doe" className="input-plain" required />
              </span>
            </label>

            <label className="block">
              <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Email</span>
              <span className="input-group">
                <Mail size={16} className="text-slate-400" />
                <input type="email" value={form.email} onChange={set('email')} placeholder="you@example.com" className="input-plain" required />
              </span>
            </label>

            <label className="block">
              <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Password</span>
              <span className="input-group">
                <Lock size={16} className="text-slate-400" />
                <input type="password" value={form.password} onChange={set('password')} placeholder="Min. 6 characters" className="input-plain" required minLength={6} />
              </span>
            </label>

            <div className="grid grid-cols-3 gap-3">
              <label className="block">
                <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Height</span>
                <span className="input-group">
                  <Ruler size={16} className="text-slate-400" />
                  <input type="number" value={form.height_cm} onChange={set('height_cm')} placeholder="170" className="input-plain" aria-label="Height in cm" />
                </span>
              </label>
              <label className="block">
                <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Water goal</span>
                <span className="input-group">
                  <Droplets size={16} className="text-teal-500" />
                  <input type="number" value={form.water_goal_ml} onChange={set('water_goal_ml')} className="input-plain" aria-label="Water goal in ml" />
                </span>
              </label>
              <label className="block">
                <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Sleep goal</span>
                <span className="input-group">
                  <MoonStar size={16} className="text-indigo-500" />
                  <input type="number" step="0.5" value={form.sleep_goal_hours} onChange={set('sleep_goal_hours')} className="input-plain" aria-label="Sleep goal hours" />
                </span>
              </label>
            </div>

            {error && (
              <div className="rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm font-medium text-rose-600">
                {error}
              </div>
            )}

            <button type="submit" disabled={isLoading} className="btn-primary w-full !py-3">
              {isLoading ? <Loader2 size={16} className="animate-spin" /> : 'Create Account'}
              {!isLoading && <ArrowRight size={16} />}
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-slate-500">
            Already have an account?{' '}
            <Link to="/login" className="font-semibold text-emerald-600 hover:text-emerald-700">
              Sign in
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}