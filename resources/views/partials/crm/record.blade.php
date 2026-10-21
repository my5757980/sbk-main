{{--
    A customer's record from the SBK CRM: profile, totals and every invoice with
    its car(s), payments, photos, documents and invoice PDF.
    Used by the admin user page and the customer's "My Invoices" tab.

    $crm = [
        'record'      => CrmRecord::forAudience(...) | null,
        'unavailable' => bool,
        'fileUrl'     => fn (array $file, bool $download = false): string,
        'pdfUrl'      => fn (array $invoice, bool $download = false): string,
        'audience'    => 'admin' | 'customer',
    ]
--}}
@php
    $rec = $crm['record'] ?? null;
    $isAdmin = ($crm['audience'] ?? 'customer') === 'admin';
    $fileUrl = $crm['fileUrl'];
    $pdfUrl = $crm['pdfUrl'];
    $money = fn ($v) => '$' . number_format((float) $v, fmod((float) $v, 1) != 0 ? 2 : 0);
    $day = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d M Y') : '—';
    $size = fn ($b) => $b >= 1048576 ? number_format($b / 1048576, 1) . ' MB' : ($b >= 1024 ? number_format($b / 1024) . ' KB' : (int) $b . ' B');
    $statusText = $isAdmin
        ? ['pending' => 'Pending approval', 'approved' => 'Approved', 'sent' => 'Sent', 'rejected' => 'Rejected']
        : ['pending' => 'Processing', 'approved' => 'Confirmed', 'sent' => 'Confirmed', 'rejected' => 'Rejected'];
    $statusTone = ['pending' => 'amber', 'approved' => 'green', 'sent' => 'green', 'rejected' => 'red'];
    $crmUrl = config('services.crm.url');
@endphp

