@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

@php
    $hour        = now()->hour;
    $greeting    = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $catCount    = \App\Models\Category::count();
    $matCount    = \App\Models\Material::count();
    $txTotal     = \App\Models\InwardOutward::count();
    $txToday     = \App\Models\InwardOutward::whereDate('date', today())->count();
    $recentTx    = \App\Models\InwardOutward::with('material.category')
                      ->orderByDesc('id')->limit(6)->get();
@endphp

{{-- Welcome banner --}}
<div style="background:linear-gradient(135deg,#111827 0%,#1f2937 60%,#5c4420 100%);
            border-radius:12px;padding:2rem 2rem 1.75rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;
                background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-30px;right:80px;width:120px;height:120px;
                background:rgba(92,68,32,.3);border-radius:50%;"></div>
    <p style="color:rgba(255,255,255,.6);font-size:.82rem;margin:0 0 .25rem;">
        {{ now()->format('l, d F Y') }}
    </p>
    <h2 style="color:#fff;font-size:1.55rem;font-weight:700;margin:0 0 .4rem;">
        {{ $greeting }}, {{ Auth::user()->name }}!
    </h2>
    <p style="color:rgba(255,255,255,.5);font-size:.83rem;margin:0;">
        Here's a snapshot of your inventory today.
    </p>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-lg-3">
        <a href="{{ route('categories.index') }}" style="text-decoration:none;">
            <div class="inv-card h-100" style="transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
                <div class="inv-card-body">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#dbeafe;
                                    display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#2563eb;">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <i class="bi bi-arrow-up-right" style="font-size:.8rem;color:#9ca3af;"></i>
                    </div>
                    <div style="font-size:2rem;font-weight:800;color:#111827;line-height:1;">{{ $catCount }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;font-weight:500;">Categories</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="{{ route('materials.index') }}" style="text-decoration:none;">
            <div class="inv-card h-100" style="transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
                <div class="inv-card-body">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#d1fae5;
                                    display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#059669;">
                            <i class="bi bi-archive-fill"></i>
                        </div>
                        <i class="bi bi-arrow-up-right" style="font-size:.8rem;color:#9ca3af;"></i>
                    </div>
                    <div style="font-size:2rem;font-weight:800;color:#111827;line-height:1;">{{ $matCount }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;font-weight:500;">Materials</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="{{ route('inward_outward.index') }}" style="text-decoration:none;">
            <div class="inv-card h-100" style="transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
                <div class="inv-card-body">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                        <div style="width:44px;height:44px;border-radius:10px;background:#ede9fe;
                                    display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <i class="bi bi-arrow-up-right" style="font-size:.8rem;color:#9ca3af;"></i>
                    </div>
                    <div style="font-size:2rem;font-weight:800;color:#111827;line-height:1;">{{ $txTotal }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;font-weight:500;">Total Transactions</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <div class="inv-card h-100" style="background:linear-gradient(135deg,#111827,#1f2937);">
            <div class="inv-card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(255,255,255,.12);
                                display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <span style="font-size:.72rem;background:rgba(255,255,255,.12);color:rgba(255,255,255,.7);
                                 padding:.2rem .55rem;border-radius:20px;font-weight:500;">Today</span>
                </div>
                <div style="font-size:2rem;font-weight:800;color:#fff;line-height:1;">{{ $txToday }}</div>
                <div style="font-size:.78rem;color:rgba(255,255,255,.5);margin-top:.25rem;font-weight:500;">Transactions Today</div>
            </div>
        </div>
    </div>

</div>

{{-- Bottom: recent transactions + quick actions --}}
<div class="row g-3">

    {{-- Recent Transactions --}}
    <div class="col-12 col-lg-8">
        <div class="inv-card h-100">
            <div class="inv-card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <div>
                        <div style="font-size:.95rem;font-weight:700;color:#111827;">Recent Transactions</div>
                        <div style="font-size:.75rem;color:#9ca3af;">Latest 6 stock movements</div>
                    </div>
                    <a href="{{ route('inward_outward.index') }}" class="btn-dark-inv" style="font-size:.78rem;padding:.3rem .75rem;text-decoration:none;">
                        View all
                    </a>
                </div>

                @if($recentTx->isEmpty())
                    <div style="text-align:center;padding:2rem 0;color:#9ca3af;">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                        No transactions yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table w-100" style="margin:0;">
                            <thead>
                                <tr>
                                    <th style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #e5e7eb;padding:.5rem .75rem;">Material</th>
                                    <th style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #e5e7eb;padding:.5rem .75rem;">Category</th>
                                    <th style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #e5e7eb;padding:.5rem .75rem;">Date</th>
                                    <th style="font-size:.73rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #e5e7eb;padding:.5rem .75rem;text-align:right;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTx as $tx)
                                <tr>
                                    <td style="font-size:.83rem;padding:.6rem .75rem;border-bottom:1px solid #f3f4f6;color:#111827;font-weight:500;">
                                        {{ $tx->material->name ?? '—' }}
                                    </td>
                                    <td style="font-size:.8rem;padding:.6rem .75rem;border-bottom:1px solid #f3f4f6;color:#6b7280;">
                                        {{ $tx->material->category->name ?? '—' }}
                                    </td>
                                    <td style="font-size:.8rem;padding:.6rem .75rem;border-bottom:1px solid #f3f4f6;color:#6b7280;">
                                        {{ $tx->date ? $tx->date->format('d M Y') : '—' }}
                                    </td>
                                    <td style="font-size:.83rem;padding:.6rem .75rem;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:700;
                                               color:{{ $tx->quantity >= 0 ? '#059669' : '#dc2626' }};">
                                        {{ $tx->quantity >= 0 ? '+' : '' }}{{ number_format((float)$tx->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-12 col-lg-4">
        <div class="inv-card h-100">
            <div class="inv-card-body">
                <div style="font-size:.95rem;font-weight:700;color:#111827;margin-bottom:.3rem;">Quick Actions</div>
                <div style="font-size:.75rem;color:#9ca3af;margin-bottom:1rem;">Jump to any section</div>

                <div style="display:flex;flex-direction:column;gap:.6rem;">

                    <a href="{{ route('categories.index') }}" style="text-decoration:none;display:flex;align-items:center;gap:.75rem;
                               padding:.75rem 1rem;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:background .12s;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:1rem;flex-shrink:0;">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:.83rem;font-weight:600;color:#111827;">Categories</div>
                            <div style="font-size:.74rem;color:#9ca3af;">{{ $catCount }} categories</div>
                        </div>
                        <i class="bi bi-chevron-right" style="font-size:.75rem;color:#9ca3af;"></i>
                    </a>

                    <a href="{{ route('materials.index') }}" style="text-decoration:none;display:flex;align-items:center;gap:.75rem;
                               padding:.75rem 1rem;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:background .12s;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#d1fae5;display:flex;align-items:center;justify-content:center;color:#059669;font-size:1rem;flex-shrink:0;">
                            <i class="bi bi-archive-fill"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:.83rem;font-weight:600;color:#111827;">Materials</div>
                            <div style="font-size:.74rem;color:#9ca3af;">{{ $matCount }} materials</div>
                        </div>
                        <i class="bi bi-chevron-right" style="font-size:.75rem;color:#9ca3af;"></i>
                    </a>

                    <a href="{{ route('inward_outward.index') }}" style="text-decoration:none;display:flex;align-items:center;gap:.75rem;
                               padding:.75rem 1rem;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:background .12s;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#ede9fe;display:flex;align-items:center;justify-content:center;color:#7c3aed;font-size:1rem;flex-shrink:0;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:.83rem;font-weight:600;color:#111827;">Inward / Outward</div>
                            <div style="font-size:.74rem;color:#9ca3af;">Record stock movement</div>
                        </div>
                        <i class="bi bi-chevron-right" style="font-size:.75rem;color:#9ca3af;"></i>
                    </a>

                    <a href="{{ route('materials.manage') }}" style="text-decoration:none;display:flex;align-items:center;gap:.75rem;
                               padding:.75rem 1rem;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:background .12s;"
                       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                        <div style="width:36px;height:36px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:1rem;flex-shrink:0;">
                            <i class="bi bi-table"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:.83rem;font-weight:600;color:#111827;">Manage Materials</div>
                            <div style="font-size:.74rem;color:#9ca3af;">View current balances</div>
                        </div>
                        <i class="bi bi-chevron-right" style="font-size:.75rem;color:#9ca3af;"></i>
                    </a>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
