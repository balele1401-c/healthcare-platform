import { useState, useEffect } from 'react';
import { Pill, Clock, Check, X, Loader2 } from 'lucide-react';
import apiClient from '../services/apiClient.js';
import { toast } from 'sonner';

const statusStyles = {
  taken: 'bg-emerald-50 text-emerald-700',
  skipped: 'bg-rose-50 text-rose-600',
  pending: 'bg-amber-50 text-amber-600',
};

export default function MedicationChecklist() {
  const [medications, setMedications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [updating, setUpdating] = useState({});

  const fetchMedications = async () => {
    try {
      const res = await apiClient.get('/medications/today');
      setMedications(res.data.data || []);
    } catch {
      toast.error('Could not load medications');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchMedications(); }, []);

  const setStatus = async (id, status) => {
    setUpdating((prev) => ({ ...prev, [id]: true }));
    try {
      await apiClient.post(`/medications/${id}/check`, { status });
      toast.success(status === 'taken' ? 'Marked as taken' : 'Marked as skipped');
      await fetchMedications();
    } catch {
      toast.error('Could not update medication');
    } finally {
      setUpdating((prev) => ({ ...prev, [id]: false }));
    }
  };

  const loadingSkeleton = (
    <div className="space-y-3">
      {[0, 1].map((i) => (
        <div key={i} className="skeleton h-16" />
      ))}
    </div>
  );

  return (
    <section className="card card-hover p-6">
      <div className="mb-5 flex items-center gap-2">
        <span className="grid h-9 w-9 place-items-center rounded-xl bg-violet-50 text-violet-500">
          <Pill size={18} strokeWidth={2.3} />
        </span>
        <div>
          <h3 className="font-display text-base font-bold text-slate-900">Today's Medications</h3>
          <p className="text-xs text-slate-400">
            {!loading && medications.filter((m) => m.status === 'taken').length}/{medications.length} taken
          </p>
        </div>
      </div>

      {loading ? (
        loadingSkeleton
      ) : medications.length === 0 ? (
        <div className="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
          <Pill size={24} className="mx-auto mb-2 text-slate-300" />
          <p className="text-sm font-semibold text-slate-500">No medications scheduled today</p>
        </div>
      ) : (
        <div className="space-y-3">
          {medications.map((med) => {
            const status = ['taken', 'skipped'].includes(med.status) ? med.status : 'pending';
            const busy = updating[med.id];
            return (
              <div
                key={med.id}
                className="group rounded-2xl border border-app-border bg-white p-4 transition-all duration-200 hover:border-slate-300 hover:shadow-sm"
              >
                <div className="flex flex-wrap items-center gap-3">
                  <span className="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-violet-50 group-hover:text-violet-500 transition-colors duration-200">
                    <Pill size={18} />
                  </span>

                  <div className="min-w-0 flex-1">
                    <p className="truncate font-display text-sm font-bold text-slate-900">{med.name}</p>
                    <p className="text-xs text-slate-400">
                      {med.dosage && <span className="font-medium text-slate-500">{med.dosage} · </span>}
                      <span className="inline-flex items-center gap-1">
                        <Clock size={11} /> {med.schedule_time}
                      </span>
                    </p>
                  </div>

                  <span className={`shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold capitalize ${statusStyles[status]}`}>
                    {status}
                  </span>
                </div>

                <div className="mt-3 flex items-center gap-2">
                  <button
                    onClick={() => setStatus(med.id, 'taken')}
                    disabled={busy || status === 'taken'}
                    className={`btn-pill flex-1 ${status === 'taken'
                      ? 'bg-emerald-500 text-white shadow-sm'
                      : 'border border-slate-200 bg-white text-slate-600 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700'}`}
                  >
                    {busy ? <Loader2 size={13} className="animate-spin" /> : <Check size={13} strokeWidth={3} />}
                    Taken
                  </button>
                  <button
                    onClick={() => setStatus(med.id, 'skipped')}
                    disabled={busy || status === 'skipped'}
                    className={`btn-pill flex-1 ${status === 'skipped'
                      ? 'bg-rose-500 text-white shadow-sm'
                      : 'border border-slate-200 bg-white text-slate-600 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600'}`}
                  >
                    {busy ? <Loader2 size={13} className="animate-spin" /> : <X size={13} strokeWidth={3} />}
                    Skipped
                  </button>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </section>
  );
}