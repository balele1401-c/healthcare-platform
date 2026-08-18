export default function LoadingSkeleton() {
  return (
    <div className="space-y-6">
      <div className="space-y-3">
        <div className="skeleton h-3 w-40" />
        <div className="skeleton h-8 w-64" />
        <div className="skeleton h-3 w-80 max-w-full" />
      </div>

      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {[0, 1, 2, 3].map((i) => (
          <div key={i} className="skeleton h-44" />
        ))}
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        {[0, 1].map((i) => (
          <div key={i} className="skeleton h-96" />
        ))}
      </div>
    </div>
  );
}