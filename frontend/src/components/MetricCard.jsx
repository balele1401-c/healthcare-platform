const tones = {
  teal: { chip: 'bg-teal-50 text-teal-600', bar: 'bg-teal-500', icon: 'text-teal-600' },
  indigo: { chip: 'bg-indigo-50 text-indigo-500', bar: 'bg-indigo-500', icon: 'text-indigo-500' },
  amber: { chip: 'bg-amber-50 text-amber-500', bar: 'bg-amber-500', icon: 'text-amber-500' },
  violet: { chip: 'bg-violet-50 text-violet-500', bar: 'bg-violet-500', icon: 'text-violet-500' },
  emerald: { chip: 'bg-emerald-50 text-emerald-600', bar: 'bg-emerald-500', icon: 'text-emerald-600' },
  rose: { chip: 'bg-rose-50 text-rose-500', bar: 'bg-rose-500', icon: 'text-rose-500' },
};

export default function MetricCard({
  icon: Icon,
  label,
  value,
  unit = '',
  target,
  progress,
  tone = 'teal',
  extra,
}) {
  const t = tones[tone] || tones.teal;

  return (
    <div className="card card-hover group p-5">
      <div className="flex items-start justify-between">
        <span className={`grid h-10 w-10 place-items-center rounded-xl transition-transform duration-200 group-hover:scale-105 ${t.chip}`}>
          <Icon size={20} className={t.icon} strokeWidth={2.2} />
        </span>
        {progress !== undefined && (
          <span className="rounded-full bg-slate-50 px-2 py-0.5 text-xs font-bold text-slate-500">
            {Math.round(Math.min(progress, 100))}%
          </span>
        )}
      </div>

      <p className="mt-4 font-display text-[28px] font-extrabold leading-none tracking-tight text-slate-900">
        {value}
        {unit && <span className="ml-1 text-base font-bold text-slate-400">{unit}</span>}
      </p>
      <p className="mt-1.5 text-sm font-semibold text-slate-700">{label}</p>
      {target && <p className="text-xs text-slate-400">{target}</p>}

      {progress !== undefined && (
        <div className="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
          <div
            className={`h-full rounded-full ${t.bar}`}
            style={{ width: `${Math.min(progress, 100)}%` }}
          />
        </div>
      )}

      {extra}
    </div>
  );
}