        <script>
            // The lead was just captured, so release the file this visitor asked for.
            (function () {
                var a = document.createElement('a');
                a.href = @json(session('brochure_url'));
                a.setAttribute('download', '');
                document.body.appendChild(a);
                a.click();
                a.remove();
            })();
        </script>
