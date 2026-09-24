<div class="custom-place border rounded p-2 mb-2" style="background-color: #fcfcfc;">
    <input type="hidden" name="custom_places[{{ $key }}][group]" value="{{ $group }}">
    <div class="form-row">
        <div class="col-md-6 mb-2">
            <input type="text" class="form-control form-control-sm" maxlength="100"
                name="custom_places[{{ $key }}][label]" placeholder="Place name, e.g. City Mall"
                value="{{ $place['label'] ?? '' }}">
        </div>
        <div class="col-md-6 mb-2">
            <select class="form-control form-control-sm" name="custom_places[{{ $key }}][icon]">
                @foreach (\App\Models\Property::PLACE_ICONS as $class => $iconLabel)
                    <option value="{{ $class }}" {{ ($place['icon'] ?? \App\Models\Property::DEFAULT_PLACE_ICON) === $class ? 'selected' : '' }}>{{ $iconLabel }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="d-flex align-items-start">
        <div class="flex-grow-1">
            @include('admin.partials.distance-input', [
                'id' => 'cp_' . $key,
                'name' => "custom_places[{$key}][distance]",
                'value' => $place['distance'] ?? '',
            ])
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger ml-2 custom-place__remove" title="Remove">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</div>
