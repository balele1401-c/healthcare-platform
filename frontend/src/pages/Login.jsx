import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { HeartPulse, Mail, Lock, Loader2, ArrowRight } from 'lucide-react';
import { useAuthStore } from '../stores/authStore.js';
import { toast } from 'sonner';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const navigate = useNavigate();
  const { login, isLoading, error } = useAuthStore();

  const handleSubmit = async (e) => {
    e.preventDefault();
    const res = await login(email.trim(), password);
    if (res.success) {
      toast.success('Welcome back!');
      navigate('/dashboard');
    } else {
      toast.error(res.error || 'Login failed');
    }
  };

  return (
    <div className="relative flex min-h-screen items-center justify-center overflow-hidden bg-app-bg px-4 py-10">
      {/* Ambient glow */}
      <div className="pointer-events-none absolute -top-32 right-0 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-32 left-0 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl" />

      <div className="relative w-full max-w-md">
        <div className="card overflow-hidden p-8 shadow-xl">
          <div className="mb-8 text-center">
            <span className="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md">
              <HeartPulse size={28} strokeWidth={2.5} />
            </span>
            <h1 className="font-display text-2xl font-extrabold tracking-tight text-slate-900">
              PulseTrack
            </h1>
            <p className="mt-1 text-sm text-slate-500">Your personal health &amp; habit companion</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <label className="block">
              <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Email</span>
              <span className="input-group">
                <Mail size={16} className="text-slate-400" />
                <input
                  type="email" value={email} onChange={(e) => setEmail(e.target.value)}
                  placeholder="you@example.com" className="input-plain" required
                />
              </span>
            </label>

            <label className="block">
              <span className="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Password</span>
              <span className="input-group">
                <Lock size={16} className="text-slate-400" />
                <input
                  type="password" value={password} onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••" className="input-plain" required
                />
              </span>
            </label>

            {error && (
              <div className="rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm font-medium text-rose-600">
                {error}
              </div>
            )}

            <button type="submit" disabled={isLoading} className="btn-primary w-full !py-3">
              {isLoading ? <Loader2 size={16} className="animate-spin" /> : 'Sign In'}
              {!isLoading && <ArrowRight size={16} />}
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-slate-500">
            New to PulseTrack?{' '}
            <Link to="/register" className="font-semibold text-emerald-600 hover:text-emerald-700">
              Create an account
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
}