import { useState, useRef, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  HeartPulse, CalendarDays, ChevronDown, LayoutDashboard,
  BarChart3, LogOut, Sparkles,
} from 'lucide-react';
import { useAuthStore } from '../stores/authStore.js';
import { toast } from 'sonner';

const navItems = [
  { path: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { path: '/analytics', label: 'Analytics', icon: BarChart3 },
];

export default function AppHeader() {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();
  const location = useLocation();
  const [menuOpen, setMenuOpen] = useState(false);
  const menuRef = useRef(null);

  useEffect(() => {
    const close = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) setMenuOpen(false);
    };
    document.addEventListener('mousedown', close);
    return () => document.removeEventListener('mousedown', close);
  }, []);

  const handleLogout = () => {
    logout();
    toast.success('Signed out, see you soon!');
    navigate('/login');
  };

  const dateStr = new Date().toLocaleDateString('en-US', {
    weekday: 'short', month: 'short', day: 'numeric',
  });

  const initials = (user?.name || 'U')
    .split(' ')
    .filter(Boolean)
    .map((s) => s[0])
    .slice(0, 2)
    .join('')
    .toUpperCase();

  return (
    <header className="sticky top-0 z-50 border-b border-app-border bg-white/85 backdrop-blur-md">
      <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        {/* Brand */}
        <button
          onClick={() => navigate('/dashboard')}
          className="group flex items-center gap-2.5 cursor-pointer"
          aria-label="PulseTrack home"
        >
          <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm transition-transform duration-200 group-hover:scale-105">
            <HeartPulse size={20} strokeWidth={2.5} />
          </span>
          <span className="font-display text-lg font-extrabold tracking-tight text-slate-900">
            Pulse<span className="text-emerald-600">Track</span>
          </span>
        </button>

        {/* Desktop nav */}
        <nav className="hidden items-center gap-1 md:flex">
          {navItems.map(({ path, label, icon: Icon }) => {
            const active = location.pathname === path;
            return (
              <button
                key={path}
                onClick={() => navigate(path)}
                className={`flex cursor-pointer items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition-all duration-200 ${
                  active
                    ? 'bg-emerald-50 text-emerald-700'
                    : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900'
                }`}
              >
                <Icon size={16} className={active ? 'text-emerald-600' : 'text-slate-400'} />
                {label}
              </button>
            );
          })}
        </nav>

        <div className="flex items-center gap-2.5">
          {/* Date badge */}
          <span className="hidden items-center gap-1.5 rounded-full border border-app-border bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 sm:inline-flex">
            <CalendarDays size={14} className="text-emerald-600" />
            {dateStr}
          </span>

          {/* Avatar dropdown */}
          <div ref={menuRef} className="relative">
            <button
              onClick={() => setMenuOpen((o) => !o)}
              className="flex cursor-pointer items-center gap-2 rounded-xl border border-app-border bg-white py-1 pl-1 pr-2 transition-all duration-200 hover:border-slate-300 hover:shadow-sm"
              aria-haspopup="menu"
              aria-expanded={menuOpen}
            >
              <span className="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-xs font-bold text-white">
                {initials}
              </span>
              <ChevronDown
                size={14}
                className={`text-slate-400 transition-transform duration-200 ${menuOpen ? 'rotate-180' : ''}`}
              />
            </button>

            {menuOpen && (
              <div
                role="menu"
                className="absolute right-0 mt-2 w-60 rounded-2xl border border-app-border bg-white p-1.5 shadow-xl"
              >
                <div className="px-3 pb-1 pt-2">
                  <p className="truncate font-display text-sm font-bold text-slate-900">{user?.name}</p>
                  <p className="truncate text-xs text-slate-500">{user?.email}</p>
                </div>

                <div className="mx-3 my-1.5 border-t border-slate-100" />

                {navItems.map(({ path, label, icon: Icon }) => {
                  const active = location.pathname === path;
                  return (
                    <button
                      key={path}
                      role="menuitem"
                      onClick={() => { setMenuOpen(false); navigate(path); }}
                      className={`flex w-full cursor-pointer items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium transition-colors duration-150 md:hidden ${
                        active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                      }`}
                    >
                      <Icon size={16} className={active ? 'text-emerald-600' : 'text-slate-400'} />
                      {label}
                    </button>
                  );
                })}

                <div className="mx-3 my-1.5 md:hidden border-t border-slate-100" />

                <button
                  role="menuitem"
                  onClick={handleLogout}
                  className="flex w-full cursor-pointer items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium text-rose-600 transition-colors duration-150 hover:bg-rose-50"
                >
                  <LogOut size={16} />
                  Log out
                </button>

                <button
                  onClick={() => { setMenuOpen(false); navigate('/analytics'); }}
                  className="mt-1 hidden w-full cursor-pointer items-center gap-2.5 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-transform duration-150 active:scale-[0.98] md:flex"
                >
                  <Sparkles size={16} />
                  View weekly insights
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}