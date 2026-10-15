<style>
.sbk-home-advanced{grid-column:1/-1;border-top:1px solid #eef0f3;padding-top:16px}.sbk-home-advanced summary{display:flex;align-items:center;justify-content:space-between;gap:12px;width:fit-content;color:#d71920;font-family:Montserrat,Arial,sans-serif!important;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;list-style:none;padding:6px 0}.sbk-home-advanced summary::-webkit-details-marker{display:none}.sbk-home-advanced summary::after{content:'+';font-size:20px;line-height:1}.sbk-home-advanced[open] summary::after{content:'−'}.sbk-home-advanced summary:focus-visible{outline:2px solid #d71920;outline-offset:4px}.sbk-home-advanced__intro{color:#6b717b;font-size:13px;line-height:1.6;margin:12px 0 18px}.sbk-home-advanced__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.sbk-home-advanced input{width:100%;height:50px;border:1px solid #dfe2e7;border-radius:8px;background:#fafbfc;padding:0 14px;font:inherit;font-size:13px}.sbk-home-advanced input:focus{outline:none;border-color:#d71920;box-shadow:0 0 0 4px rgba(215,25,32,.08)}.sbk-home-advanced__actions{display:flex;justify-content:flex-end;margin-top:22px}@media(max-width:980px){.sbk-home-advanced__grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.sbk-home-advanced__grid{grid-template-columns:1fr}.sbk-home-advanced__actions .sbk-home-submit{width:100%}}
</style>
<details class="sbk-home-advanced" id="homeAdvancedSearch">
    <summary>Advanced Search</summary>
    <p class="sbk-home-advanced__intro">Choose additional specifications. Your selections above will also apply.</p>
    <div class="sbk-home-advanced__grid">
        <div class="sbk-home-field">
            <label for="homeSteering">Steering</label>
            <div class="sbk-home-control"><select id="homeSteering" name="steering">
                <option value="">Any Steering</option>
                <option value="LHD">Left Hand Drive (LHD)</option>
                <option value="RHD">Right Hand Drive (RHD)</option>
            </select></div>
        </div>
        @foreach(['types' => ['Body Type', $homeAdvancedTypes], 'grades' => ['Grade', $homeAdvancedGrades]] as $parameter => [$label, $values])
            <div class="sbk-home-field">
                <label for="homeAdvanced-{{ $parameter }}">{{ $label }}</label>
                <div class="sbk-home-control"><select id="homeAdvanced-{{ $parameter }}" name="{{ $parameter }}[]">
                    <option value="">Any {{ $label }}</option>
                    @foreach($values as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach
                </select></div>
            </div>
        @endforeach
        @foreach($homeAdvancedOptions['fields'] as $parameter => $field)
            <div class="sbk-home-field">
                <label for="homeAdvanced-{{ $parameter }}">{{ $field['label'] }}</label>
                @if(in_array($parameter, ['ref_no', 'stock_no', 'chassis_no'], true))
                    <input id="homeAdvanced-{{ $parameter }}" name="{{ $parameter }}" type="text" placeholder="Any {{ $field['label'] }}" maxlength="255">
                @else
                    <div class="sbk-home-control"><select id="homeAdvanced-{{ $parameter }}" name="{{ $parameter }}">
                        <option value="">Any {{ $field['label'] }}</option>
                        @foreach($field['values'] as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach
                    </select></div>
                @endif
            </div>
        @endforeach
        @foreach($homeAdvancedOptions['specifications'] as $key => $field)
            <div class="sbk-home-field">
                <label for="homeSpec-{{ $key }}">{{ $field['label'] }}</label>
                <div class="sbk-home-control"><select id="homeSpec-{{ $key }}" name="specs[{{ $key }}]">
                    <option value="">Any {{ $field['label'] }}</option>
                    @foreach($field['values'] as $value)<option value="{{ $value }}">{{ $value }}</option>@endforeach
                </select></div>
            </div>
        @endforeach
    </div>
    <div class="sbk-home-advanced__actions"><button class="sbk-home-submit" type="submit">Search Vehicles</button></div>
</details>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('homeAdvancedSearch');
    const form = document.getElementById('sbkHomeFilter');
    if (!panel || !form) return;
    form.addEventListener('formdata', function (event) {
        panel.querySelectorAll('select[name], input[name]').forEach(function (field) {
            if (!field.value.trim()) event.formData.delete(field.name);
        });
    });
});
</script>
