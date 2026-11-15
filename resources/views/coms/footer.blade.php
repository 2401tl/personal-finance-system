    <script src="{{ asset('plugs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @foreach($imports ?? [] as $file)
        @php
            $path = str_starts_with($file, 'resources/') ? substr($file, 10) : ltrim($file, '/');
        @endphp
        @if(str_ends_with($path, '.js'))
            <script src="{{ asset($path) }}"></script>
        @endif
    @endforeach
    <script>
        window.addEventListener('load', () => {
            @if(isset($errors) && $errors->any())
                showToast('{{ $errors->first() }}', 'error');
            @endif
            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
        });
    </script>
</body>
</html>
