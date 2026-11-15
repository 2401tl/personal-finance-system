<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '个人记账系统' }} - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('plugs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugs/font-awesome/css/all.min.css') }}">
    @empty($minimal)
    <link rel="stylesheet" href="{{ asset('plugs/flatpickr/flatpickr.min.css') }}">
    @endempty
    @foreach($headScripts ?? [] as $file)
        @php
            $path = str_starts_with($file, 'resources/') ? substr($file, 10) : ltrim($file, '/');
        @endphp
        @if(str_ends_with($path, '.js'))
            <script src="{{ asset($path) }}"></script>
        @endif
    @endforeach
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @foreach($imports ?? [] as $file)
        @php
            $path = str_starts_with($file, 'resources/') ? substr($file, 10) : ltrim($file, '/');
        @endphp
        @if(str_ends_with($path, '.css'))
            <link rel="stylesheet" href="{{ asset($path) }}">
        @endif
    @endforeach
</head>
<body>
