{{-- Shared gallery grid for the add/edit property pages: the tile styles, the preview of newly
     chosen photos and the remove-one-before-saving handler.

     Layout: an auto-filling grid of equal 4:3 tiles, so any number of photos wraps cleanly,
     the cross is pinned inside each tile's corner, and long galleries scroll instead of
     stretching the page. Previews use object URLs and are built in file order, so the button
     on tile N always removes file N (FileReader callbacks finish out of order). --}}
@once
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            max-height: 460px;
            overflow-y: auto;
            padding: 2px;
        }

        .gallery-grid:empty {
            display: none;
        }

        .gallery-tile {
            position: relative;
            aspect-ratio: 4 / 3;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            background: #f4f4f4;
        }

        .gallery-tile img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Pinned to the tile's own top-right corner, whatever the image size or column width. */
        .gallery-tile__remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 24px;
            height: 24px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: #dc3545;
            color: #fff;
            font-size: 16px;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .35);
        }

        .gallery-tile__remove:hover {
            background: #b52a37;
        }

        .gallery-tile__remove:disabled {
            opacity: .6;
            cursor: wait;
        }

        .gallery-tile__badge {
            position: absolute;
            left: 4px;
            bottom: 4px;
            padding: 1px 6px;
            border-radius: 3px;
            background: rgba(0, 0, 0, .65);
            color: #fff;
            font-size: 11px;
            line-height: 16px;
        }
    </style>

    <script>
        // PHP accepts only 20 files per request and silently drops the rest, so the form carries
        // at most GALLERY_BATCH photos and the others follow in separate requests (see submit handler below).
        window.GALLERY_BATCH = 10;

        window.previewAdditionalImages = function (event) {
            var container = document.getElementById('additional_images_preview');
            if (!container) { return; }

            (container._urls || []).forEach(function (u) { URL.revokeObjectURL(u); });
            container._urls = [];
            container.innerHTML = '';

            var files = Array.from((event.target && event.target.files) || []);
            var label = document.getElementById('additional_images_preview_label');
            if (label) { label.hidden = files.length === 0; }

            files.forEach(function (file, i) {
                var url = URL.createObjectURL(file);
                container._urls.push(url);

                var tile = document.createElement('div');
                tile.className = 'gallery-tile';

                var img = document.createElement('img');
                img.src = url;
                img.alt = 'New photo ' + (i + 1);

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'gallery-tile__remove';
                btn.title = 'Remove before saving';
                btn.setAttribute('aria-label', 'Remove photo ' + (i + 1));
                btn.textContent = '×';
                btn.addEventListener('click', function () { window.removeAdditionalImage(i); });

                tile.appendChild(img);
                tile.appendChild(btn);
                container.appendChild(tile);
            });
        };

        // Drop file #index from the input, then redraw the previews.
        window.removeAdditionalImage = function (index) {
            var input = document.getElementById('property_images');
            var files = Array.from(input.files);
            files.splice(index, 1);

            var dt = new DataTransfer();
            files.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;

            input.dispatchEvent(new Event('change'));
        };

        // Big selections: save the form with the first batch, then send the rest in batches of
        // GALLERY_BATCH to the property that was just saved. Runs after the page's own validation.
        document.addEventListener('submit', function (e) {
            var form = e.target;
            var input = document.getElementById('property_images');
            if (e.defaultPrevented || !input || !form.contains(input) || input.files.length <= window.GALLERY_BATCH) { return; }
            e.preventDefault();

            var all = Array.from(input.files);
            var rest = all.slice(window.GALLERY_BATCH);
            var csrf = (form.querySelector('input[name=_token]') || {}).value;
            var buttons = form.querySelectorAll('button[type=submit], input[type=submit]');
            buttons.forEach(function (b) { b.disabled = true; });

            var status = document.createElement('div');
            status.className = 'alert alert-info';
            status.style.cssText = 'position:fixed;top:70px;right:20px;z-index:99999;box-shadow:0 2px 8px rgba(0,0,0,.3)';
            status.textContent = 'Saving…';
            document.body.appendChild(status);

            function fail(msg) {
                status.remove();
                buttons.forEach(function (b) { b.disabled = false; });
                alert(msg);
            }

            var data = new FormData(form);
            data.delete('property_images[]');
            all.slice(0, window.GALLERY_BATCH).forEach(function (f) { data.append('property_images[]', f); });

            fetch(form.action, {
                method: 'POST', body: data,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (body) { return { res: res, body: body }; });
            }).then(function (r) {
                if (r.res.status === 422) {
                    var msgs = [];
                    Object.keys(r.body.errors || {}).forEach(function (k) { msgs = msgs.concat(r.body.errors[k]); });
                    throw new Error(msgs.join('\n') || 'Please check the form.');
                }
                if (!r.res.ok || !r.body.property_id) { throw new Error(r.body.message || 'Could not save the property.'); }

                var url = '{{ url('admin/properties') }}/' + r.body.property_id + '/images';
                var done = window.GALLERY_BATCH;
                var chain = Promise.resolve();
                for (let i = 0; i < rest.length; i += window.GALLERY_BATCH) {
                    chain = chain.then(function () {
                        status.textContent = 'Uploading photos ' + (done + 1) + '–' + Math.min(done + window.GALLERY_BATCH, all.length) + ' of ' + all.length + '…';
                        var d = new FormData();
                        d.append('_token', csrf);
                        rest.slice(i, i + window.GALLERY_BATCH).forEach(function (f) { d.append('property_images[]', f); });
                        return fetch(url, { method: 'POST', body: d, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (res) { if (!res.ok) { throw new Error('Photos ' + (done + 1) + ' onwards failed to upload (HTTP ' + res.status + '). The property itself was saved — open it and add the remaining photos.'); } done += window.GALLERY_BATCH; });
                    });
                }
                return chain.then(function () { window.location.href = r.body.redirect; });
            }).catch(function (err) { fail(err.message); });
        });

        // Replace the file of one saved photo (edit page) without reloading.
        document.addEventListener('change', function (e) {
            var input = e.target.closest && e.target.closest('[data-image-replace]');
            if (!input || !input.files.length) { return; }
            var tile = input.closest('.gallery-tile');
            var d = new FormData();
            d.append('_token', input.dataset.token);
            d.append('image', input.files[0]);
            tile.style.opacity = .5;
            fetch(input.dataset.url, { method: 'POST', body: d, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (b) {
                        if (!res.ok) { throw new Error(((b.errors && b.errors.image) || [b.message || 'Could not replace the image.'])[0]); }
                        tile.querySelector('img').src = b.url + '?t=' + Date.now();
                    });
                })
                .catch(function (err) { alert(err.message); })
                .then(function () { tile.style.opacity = ''; input.value = ''; });
        });
    </script>
    <style>
        .gallery-tile__replace {
            position: absolute;
            top: 4px;
            right: 32px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            font-size: 12px;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .35);
        }

        .gallery-tile__replace input {
            display: none;
        }
    </style>
@endonce
