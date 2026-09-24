@once
    <script>
        function initDistanceInput(box) {
            if (box.dataset.ready) { return; }
            box.dataset.ready = '1';
            {
                var num = box.querySelector('[data-distance-number]');
                var hidden = box.querySelector('[data-distance-value]');
                var buttons = box.querySelectorAll('[data-distance-unit]');
                var unit = 'km';

                function setUnit(u) {
                    unit = u;
                    buttons.forEach(function (b) {
                        b.classList.toggle('active', b.dataset.distanceUnit === u);
                    });
                }

                function sync() {
                    hidden.value = num.value !== '' ? num.value + ' ' + unit : '';
                }

                // Pre-fill from a stored value, including legacy free text like "Metro (0.5 km)".
                var m = (hidden.value || '').match(/(\d+(?:\.\d+)?)\s*(km|m)\b/i);
                if (m) {
                    num.value = m[1];
                    setUnit(m[2].toLowerCase());
                }
                sync();

                num.addEventListener('input', sync);
                buttons.forEach(function (b) {
                    b.addEventListener('click', function () {
                        setUnit(b.dataset.distanceUnit);
                        sync();
                    });
                });
            }
        }
        window.initDistanceInput = initDistanceInput;
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.distance-input').forEach(initDistanceInput);
        });
    </script>
@endonce
