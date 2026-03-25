@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">Total Revenue</span>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-stone-900">₹2,48,500</p>
            <p class="text-xs text-green-600 mt-1 font-medium">↑ 12.4% from last month</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">Pending Invoices</span>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-stone-900">12</p>
            <p class="text-xs text-red-500 mt-1 font-medium">↑ 3 overdue</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">Total Expenses</span>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-orange-50">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-stone-900">₹84,200</p>
            <p class="text-xs text-stone-400 mt-1 font-medium">↓ 4.1% from last month</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">Active Clients</span>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-stone-900">38</p>
            <p class="text-xs text-green-600 mt-1 font-medium">↑ 5 new this month</p>
        </div>

    </div>

    {{-- Recent Invoices + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Recent Invoices --}}
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
                <h2 class="text-sm font-semibold text-stone-800">Recent Invoices</h2>
                <a href="#" class="text-xs text-red-700 font-medium hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-stone-100">
                @foreach([
                    ['INV-0042', 'Acme Corp', '₹18,000', 'Overdue', 'red'],
                    ['INV-0041', 'TechSupply Co.', '₹32,500', 'Paid', 'green'],
                    ['INV-0040', 'Bright Media', '₹9,750', 'Pending', 'yellow'],
                    ['INV-0039', 'Nova Retail', '₹54,000', 'Paid', 'green'],
                    ['INV-0038', 'Skyline Infra', '₹12,200', 'Pending', 'yellow'],
                ] as $inv)
                <div class="flex items-center gap-4 px-5 py-3 hover:bg-stone-50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-stone-800">{{ $inv[0] }}</p>
                        <p class="text-xs text-stone-400">{{ $inv[1] }}</p>
                    </div>
                    <p class="text-sm font-semibold text-stone-700">{{ $inv[2] }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $inv[4] === 'green' ? 'bg-green-50 text-green-700' : ($inv[4] === 'red' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                        {{ $inv[3] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-100">
                <h2 class="text-sm font-semibold text-stone-800">Quick Actions</h2>
            </div>
            <div class="p-4 space-y-2">
                @foreach([
                    ['New Invoice', 'M12 4v16m8-8H4', 'bg-red-800 hover:bg-red-700 text-white'],
                    ['Add Expense', 'M12 4v16m8-8H4', 'bg-stone-100 hover:bg-stone-200 text-stone-700'],
                    ['Add Client', 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'bg-stone-100 hover:bg-stone-200 text-stone-700'],
                    ['Generate Report', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'bg-stone-100 hover:bg-stone-200 text-stone-700'],
                ] as $action)
                <a href="#" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $action[2] }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action[1] }}"/>
                    </svg>
                    {{ $action[0] }}
                </a>
                @endforeach
            </div>

            {{-- Mini summary --}}
            <div class="mx-4 mb-4 p-4 bg-stone-50 rounded-xl border border-stone-100">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-widest mb-2">This Month</p>
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-stone-500">Collected</span>
                        <span class="font-semibold text-stone-800">₹1,64,300</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-stone-500">Outstanding</span>
                        <span class="font-semibold text-red-600">₹84,200</span>
                    </div>
                    <div class="w-full bg-stone-200 rounded-full h-1.5 mt-2">
                        <div class="bg-red-700 h-1.5 rounded-full" style="width: 66%"></div>
                    </div>
                    <p class="text-[10px] text-stone-400">66% collected</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
