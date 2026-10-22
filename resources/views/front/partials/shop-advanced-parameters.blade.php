@foreach(($advancedSearchParameters ?? []) as $parameter => $value)
    @if($parameter === 'specs')
        @foreach($value as $key => $specValue)
            <input type="hidden" name="specs[{{ $key }}]" value="{{ $specValue }}">
        @endforeach
    @else
        <input type="hidden" name="{{ $parameter }}" value="{{ $value }}">
    @endif
@endforeach
