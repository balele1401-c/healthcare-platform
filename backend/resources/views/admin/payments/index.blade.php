@extends('layouts.admin')

@section('title', 'Payments & Invoicing')
@section('page_title', 'Billing & Payments')

@section('content')
<div class="space-y-6">
    <!-- Top Financial Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Settled Revenue</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">${{ number_format($totalPaidAmount, 2) }}</h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">Verified patient payments in platform</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Settlement Invoices</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1">${{ number_format($totalPendingAmount, 2) }}</h3>
                <p class="text-xs text-slate-500 mt-1">Awaiting confirmation or clinic processing</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Payment Transactions</h2>
            <p class="text-xs text-slate-500">Audit trail of consultation payments and billing invoices</p>
        </div>

        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Txn ID, patient, code..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $status)
                <a href="{{ route('admin.payments.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Payments Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Transaction ID</th>
                        <th class="py-3.5 px-6">Booking Code</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Amount</th>
                        <th class="py-3.5 px-6">Payment Method</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $pay)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-semibold text-slate-900 text-xs">
                                {{ $pay->transaction_id ?? 'TXN-' . $pay->id }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-700">
                                {{ $pay->appointment->booking_code ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $pay->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $pay->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900">
                                {{ $pay->appointment->doctor->user->name ?? 'Doctor' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-bold text-slate-900">
                                ${{ number_format($pay->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700 font-medium">
                                {{ strtoupper($pay->payment_method ?? 'CREDIT_CARD') }}
                            </td>
                            <td class="py-3.5 px-6">
                                @php
                                    $pStatus = is_object($pay->payment_status) ? $pay->payment_status->value : $pay->payment_status;
                                    $pLabel = is_object($pay->payment_status) ? $pay->payment_status->label() : ucfirst($pay->payment_status);
                                    $pStyles = match($pStatus) {
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'refunded' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $pStyles }}">
                                    {{ $pLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $pay->paid_at ? date('M d, Y H:i', strtotime($pay->paid_at)) : $pay->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No payment transactions found.
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
