@php
    $trackingStages = \App\Models\VehicleTrackingEvent::STAGES;
@endphp
<section class="vehicle-progress" aria-label="Vehicle progress">
    @foreach($trackingCars as $car)
        @php
            $latestProgress = $car['events']->first();
            $currentStage = $latestProgress->stage ?? 'not_started';
            $currentPosition = array_search($currentStage, array_keys($trackingStages), true);
            $formKey = $trackingInvoice . '-' . ($car['key'] ?? $loop->index);
            $isSubmittedCar = old('invoice_id') === (string) $trackingInvoice && old('vehicle_key') === $car['key'];
        @endphp
        <div class="vehicle-progress__car">
            <h4>Vehicle progress — {{ $car['label'] }}</h4>
            @if($car['chassis'])<p>Chassis: {{ $car['chassis'] }}</p>@endif
            <p><strong>{{ $trackingStages[$currentStage] }}</strong>@if($latestProgress) · {{ $latestProgress->occurred_on->format('d M Y') }}@endif</p>
            <ol class="vehicle-progress__line">
                @foreach($trackingStages as $stage => $label)
                    @if($stage !== 'not_started')
                        <li class="{{ $loop->index < $currentPosition ? 'is-complete' : ($stage === $currentStage ? 'is-current' : '') }}" @if($stage === $currentStage) aria-current="step" @endif>
                            <span class="vehicle-progress__dot">{{ $loop->index < $currentPosition ? '✓' : $loop->index }}</span>
                            <span>{{ $label }}</span>
                        </li>
                    @endif
                @endforeach
            </ol>
            @if($latestProgress && $latestProgress->note)<p class="vehicle-progress__note">{{ $latestProgress->note }}</p>@endif
            @if($isAdmin && !empty($crm['trackingUrl']))
                @if($car['key'])
                    <details class="vehicle-progress__edit" @if($isSubmittedCar && $errors->any()) open @endif>
                        <summary>Update Progress</summary>
                        <form method="POST" action="{{ $crm['trackingUrl'] }}">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $trackingInvoice }}">
                            <input type="hidden" name="vehicle_key" value="{{ $car['key'] }}">
                            @if($isSubmittedCar && $errors->any())
                                <div role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                            @endif
                            <label for="stage-{{ $formKey }}">Stage</label>
                            <select id="stage-{{ $formKey }}" name="stage" required>
                                @foreach($trackingStages as $stage => $label)
                                    <option value="{{ $stage }}" @selected(($isSubmittedCar ? old('stage', $currentStage) : $currentStage) === $stage)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <label for="date-{{ $formKey }}">Date</label>
                            <input id="date-{{ $formKey }}" type="date" name="occurred_on" max="{{ now()->toDateString() }}" value="{{ $isSubmittedCar ? old('occurred_on', now()->toDateString()) : now()->toDateString() }}">
                            <label for="note-{{ $formKey }}">Note (visible to the customer)</label>
                            <textarea id="note-{{ $formKey }}" name="note" rows="2" maxlength="2000">{{ $isSubmittedCar ? old('note') : '' }}</textarea>
                            <p>You can move forward, correct an earlier stage, or reset to Not started. Previous updates remain in the history.</p>
                            <button class="crm-btn" type="submit">Save Progress</button>
                        </form>
                    </details>
                @else
                    <p>A unique vehicle ID or chassis number is needed in the CRM before this car can be tracked.</p>
                @endif
            @endif
            @if($car['events']->isNotEmpty())
                <details class="vehicle-progress__history">
                    <summary>Progress history</summary>
                    <ul>
                        @foreach($car['events'] as $event)
                            <li><strong>{{ $trackingStages[$event->stage] }}</strong> · {{ $event->occurred_on->format('d M Y') }}
                                @if($isAdmin)<small> — {{ $event->admin_name }} · recorded {{ $event->created_at->format('d M Y H:i') }}</small>@endif
                                @if($event->note)<p>{{ $event->note }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    @endforeach
</section>
@once
<style>
.vehicle-progress{margin:14px 0;color:#252935}.vehicle-progress__car{padding:20px;margin-bottom:14px;border:1px solid #e2e5ea;border-radius:12px;background:#fff}.vehicle-progress h4{margin:0 0 8px;font-size:16px}.vehicle-progress p{font-size:13px;overflow-wrap:anywhere}.vehicle-progress__line{display:flex;list-style:none;margin:22px 0;padding:0;overflow-x:auto;gap:0}.vehicle-progress__line li{flex:1;min-width:110px;position:relative;text-align:center;font-size:12px;color:#b91520;padding:0 8px 8px}.vehicle-progress__line li:before{content:'';position:absolute;top:15px;left:0;right:0;height:3px;background:#b91520}.vehicle-progress__line li:first-child:before{left:50%}.vehicle-progress__line li:last-child:before{right:50%}.vehicle-progress__dot{position:relative;display:flex;align-items:center;justify-content:center;width:32px;height:32px;margin:0 auto 8px;border-radius:50%;background:#b91520;color:white;font-weight:700}.vehicle-progress__line .is-complete{color:#17734b}.vehicle-progress__line .is-complete:before,.vehicle-progress__line .is-complete .vehicle-progress__dot{background:#17734b;color:white}.vehicle-progress__line .is-current{color:#17734b;font-weight:700}.vehicle-progress__line .is-current:before{background:#17734b}.vehicle-progress__line .is-current .vehicle-progress__dot{background:#17734b;color:white;box-shadow:0 0 0 4px #e0f2e8}.vehicle-progress summary{cursor:pointer;font-weight:600;padding:8px 0}.vehicle-progress__edit form{display:grid;gap:8px;max-width:600px;padding:12px 0}.vehicle-progress__edit label{font-size:13px;margin:0}.vehicle-progress__edit input,.vehicle-progress__edit select,.vehicle-progress__edit textarea{width:100%;padding:10px;border:1px solid #cbd0d8;border-radius:6px;background:white;color:#252935}.vehicle-progress__edit button{justify-self:start}.vehicle-progress__history li{margin:10px 0;font-size:13px}.vehicle-progress__history p,.vehicle-progress__note{white-space:pre-wrap}.vehicle-progress [role=alert]{color:#b91520}
</style>
@endonce
