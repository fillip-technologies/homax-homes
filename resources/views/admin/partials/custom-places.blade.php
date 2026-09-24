{{-- Admin-defined extra places for the "nearby" or "connectivity" card. Expects $group and $places (all saved/old custom places). --}}
@php
    $rows = collect($places ?? [])->filter(fn ($p) => ($p['group'] ?? null) === $group)->all();
@endphp
@include('admin.partials.distance-input-script')

<div class="custom-places border-top pt-3 mt-3" data-group="{{ $group }}">
    <label class="small font-weight-bold d-block mb-2">Other places</label>
    <div class="custom-places__rows">
        @foreach ($rows as $i => $place)
            @include('admin.partials.custom-place-row', ['key' => $group . '_e' . $loop->index, 'group' => $group, 'place' => $place])
        @endforeach
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary custom-places__add">
        <i class="fas fa-plus mr-1"></i> Add custom place
    </button>

    <template class="custom-places__template">
        @include('admin.partials.custom-place-row', ['key' => '__KEY__', 'group' => $group, 'place' => []])
    </template>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var counter = 0;
            document.querySelectorAll('.custom-places').forEach(function (box) {
                var rows = box.querySelector('.custom-places__rows');
                var tpl = box.querySelector('.custom-places__template');

                box.querySelector('.custom-places__add').addEventListener('click', function () {
                    var html = tpl.innerHTML.split('__KEY__').join(box.dataset.group + '_n' + (++counter));
                    rows.insertAdjacentHTML('beforeend', html);
                    window.initDistanceInput(rows.lastElementChild.querySelector('.distance-input'));
                });

                box.addEventListener('click', function (e) {
                    var btn = e.target.closest('.custom-place__remove');
                    if (btn) { btn.closest('.custom-place').remove(); }
                });
            });
        });
    </script>
@endonce
