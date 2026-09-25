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
        // Server limit: PHP accepts 20 files per request and silently drops the rest.
        window.GALLERY_MAX_UPLOAD = 20;

        window.previewAdditionalImages = function (event) {
            var container = document.getElementById('additional_images_preview');
            if (!container) { return; }

            (container._urls || []).forEach(function (u) { URL.revokeObjectURL(u); });
            container._urls = [];
            container.innerHTML = '';

            var files = Array.from((event.target && event.target.files) || []);
            var label = document.getElementById('additional_images_preview_label');
            if (label) { label.hidden = files.length === 0; }

            if (files.length > window.GALLERY_MAX_UPLOAD) {
                alert('You can upload at most ' + window.GALLERY_MAX_UPLOAD + ' photos at a time (' + files.length +
                    ' selected). Remove some, or save and add the rest afterwards.');
            }

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
    </script>
@endonce
