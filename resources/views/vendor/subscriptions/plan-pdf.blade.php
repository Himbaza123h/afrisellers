<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
        }

        .plan-page {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            overflow: hidden;
        }

        /* ── Watermark ── */
        .watermark {
            position: absolute;
            top: 36%;
            left: 5%;
            font-size: 72px;
            font-weight: 900;
            opacity: 0.03;
            color: #ff0808;
            transform: rotate(-35deg);
            letter-spacing: 6px;
            z-index: 0;
            white-space: nowrap;
        }

        /* ── Everything above footer ── */
        .page-inner {
            position: relative;
            z-index: 1;
            padding-bottom: 40px;
        }

        /* ── Footer pinned to bottom ── */
        .page-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 34px;
            padding: 0 32px;
            background: #fff5f5;
            border-top: 1px solid #fecaca;
            display: table;
            width: 100%;
            z-index: 2;
        }
        .footer-left   { display: table-cell; font-size: 8px; color: #94a3b8; vertical-align: middle; }
        .footer-center { display: table-cell; font-size: 8px; color: #ff0808; font-weight: 700;
                         text-align: center; vertical-align: middle; }
        .footer-right  { display: table-cell; font-size: 8px; color: #94a3b8;
                         text-align: right; vertical-align: middle; }

        /* ── Top accent bar ── */
        .accent-bar {
            height: 6px;
            width: 100%;
            background: linear-gradient(90deg, #1a2942 0%, #ff0808 100%);
        }

        /* ── Header ── */
        .page-header {
            padding: 18px 32px 14px;
            border-bottom: 2px solid #f1f5f9;
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }

        .org-name { font-size: 18px; font-weight: 700; color: #111; }
        .org-name span { color: #ff0808; }
        .org-sub  { font-size: 9px; color: #64748b; margin-top: 2px; }

        .doc-label    { font-size: 8px; font-weight: 700; text-transform: uppercase;
                        letter-spacing: 1.5px; color: #ff0808; }
        .plan-number  { font-size: 20px; font-weight: 900; color: #111;
                        font-family: DejaVu Sans Mono, monospace; letter-spacing: 1px; }
        .header-date  { font-size: 9px; color: #64748b; margin-top: 2px; }

        /* ── Status banner ── */
        .status-banner {
            margin: 0 32px;
            padding: 6px 14px;
            border-radius: 0 0 6px 6px;
            font-size: 10px;
            font-weight: 700;
        }
        .banner-active    { background: #f0fdf4; border: 1px solid #bbf7d0; border-top: none; color: #15803d; }
        .banner-expired   { background: #fff7ed; border: 1px solid #fed7aa; border-top: none; color: #c2410c; }
        .banner-cancelled { background: #fef2f2; border: 1px solid #fecaca; border-top: none; color: #b91c1c; }

        /* ── Body ── */
        .page-body { padding: 16px 32px 12px; }

        /* ── Section ── */
        .section { margin-bottom: 14px; }
        .section-title {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: #ff0808;
            border-bottom: 1px solid #fee2e2;
            padding-bottom: 4px; margin-bottom: 9px;
        }

        /* ── Info grid ── */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { width: 33%; padding: 4px 6px 4px 0; vertical-align: top; }
        .info-label { font-size: 8px; font-weight: 700; text-transform: uppercase;
                      letter-spacing: 0.8px; color: #94a3b8; display: block; margin-bottom: 2px; }
        .info-val   { font-size: 10.5px; font-weight: 600; color: #1e293b; }

        /* ── Status badge ── */
        .badge {
            display: inline-block; padding: 2px 9px; border-radius: 20px;
            font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .b-active    { background: #dcfce7; color: #15803d; }
        .b-expired   { background: #ffedd5; color: #c2410c; }
        .b-cancelled { background: #fee2e2; color: #b91c1c; }

        /* ── Summary cards ── */
        .cards-table { width: 100%; border-collapse: separate; border-spacing: 7px 0; margin-bottom: 14px; }
        .card-cell { vertical-align: top; }
        .card-inner {
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 10px 12px;
            text-align: center;
        }
        .card-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px;
                      color: #94a3b8; font-weight: 700; }
        .card-value { font-size: 17px; font-weight: 900; color: #1a2942; margin-top: 3px; }
        .card-value.red { color: #ff0808; }

        /* ── Progress bar ── */
        .progress-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 11px 14px;
            margin-bottom: 14px;
        }
        .progress-labels {
            display: table; width: 100%;
            font-size: 8px; color: #64748b; margin-bottom: 6px;
        }
        .progress-labels .pl-left  { display: table-cell; text-align: left; }
        .progress-labels .pl-mid   { display: table-cell; text-align: center; font-weight: 700; color: #1a2942; }
        .progress-labels .pl-right { display: table-cell; text-align: right; }
        .progress-bar-bg {
            background: #e5e7eb; border-radius: 10px; height: 7px; width: 100%;
        }
        .progress-bar-fill {
            background: linear-gradient(90deg, #1a2942 0%, #ff0808 100%);
            border-radius: 10px; height: 7px;
        }

        /* ── Features grid ── */
        .features-table { width: 100%; border-collapse: collapse; }
        .feature-td { width: 50%; padding: 0 5px 7px 0; vertical-align: top; }
        .feature-td:nth-child(2) { padding-right: 0; padding-left: 5px; }
        .feature-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #ff0808;
            border-radius: 5px;
            padding: 8px 10px;
        }
        .feature-dot {
            display: inline-block;
            width: 7px; height: 7px;
            background: #ff0808;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        .feature-dot.green { background: #16a34a; }
        .feature-dot.gray  { background: #94a3b8; }
        .feature-key   { font-weight: 700; color: #1a2942; font-size: 10px; }
        .feature-value { font-size: 10px; font-weight: 700; margin-left: 4px; }
        .fv-true  { color: #16a34a; }
        .fv-false { color: #dc2626; }
        .fv-other { color: #ff0808; }

        /* ── Signature ── */
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sig-table td { width: 33%; text-align: center; padding: 0 16px; }
        .sig-line  { border-bottom: 1px solid #94a3b8; margin-bottom: 5px; height: 28px; }
        .sig-label { font-size: 8px; color: #94a3b8; font-weight: 600;
                     text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

@php
    $plan       = $subscription->plan;
    $features   = $plan->features ?? collect();
    $totalDays  = $subscription->starts_at->diffInDays($subscription->ends_at);
    $daysUsed   = max(0, $subscription->starts_at->diffInDays(now()));
    $daysLeft   = max(0, (int) now()->diffInDays($subscription->ends_at, false));
    $percentage = $totalDays > 0 ? min(round(($daysUsed / $totalDays) * 100), 100) : 100;

    $maxProducts = $plan->getFeature('max_products', '—');
    $maxMessages = $plan->getFeature('max_messages', '—');

    $bannerClass = match($subscription->status) {
        'active'    => 'banner-active',
        'expired'   => 'banner-expired',
        'cancelled' => 'banner-cancelled',
        default     => 'banner-active',
    };
    $badgeClass = match($subscription->status) {
        'active'    => 'b-active',
        'expired'   => 'b-expired',
        'cancelled' => 'b-cancelled',
        default     => 'b-active',
    };

    function fmtKey(string $key): string {
        return ucwords(str_replace(['has_', 'can_', 'is_', '_'], ['', '', '', ' '], $key));
    }
    function fmtVal(string $val): array {
        $lower = strtolower(trim($val));
        if ($lower === 'true'  || $lower === '1') return ['Yes', 'fv-true',  'green'];
        if ($lower === 'false' || $lower === '0') return ['No',  'fv-false', 'gray'];
        return [$val, 'fv-other', ''];
    }

    $featArr = $features->values()->all();
    $pairs   = array_chunk($featArr, 2);
@endphp

<div class="plan-page">

    <div class="watermark">{{ strtoupper($plan->name) }}</div>

    <div class="page-inner">

        <div class="accent-bar"></div>

        {{-- Header --}}
        <div class="page-header">
            <div class="header-left">
                <div class="org-name">Trade<span>Hub</span></div>
                <div class="org-sub">Marketplace Platform &nbsp;&middot;&nbsp; Subscription Plan Document &nbsp;&middot;&nbsp; CONFIDENTIAL</div>
            </div>
            <div class="header-right">
                <div class="doc-label">Subscription ID</div>
                <div class="plan-number">SUB-{{ str_pad($subscription->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="header-date">Subscribed: {{ $subscription->starts_at->format('d M Y \a\t H:i') }}</div>
            </div>
        </div>

        {{-- Status banner --}}
        <div class="status-banner {{ $bannerClass }}">
            {{ strtoupper($plan->name) }} &nbsp;&middot;&nbsp;
            <span class="badge {{ $badgeClass }}">{{ ucfirst($subscription->status) }}</span>
            &nbsp;&middot;&nbsp; Expires: {{ $subscription->ends_at->format('d M Y') }}
            &nbsp;&middot;&nbsp; {{ $daysLeft }} days remaining
            @if($subscription->auto_renew) &nbsp;&middot;&nbsp; Auto-Renew: ON @endif
        </div>

        <div class="page-body">

            {{-- Summary cards --}}
            <div class="section">
                <table class="cards-table">
                    <tr>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Plan Price</div>
                                <div class="card-value red">${{ number_format($plan->price, 2) }}</div>
                            </div>
                        </td>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Duration</div>
                                <div class="card-value">{{ $plan->duration_days }}d</div>
                            </div>
                        </td>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Days Left</div>
                                <div class="card-value {{ $daysLeft <= 7 ? 'red' : '' }}">{{ $daysLeft }}</div>
                            </div>
                        </td>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Max Products</div>
                                <div class="card-value">{{ $maxProducts }}</div>
                            </div>
                        </td>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Max Messages</div>
                                <div class="card-value">{{ $maxMessages }}</div>
                            </div>
                        </td>
                        <td class="card-cell">
                            <div class="card-inner">
                                <div class="card-label">Features</div>
                                <div class="card-value">{{ $features->count() }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Plan Details --}}
            <div class="section">
                <div class="section-title">Plan Details</div>
                <table class="info-table">
                    <tr>
                        <td>
                            <span class="info-label">Plan Name</span>
                            <span class="info-val">{{ $plan->name }}</span>
                        </td>
                        <td>
                            <span class="info-label">Plan Slug</span>
                            <span class="info-val">{{ $plan->slug }}</span>
                        </td>
                        <td>
                            <span class="info-label">Status</span>
                            <span class="info-val">
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($subscription->status) }}</span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="info-label">Start Date</span>
                            <span class="info-val">{{ $subscription->starts_at->format('d M Y') }}</span>
                        </td>
                        <td>
                            <span class="info-label">End Date</span>
                            <span class="info-val">{{ $subscription->ends_at->format('d M Y') }}</span>
                        </td>
                        <td>
                            <span class="info-label">Auto-Renew</span>
                            <span class="info-val">{{ $subscription->auto_renew ? 'Enabled' : 'Disabled' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Progress --}}
            <div class="progress-section">
                <div class="progress-labels">
                    <span class="pl-left">{{ $subscription->starts_at->format('d M Y') }}</span>
                    <span class="pl-mid">{{ $percentage }}% used &nbsp;&middot;&nbsp; {{ $daysLeft }} days left</span>
                    <span class="pl-right">{{ $subscription->ends_at->format('d M Y') }}</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ $percentage }}%;"></div>
                </div>
            </div>

            {{-- Features --}}
            <div class="section">
                <div class="section-title">Features Included in This Plan</div>
                @if($features->count())
                    <table class="features-table">
                        @foreach($pairs as $pair)
                            <tr>
                                @foreach($pair as $feature)
                                    @php [$val, $cls, $dot] = fmtVal($feature->feature_value); @endphp
                                    <td class="feature-td">
                                        <div class="feature-item">
                                            <span class="feature-dot {{ $dot }}"></span>
                                            <span class="feature-key">{{ $feature->feature?->name ?? fmtKey((string) ($feature->feature_key ?? '')) }}:</span>
                                            <span class="feature-value {{ $cls }}">{{ $val }}</span>
                                        </div>
                                    </td>
                                @endforeach
                                @if(count($pair) === 1)
                                    <td class="feature-td"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p style="color:#9ca3af; font-size:10px; text-align:center; padding:16px 0;">No features listed for this plan.</p>
                @endif
            </div>

            {{-- Signatures --}}
            <table class="sig-table">
                <tr>
                    <td>
                        <div class="sig-line"></div>
                        <div class="sig-label">Vendor Signature</div>
                    </td>
                    <td>
                        <div class="sig-line"></div>
                        <div class="sig-label">TradeHub Officer</div>
                    </td>
                    <td>
                        <div class="sig-line"></div>
                        <div class="sig-label">Date</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

    {{-- Footer --}}
    <div class="page-footer">
        <div class="footer-left">Generated: {{ now()->format('d M Y \a\t H:i') }} &nbsp;&middot;&nbsp; TradeHub System</div>
        <div class="footer-center">CONFIDENTIAL &mdash; FOR OFFICIAL USE ONLY</div>
        <div class="footer-right">SUB-{{ str_pad($subscription->id, 5, '0', STR_PAD_LEFT) }} &nbsp;&middot;&nbsp; {{ $plan->name }}</div>
    </div>

</div>

</body>
</html>
