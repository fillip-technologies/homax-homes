{{-- Optional place name plus a numeric distance with an m / km toggle.
     The distance submits a normalised string such as "2 km" under $name; the name submits under
     place_names[<key>] (key = $name without "_distance_km"). --}}
@php
    $placeKey = \Illuminate\Support\Str::before($name, '_distance_km');
    $placeName = old('place_names.' . $placeKey, isset($property) ? ($property->place_names[$placeKey] ?? '') : '');
    $placeExamples = [
        'bazar' => 'Kharghar Metro Station',
        'hospital' => 'Apollo Hospital',
        'school' => 'DAV Public School',
        'bus_stand' => 'City Bus Depot',
        'junction' => 'Kharghar Railway Station',
        'airport' => 'Navi Mumbai Airport',
    ];
@endphp
{{-- Only the six fixed places get a name box; custom places already have their own name field. --}}
@if (array_key_exists($placeKey, \App\Models\Property::NAMED_PLACES))
    <input type="text" class="form-control mb-2" id="{{ $id }}_name" name="place_names[{{ $placeKey }}]" maxlength="100"
        value="{{ $placeName }}" placeholder="Name (optional), e.g. {{ $placeExamples[$placeKey] ?? 'Place name' }}">
@endif
<div class="input-group distance-input">
    <input type="number" step="any" min="0" class="form-control" id="{{ $id }}" data-distance-number placeholder="Distance, e.g. 2">
    <div class="input-group-append btn-group" role="group" aria-label="Distance unit">
        <button type="button" class="btn btn-outline-secondary" data-distance-unit="m">m</button>
        <button type="button" class="btn btn-outline-secondary active" data-distance-unit="km">km</button>
    </div>
    <input type="hidden" name="{{ $name }}" data-distance-value value="{{ $value ?? '' }}">
</div>

@include('admin.partials.distance-input-script')
