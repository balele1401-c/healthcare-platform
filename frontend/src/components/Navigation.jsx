import { useNavigate, useLocation } from 'react-router-dom';
import { BarChart3, LayoutDashboard } from 'lucide-react';

const items = [
  { path: '/dashboard', label: 'Home', icon: LayoutDashboard },
  { path: '/analytics', label: 'Charts', icon: BarChart3 },
];

export default function Navigation() {
  const navigate = useNavigate();
  const location = useLocation();

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-40 border-t border-app-border bg-white/90 backdrop-blur-md pb-[env(safe-area-inset-bottom)] md:hidden">
      <div className="mx-auto flex max-w-md items-center justify-around px-6">
        {items.map(({ path, label, icon: Icon }) => {
          const active = location.pathname === path;
          return (
            <button
              key={path}
              onClick={() => navigate(path)}
              aria-label={label}
              className={`flex flex-1 cursor-pointer flex-col items-center gap-1 py-2.5 transition-colors duration-200 ${
                active ? 'text-emerald-600' : 'text-slate-400 active:text-slate-500'
              }`}
            >
              <span
                className={`grid h-10 w-14 place-items-center rounded-2xl transition-all duration-200 ${
                  active ? 'bg-emerald-50' : 'bg-transparent'
                }`}
              >
                <Icon size={22} strokeWidth={active ? 2.4 : 2} />
              </span>
              <span className="text-[11px] font-semibold">{label}</span>
            </button>
          );
        })}
      </div>
    </nav>
  );
}