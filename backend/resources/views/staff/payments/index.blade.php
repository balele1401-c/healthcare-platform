@extends('layouts.staff')

@section('title', 'Billing & Payments Visibility')
@section('page_title', 'Billing & Operational Payment Statuses')

@section('content')
<div class="space-y-6">
    <!-- Top Summary Financial Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Settled Collections</span>
                <div class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaidAmount, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                $
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Settlement Invoices</span>
                <div class="text-2xl font-bold text-amber-600 mt-1">${{ number_format($totalPendingAmount, 2) }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Billing Transactions</h2>
            <p class="text-xs text-slate-500">Live operational visibility of consultation fees and settlement statuses</p>
        </div>

        <form method="GET" action="{{ route('staff.payments.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search transaction ID, patient..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Statuses</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <button type="submit" class="py-2 px-4 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                Filter
            </button>
        </form>
    </div>

    <!-- Payments Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Transaction ID</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Booking Code</th>
                        <th class="py-3.5 px-6">Amount</th>
                        <th class="py-3.5 px-6">Payment Method</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $pay)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ $pay->payment_reference }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $pay->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $pay->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-800">
                                Dr. {{ $pay->appointment->doctor->user->name ?? 'Doctor' }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-700">
                                {{ $pay->appointment->booking_code ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-sm">
                                ${{ number_format($pay->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700 uppercase">
                                {{ $pay->payment_method ?? 'Card' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ match($pay->status->value ?? $pay->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'failed' => 'bg-rose-50 text-rose-700',
                                    'refunded' => 'bg-slate-100 text-slate-700',
                                    default => 'bg-slate-100 text-slate-700',
                                } }}">
                                    {{ is_object($pay->status) ? $pay->status->label() : ucfirst($pay->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-400 font-mono text-right whitespace-nowrap">
                                {{ $pay->created_at ? $pay->created_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No billing records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
