{{-- Numeric distance with an m / km toggle. Submits a normalised string such as "2 km" under $name. --}}
<div class="input-group distance-input">
    <input type="number" step="any" min="0" class="form-control" id="{{ $id }}" data-distance-number placeholder="e.g. 2">
    <div class="input-group-append btn-group" role="group" aria-label="Distance unit">
        <button type="button" class="btn btn-outline-secondary" data-distance-unit="m">m</button>
        <button type="button" class="btn btn-outline-secondary active" data-distance-unit="km">km</button>
    </div>
    <input type="hidden" name="{{ $name }}" data-distance-value value="{{ $value ?? '' }}">
</div>

@include('admin.partials.distance-input-script')