<div class="crm-rec">
    @if(!$rec)
        <div class="crm-rec__notice">
            @if($crm['unavailable'] ?? false)
                The CRM record could not be loaded right now. Please refresh in a minute.
            @else
                No CRM record was found.
            @endif
        </div>
    @elseif(empty($rec['invoices']))
        <div class="crm-rec__notice">There are no invoices in the CRM for this account yet.</div>
    @else
        @php
            $customer = $rec['customer'] ?? [];
            $totals = $rec['totals'];
            $paidPct = $totals['total'] > 0 ? min(100, round($totals['paid'] / $totals['total'] * 100)) : 0;
        @endphp

        {{-- Staff see whose record this is; the customer already sees their own account beside it. --}}
        <div class="crm-rec__summary {{ $isAdmin ? '' : 'crm-rec__summary--solo' }}">
            @if($isAdmin)
                <div class="crm-rec__who">
                    <span class="crm-rec__eyebrow">CRM customer</span>
                    <h3>{{ $customer['name'] ?? '' ?: 'SBK Customer' }}</h3>
                    <ul>
                        @if(!empty($customer['contactPerson']))<li><b>Contact</b>{{ $customer['contactPerson'] }}</li>@endif
                        <li><b>Email</b>{{ ($customer['email'] ?? '') ?: 'Not in the CRM' }}</li>
                        @if(!empty($customer['phone']))<li><b>Phone</b>{{ $customer['phone'] }}</li>@endif
                        @if(!empty($customer['country']))<li><b>Country</b>{{ $customer['country'] }}{{ !empty($customer['port']) ? ' / ' . $customer['port'] : '' }}</li>@endif
                        @if(!empty($customer['address']))<li><b>Address</b>{{ $customer['address'] }}</li>@endif
                    </ul>
                </div>
            @endif
            <div class="crm-rec__tiles">
                <div class="crm-tile"><small>Invoices</small><strong>{{ $totals['invoices'] }}</strong></div>
                <div class="crm-tile"><small>Total</small><strong>{{ $money($totals['total']) }}</strong></div>
                <div class="crm-tile crm-tile--good"><small>Paid</small><strong>{{ $money($totals['paid']) }}</strong></div>
                <div class="crm-tile {{ $totals['balance'] > 0 ? 'crm-tile--due' : 'crm-tile--good' }}"><small>Balance</small><strong>{{ $money($totals['balance']) }}</strong></div>
                <div class="crm-rec__progress" title="{{ $paidPct }}% paid"><span style="width: {{ $paidPct }}%"></span></div>
                <p class="crm-rec__progress-label">{{ $paidPct }}% paid &middot; {{ $totals['photos'] }} photos &middot; {{ $totals['documents'] }} documents</p>
            </div>
        </div>

        @foreach($rec['invoices'] as $index => $inv)
            @php
                $units = $inv['units'] ?? [];
                $photos = collect($units)->flatMap(fn ($u) => collect($u['files'] ?? [])->where('isImage', true))->values();
                $documents = collect($units)->flatMap(fn ($u) => collect($u['files'] ?? [])->where('isImage', false))->groupBy('folder');
                $invPaidPct = ($inv['cnfPrice'] ?? 0) > 0 ? min(100, round(($inv['paid'] ?? 0) / $inv['cnfPrice'] * 100)) : 0;
                $status = $inv['status'] ?? 'pending';
                $unit = $units[0] ?? null;
                $title = $inv['unit'] ?: trim(($unit['make'] ?? '') . ' ' . ($unit['model'] ?? '')) ?: 'Vehicle';
                $cover = $photos->first();
                $specs = array_filter([
                    'Make / Model' => $unit ? trim(($unit['make'] ?? '') . ' ' . ($unit['model'] ?? '')) : null,
                    'Year' => $unit['year'] ?? null ?: ($inv['year'] ?? null),
                    'Chassis' => $unit['chassis'] ?? null ?: ($inv['chassisNo'] ?? null),
                    'Engine No.' => $inv['engineNo'] ?? null,
                    'Engine' => !empty($unit['engineCC']) ? number_format($unit['engineCC']) . ' cc' : null,
                    'Color' => $unit['color'] ?? null ?: ($inv['color'] ?? null),
                    'Fuel' => $unit['fuel'] ?? null ?: ($inv['fuel'] ?? null),
                    'Transmission' => $unit['transmission'] ?? null ?: ($inv['transmission'] ?? null),
                    'Mileage' => !empty($unit['mileage']) ? number_format($unit['mileage']) . ' km' : null,
                    'Drive' => $unit['drive'] ?? null,
                    'Steering' => $unit['steering'] ?? null,
                    'Doors' => $unit['doors'] ?? null,
                    'Seats' => $unit['seats'] ?? null,
                    'Location' => $unit['location'] ?? null,
                ], fn ($v) => $v !== null && $v !== '');
                $consignee = array_filter($inv['consignee'] ?? []);
                // Several vehicles on one invoice (the fields above are vehicle 1).
                $vehicles = $inv['vehicles'] ?? [];
            @endphp

            <details class="crm-inv" {{ $index === 0 ? 'open' : '' }}>
                <summary class="crm-inv__bar">
                    @if($cover)
                        <img class="crm-inv__thumb" src="{{ $fileUrl($cover) }}" alt="" loading="lazy" onerror="this.style.visibility='hidden'">
                    @else
                        <span class="crm-inv__thumb crm-inv__thumb--empty" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="26" height="26"><path fill="currentColor" d="M5 11l1.5-4.5h11L19 11m-1.5 5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m-11 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3M18.92 6c-.2-.58-.76-1-1.42-1h-11c-.66 0-1.22.42-1.42 1L3 12v8a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1h12v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-8z"/></svg>
                        </span>
                    @endif
                    <span class="crm-inv__title">
                        <strong>{{ $title }}@if(count($vehicles) > 1) <span class="crm-count">+{{ count($vehicles) - 1 }} more</span>@endif</strong>
                        <small>Invoice {{ $inv['number'] }} &middot; {{ $day($inv['createdAt'] ?? null) }}{{ !empty($inv['chassisNo']) ? ' · ' . $inv['chassisNo'] : '' }}</small>
                    </span>
                    <span class="crm-badge crm-badge--{{ $statusTone[$status] ?? 'grey' }}">{{ $statusText[$status] ?? ucfirst($status) }}</span>
                    <span class="crm-inv__money">
                        <strong>{{ $money($inv['cnfPrice'] ?? 0) }}</strong>
                        <small class="{{ ($inv['balance'] ?? 0) > 0 ? 'crm-due' : 'crm-ok' }}">{{ ($inv['balance'] ?? 0) > 0 ? $money($inv['balance']) . ' due' : 'Fully paid' }}</small>
                    </span>
                    <span class="crm-inv__chevron" aria-hidden="true"></span>
                </summary>

                <div class="crm-inv__body">
                    {{-- Several vehicles: their list leads (it replaces the one-vehicle card). --}}
                    @if(count($vehicles) > 1)
                        <section class="crm-card crm-card--wide crm-card--first">
                            <h4>Vehicles on this invoice <span class="crm-count">{{ count($vehicles) }}</span></h4>
                            <ol class="crm-vehicles">
                                @foreach($vehicles as $n => $vehicle)
                                    <li>
                                        <span class="crm-vehicles__n">{{ $n + 1 }}</span>
                                        <span class="crm-vehicles__what">
                                            <strong>{{ ($vehicle['unit'] ?? '') ?: 'Vehicle ' . ($n + 1) }}</strong>
                                            <small>{{ implode(' · ', array_filter([$vehicle['year'] ?? '', $vehicle['color'] ?? '', $vehicle['chassisNo'] ?? ''])) ?: '—' }}</small>
                                        </span>
                                        <b class="crm-vehicles__price">{{ $money($vehicle['cnfPrice'] ?? 0) }}</b>
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    @endif

                    <div class="crm-grid {{ count($vehicles) > 1 ? 'crm-grid--two' : '' }}">
                        @if(count($vehicles) <= 1)
                            <section class="crm-card">
                                <h4>Vehicle</h4>
                                <dl class="crm-specs">
                                    @forelse($specs as $label => $value)
                                        <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                                    @empty
                                        <div><dt>Vehicle</dt><dd>{{ $title }}</dd></div>
                                    @endforelse
                                </dl>
                            </section>
                        @endif

                        <section class="crm-card">
                            <h4>Payment</h4>
                            <dl class="crm-price">
                                <div><dt>C&amp;F price</dt><dd>{{ $money($inv['cnfPrice'] ?? 0) }}</dd></div>
                                <div><dt>Advance ({{ (float) ($inv['advancePercent'] ?? 0) }}%)</dt><dd>{{ $money($inv['advanceAmount'] ?? 0) }}</dd></div>
                                <div><dt>Paid</dt><dd class="crm-ok">{{ $money($inv['paid'] ?? 0) }}</dd></div>
                                <div class="crm-price__total"><dt>Remaining</dt><dd class="{{ ($inv['balance'] ?? 0) > 0 ? 'crm-due' : 'crm-ok' }}">{{ $money($inv['balance'] ?? 0) }}</dd></div>
                            </dl>
                            <div class="crm-rec__progress crm-rec__progress--sm"><span style="width: {{ $invPaidPct }}%"></span></div>
                            <p class="crm-rec__progress-label">{{ $invPaidPct }}% paid{{ empty($inv['payments']) ? ' · no payment recorded yet' : '' }}</p>
                            @if(!empty($inv['pdf']))
                                <div class="crm-actions">
                                    <a class="crm-btn" href="{{ $pdfUrl($inv) }}" target="_blank" rel="noopener">View invoice PDF</a>
                                    <a class="crm-btn crm-btn--ghost" href="{{ $pdfUrl($inv, true) }}">Download</a>
                                </div>
                            @endif
                        </section>

                        <section class="crm-card">
                            <h4>Consignee</h4>
                            <dl class="crm-specs crm-specs--single">
                                @foreach(['name' => 'Name', 'address' => 'Address', 'port' => 'Port', 'country' => 'Country', 'phone' => 'Phone', 'email' => 'Email'] as $key => $label)
                                    @if(!empty($consignee[$key]))<div><dt>{{ $label }}</dt><dd>{{ $consignee[$key] }}</dd></div>@endif
                                @endforeach
                                @if(empty($consignee))<div><dt>Consignee</dt><dd>Not recorded</dd></div>@endif
                                @if($isAdmin && !empty($inv['salesperson']))<div><dt>Sales person</dt><dd>{{ $inv['salesperson'] }}</dd></div>@endif
                            </dl>
                            @if($isAdmin)
                                <div class="crm-actions">
                                    <a class="crm-btn crm-btn--ghost" href="{{ $crmUrl }}/invoices/{{ $inv['id'] }}" target="_blank" rel="noopener">Open in CRM</a>
                                </div>
                            @endif
                        </section>
                    </div>

                    {{-- Only what there is: an empty history, gallery or document list adds a box of nothing. --}}
                    @if(!empty($inv['payments']))
                    <section class="crm-card crm-card--wide">
                        <h4>Payment history <span class="crm-count">{{ count($inv['payments']) }}</span></h4>
                            <div class="crm-table-wrap">
                                <table class="crm-table">
                                    <thead><tr><th>#</th><th>Date</th><th class="crm-num">Amount</th><th class="crm-num">Balance after</th></tr></thead>
                                    <tbody>
                                        @foreach($inv['payments'] as $n => $payment)
                                            <tr>
                                                <td>{{ $n + 1 }}</td>
                                                <td>{{ $day($payment['date'] ?? null) }}</td>
                                                <td class="crm-num crm-ok">{{ $money($payment['amount'] ?? 0) }}</td>
                                                <td class="crm-num {{ ($payment['balanceAfter'] ?? 0) > 0 ? 'crm-due' : 'crm-ok' }}">{{ $money($payment['balanceAfter'] ?? 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </section>
                    @endif

                    @if($photos->isEmpty() && $documents->isEmpty())
                        <p class="crm-note">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            Photos and documents of this car will appear here once they are added.
                        </p>
                    @endif

                    @if($photos->isNotEmpty())
                    <section class="crm-card crm-card--wide">
                        <h4>Vehicle photos <span class="crm-count">{{ $photos->count() }}</span></h4>
                            <div class="crm-gallery" data-crm-gallery>
                                @foreach($photos as $photo)
                                    <a href="{{ $fileUrl($photo) }}" target="_blank" rel="noopener" data-crm-photo data-caption="{{ $photo['folder'] }} · {{ $photo['filename'] }}">
                                        <img src="{{ $fileUrl($photo) }}" alt="{{ $photo['filename'] }}" loading="lazy">
                                        <span>{{ $photo['folder'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                    </section>
                    @endif

                    @if($documents->isNotEmpty())
                    <section class="crm-card crm-card--wide">
                        <h4>Documents <span class="crm-count">{{ $documents->flatten(1)->count() }}</span></h4>
                            @foreach($documents as $folder => $files)
                                <div class="crm-docs">
                                    <h5>{{ $folder }}</h5>
                                    @foreach($files as $doc)
                                        <div class="crm-doc">
                                            <span class="crm-doc__icon" aria-hidden="true">{{ strtoupper(pathinfo($doc['filename'], PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                                            <span class="crm-doc__name"><strong>{{ $doc['filename'] }}</strong><small>{{ $size($doc['size'] ?? 0) }} &middot; {{ $day($doc['uploadedAt'] ?? null) }}</small></span>
                                            <a class="crm-btn crm-btn--ghost" href="{{ $fileUrl($doc) }}" target="_blank" rel="noopener">View</a>
                                            <a class="crm-btn" href="{{ $fileUrl($doc, true) }}">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                    </section>
                    @endif
                </div>
            </details>
        @endforeach
    @endif
</div>

@once
<div class="crm-lightbox" id="crmLightbox" hidden>
    <button type="button" class="crm-lightbox__btn crm-lightbox__close" aria-label="Close">&times;</button>
    <button type="button" class="crm-lightbox__btn crm-lightbox__prev" aria-label="Previous">&#8249;</button>
    <figure><img alt=""><figcaption></figcaption></figure>
    <button type="button" class="crm-lightbox__btn crm-lightbox__next" aria-label="Next">&#8250;</button>
</div>
<style>
.crm-rec{--crm-red:#d71920;--crm-ink:#15161a;--crm-muted:#6b7079;--crm-line:#e7e9ee;--crm-soft:#f6f7f9;--crm-green:#12823b;--crm-amber:#a15c00;color:var(--crm-ink);font-size:14px;line-height:1.5}
.crm-rec *{box-sizing:border-box}
.crm-rec h3,.crm-rec h4,.crm-rec h5{margin:0;color:var(--crm-ink)}
.crm-rec__notice{padding:22px;border:1px dashed var(--crm-line);border-radius:10px;background:var(--crm-soft);color:var(--crm-muted)}
.crm-rec__summary{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);gap:18px;margin-bottom:18px}
.crm-rec__who{padding:20px 22px;border-radius:12px;background:linear-gradient(135deg,#121317 0%,#26282e 70%,#3a1416 100%);color:#fff}
.crm-rec__who h3{color:#fff;font-size:22px;margin:6px 0 12px}
.crm-rec__eyebrow{color:#ff5a61;font-size:11px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}
.crm-rec__who ul{list-style:none;margin:0;padding:0;display:grid;gap:6px}
.crm-rec__who li{display:grid;grid-template-columns:78px minmax(0,1fr);gap:10px;color:rgba(255,255,255,.88);overflow-wrap:anywhere}
.crm-rec__who li b{color:rgba(255,255,255,.55);font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;padding-top:2px}
.crm-rec__tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;align-content:start}
.crm-tile{padding:14px 16px;border:1px solid var(--crm-line);border-radius:10px;background:#fff}
.crm-tile small{display:block;color:var(--crm-muted);font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase}
.crm-tile strong{display:block;margin-top:4px;font-size:22px;font-weight:800}
.crm-tile--good strong{color:var(--crm-green)}.crm-tile--due strong{color:var(--crm-red)}
.crm-rec__progress{grid-column:1/-1;height:9px;border-radius:99px;background:#eceef2;overflow:hidden}
.crm-rec__progress span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#12823b,#22b35a)}
.crm-rec__progress--sm{height:7px;margin-top:12px}
.crm-rec__progress-label{grid-column:1/-1;margin:4px 0 0;color:var(--crm-muted);font-size:12px}
.crm-inv{margin-bottom:14px;border:1px solid var(--crm-line);border-radius:12px;background:#fff;box-shadow:0 10px 30px rgba(17,18,20,.05);overflow:hidden}
.crm-inv[open]{box-shadow:0 16px 40px rgba(17,18,20,.08)}
.crm-inv__bar{display:grid;grid-template-columns:64px minmax(0,1fr) auto auto 18px;gap:14px;align-items:center;padding:14px 18px;cursor:pointer;list-style:none;background:#fff}
.crm-inv__bar::-webkit-details-marker{display:none}
.crm-inv__bar:hover{background:#fbfbfc}
.crm-inv__thumb{width:64px;height:48px;border-radius:8px;object-fit:cover;background:var(--crm-soft)}
.crm-inv__thumb--empty{display:flex;align-items:center;justify-content:center;color:#b3b6bd}
.crm-inv__title{min-width:0}.crm-inv__title strong{display:block;font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.crm-inv__title small{display:block;color:var(--crm-muted);font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.crm-inv__money{text-align:right}.crm-inv__money strong{display:block;font-size:16px}.crm-inv__money small{font-size:12px;font-weight:700}
.crm-inv__chevron{width:10px;height:10px;border-right:2px solid #9aa0a8;border-bottom:2px solid #9aa0a8;transform:rotate(45deg);transition:transform .2s}
.crm-inv[open] .crm-inv__chevron{transform:rotate(-135deg)}
.crm-inv__body{padding:4px 18px 18px;border-top:1px solid var(--crm-line);background:#fcfcfd}
.crm-badge{display:inline-block;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:800;letter-spacing:.3px;white-space:nowrap}
.crm-badge--green{background:#e6f6ec;color:var(--crm-green)}.crm-badge--amber{background:#fff4e0;color:var(--crm-amber)}.crm-badge--red{background:#ffe9ea;color:#b3121a}.crm-badge--grey{background:#eef0f3;color:#555}
.crm-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:14px}
.crm-card{padding:16px 18px;border:1px solid var(--crm-line);border-radius:10px;background:#fff}
.crm-card--wide{margin-top:14px}
.crm-card h4{display:flex;align-items:center;gap:8px;margin-bottom:12px;font-size:13px;font-weight:800;letter-spacing:.5px;text-transform:uppercase}
.crm-card h4:before{content:"";width:4px;height:14px;border-radius:2px;background:var(--crm-red)}
.crm-count{padding:1px 8px;border-radius:99px;background:var(--crm-soft);color:var(--crm-muted);font-size:11px;white-space:nowrap}
.crm-specs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin:0}
.crm-specs--single{grid-template-columns:1fr}
.crm-specs div{padding:8px 10px;border-radius:7px;background:var(--crm-soft);min-width:0}
.crm-specs dt{color:var(--crm-muted);font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase}
.crm-specs dd{margin:2px 0 0;font-weight:600;overflow-wrap:anywhere}
.crm-price{margin:0;display:grid;gap:6px}
.crm-price div{display:flex;justify-content:space-between;gap:12px;padding:6px 0;border-bottom:1px dashed var(--crm-line)}
.crm-price dt{color:var(--crm-muted)}.crm-price dd{margin:0;font-weight:700}
.crm-price__total{border-bottom:0!important;font-size:16px}
.crm-ok{color:var(--crm-green)!important}.crm-due{color:var(--crm-red)!important}.crm-muted{margin:0;color:var(--crm-muted)}
.crm-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
.crm-btn{display:inline-flex;align-items:center;justify-content:center;min-height:34px;padding:0 14px;border:1px solid var(--crm-red);border-radius:7px;background:var(--crm-red);color:#fff!important;font-size:12px;font-weight:800;text-decoration:none!important;white-space:nowrap}
.crm-btn:hover{background:#b3121a;border-color:#b3121a}
.crm-btn--ghost{background:#fff;color:var(--crm-ink)!important;border-color:var(--crm-line)}
.crm-btn--ghost:hover{background:var(--crm-soft);border-color:#cfd3da}
.crm-table-wrap{overflow-x:auto}
.crm-table{width:100%;border-collapse:collapse}
.crm-table th{padding:9px 12px;background:var(--crm-soft);color:var(--crm-muted);font-size:11px;font-weight:800;letter-spacing:.5px;text-align:left;text-transform:uppercase}
.crm-table td{padding:10px 12px;border-bottom:1px solid var(--crm-line)}
.crm-table tbody tr:last-child td{border-bottom:0}
.crm-num{text-align:right!important;white-space:nowrap;font-weight:700}
.crm-rec__summary--solo{grid-template-columns:1fr}
.crm-rec__summary--solo .crm-rec__tiles{grid-template-columns:repeat(4,minmax(0,1fr))}
.crm-grid--two{grid-template-columns:repeat(2,minmax(0,1fr))}
.crm-note{display:flex;align-items:center;gap:9px;margin:14px 0 0;padding:12px 14px;border:1px dashed #d9dce2;border-radius:10px;background:#fff;color:var(--crm-muted);font-size:13px}
.crm-note svg{flex:0 0 auto;color:#b3b8c1}
.crm-vehicles{list-style:none;margin:0;padding:0;display:grid;gap:8px}
.crm-vehicles li{display:grid;grid-template-columns:28px minmax(0,1fr) auto;gap:12px;align-items:center;padding:10px 12px;border-radius:8px;background:var(--crm-soft)}
.crm-vehicles__n{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:#fff;color:var(--crm-red);font-size:12px;font-weight:800}
.crm-vehicles__what{min-width:0}.crm-vehicles__what strong{display:block;overflow-wrap:anywhere}
.crm-vehicles__what small{display:block;color:var(--crm-muted);font-size:12px;overflow-wrap:anywhere}
.crm-vehicles__price{white-space:nowrap;font-weight:800}
.crm-gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.crm-gallery a{position:relative;display:block;aspect-ratio:4/3;border-radius:9px;overflow:hidden;background:var(--crm-soft)}
.crm-gallery img{width:100%;height:100%;object-fit:cover;transition:transform .25s}
.crm-gallery a:hover img{transform:scale(1.05)}
.crm-gallery span{position:absolute;left:6px;bottom:6px;max-width:calc(100% - 12px);padding:2px 8px;border-radius:99px;background:rgba(17,18,22,.7);color:#fff;font-size:10px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.crm-docs+.crm-docs{margin-top:14px}
.crm-docs h5{margin-bottom:8px;color:var(--crm-muted);font-size:12px;font-weight:800;letter-spacing:.4px;text-transform:uppercase}
.crm-doc{display:grid;grid-template-columns:44px minmax(0,1fr) auto auto;gap:10px;align-items:center;padding:10px;border:1px solid var(--crm-line);border-radius:9px;background:#fff}
.crm-doc+.crm-doc{margin-top:8px}
.crm-doc__icon{display:flex;align-items:center;justify-content:center;height:44px;border-radius:8px;background:#fff0f1;color:var(--crm-red);font-size:10px;font-weight:900}
.crm-doc__name{min-width:0}.crm-doc__name strong{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.crm-doc__name small{color:var(--crm-muted);font-size:12px}
.crm-lightbox{position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;background:rgba(8,9,11,.92)}
.crm-lightbox[hidden]{display:none}
.crm-lightbox figure{margin:0;max-width:min(1100px,92vw);text-align:center}
.crm-lightbox img{max-width:100%;max-height:82vh;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
.crm-lightbox figcaption{margin-top:10px;color:rgba(255,255,255,.8);font-size:13px}
.crm-lightbox__btn{position:absolute;display:flex;align-items:center;justify-content:center;width:48px;height:48px;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:30px;line-height:1;cursor:pointer}
.crm-lightbox__btn:hover{background:#d71920}
.crm-lightbox__close{top:18px;right:18px}.crm-lightbox__prev{left:18px}.crm-lightbox__next{right:18px}
@media (max-width:1100px){.crm-grid{grid-template-columns:1fr 1fr}.crm-grid .crm-card:last-child{grid-column:1/-1}.crm-grid--two .crm-card:last-child{grid-column:auto}}
@media (max-width:767px){
 .crm-rec__summary{grid-template-columns:1fr}
 .crm-rec__summary--solo .crm-rec__tiles{grid-template-columns:1fr 1fr}
 .crm-grid,.crm-grid--two{grid-template-columns:1fr}
 .crm-tile{padding:12px 14px}
 .crm-tile strong{font-size:19px}
 .crm-inv__bar{grid-template-columns:52px minmax(0,1fr) 18px;gap:10px;padding:12px}
 .crm-inv__thumb{width:52px;height:40px}
 /* phones: the car, "+2 more", invoice number and date wrap instead of being cut short */
 .crm-inv__title strong,.crm-inv__title small{white-space:normal}
 .crm-inv__bar .crm-badge,.crm-inv__money{grid-column:2/3}
 .crm-inv__bar .crm-badge{justify-self:start}
 .crm-inv__money{text-align:left;display:flex;gap:10px;align-items:baseline}
 .crm-inv__chevron{grid-row:1;grid-column:3}
 .crm-inv__body{padding:4px 12px 12px}
 .crm-specs{grid-template-columns:1fr 1fr}
 .crm-gallery{grid-template-columns:repeat(2,minmax(0,1fr))}
 .crm-doc{grid-template-columns:40px minmax(0,1fr);}
 .crm-doc .crm-btn{grid-column:auto}
 .crm-doc .crm-btn--ghost{grid-column:1/2}
 .crm-lightbox__prev{left:6px}.crm-lightbox__next{right:6px}
}
</style>
<script>
(function () {
    var box = document.getElementById('crmLightbox');
    if (!box) return;
    var img = box.querySelector('img'), cap = box.querySelector('figcaption'), items = [], at = 0;
    function show(i) {
        at = (i + items.length) % items.length;
        img.src = items[at].href;
        cap.textContent = items[at].getAttribute('data-caption') || '';
    }
    document.addEventListener('click', function (e) {
        var link = e.target.closest && e.target.closest('[data-crm-photo]');
        if (!link) return;
        e.preventDefault();
        items = Array.prototype.slice.call(link.closest('[data-crm-gallery]').querySelectorAll('[data-crm-photo]'));
        box.hidden = false;
        document.body.style.overflow = 'hidden';
        show(items.indexOf(link));
    });
    function close() { box.hidden = true; img.src = ''; document.body.style.overflow = ''; }
    box.querySelector('.crm-lightbox__close').addEventListener('click', close);
    box.querySelector('.crm-lightbox__prev').addEventListener('click', function () { show(at - 1); });
    box.querySelector('.crm-lightbox__next').addEventListener('click', function () { show(at + 1); });
    box.addEventListener('click', function (e) { if (e.target === box) close(); });
    document.addEventListener('keydown', function (e) {
        if (box.hidden) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(at - 1);
        if (e.key === 'ArrowRight') show(at + 1);
    });
})();
</script>
@endonce
