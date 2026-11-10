@php $id = $m ? $m->id : 'new'; @endphp
<div class="form-row">
    <div class="form-group col-md-4">
        <label>Name</label>
        <input type="text" name="name" class="form-control" maxlength="100" required value="{{ $m?->name }}">
    </div>
    <div class="form-group col-md-4">
        <label>Role <small class="text-muted">(e.g. Founder)</small></label>
        <input type="text" name="role" class="form-control" maxlength="100" value="{{ $m?->role }}">
    </div>
    <div class="form-group col-md-4">
        <label>Order</label>
        <input type="number" name="sort_order" min="0" max="9999" class="form-control" value="{{ $m?->sort_order }}" placeholder="auto">
    </div>
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="4" maxlength="1000">{{ $m?->description }}</textarea>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label>Photo <small class="text-muted">(JPG/PNG/WebP, under 2 MB)</small></label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-control-file">
    </div>
    <div class="form-group col-md-6 pt-4">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="am_active_{{ $id }}" name="is_active" value="1" {{ !$m || $m->is_active ? 'checked' : '' }}>
            <label class="custom-control-label" for="am_active_{{ $id }}">Show on website</label>
        </div>
        @if ($m && $m->photo)
            <div class="custom-control custom-checkbox mt-1">
                <input type="checkbox" class="custom-control-input" id="am_rm_{{ $id }}" name="remove_photo" value="1">
                <label class="custom-control-label" for="am_rm_{{ $id }}">Remove photo</label>
            </div>
        @endif
    </div>
</div>
