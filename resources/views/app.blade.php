<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        {{-- Fontes do convite Iasmin (Great Vibes / Bodoni Moda / Lora) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,600;6..96,700&family=Great+Vibes&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap"
            rel="stylesheet"
        >

        @php
            $isInvite = request()->routeIs('home', 'confirmar', 'presentes');
            $shareTitle = match (true) {
                request()->routeIs('confirmar') => 'Iasmin 15 anos – Confirmação de Presença',
                request()->routeIs('presentes') => 'Iasmin 15 anos – Lista de presentes',
                default => 'Iasmin 15 anos – Convite',
            };
            $shareDescription = 'Você é nosso convidado especial. A borboleta mais linda do nosso jardim vai completar 15 anos. 8 de novembro de 2026, às 10h.';
            $shareImage = url('/images/og-convite.jpg');
            $shareUrl = request()->url();
        @endphp

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])

        @if ($isInvite)
            <meta name="description" content="{{ $shareDescription }}">
            <meta name="theme-color" content="#8a00c4">
            <meta property="og:site_name" content="Iasmin 15 anos">
            <meta property="og:title" content="{{ $shareTitle }}">
            <meta property="og:description" content="{{ $shareDescription }}">
            <meta property="og:type" content="website">
            <meta property="og:locale" content="pt_BR">
            <meta property="og:url" content="{{ $shareUrl }}">
            <meta property="og:image" content="{{ $shareImage }}">
            <meta property="og:image:secure_url" content="{{ $shareImage }}">
            <meta property="og:image:type" content="image/jpeg">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:alt" content="Convite dos 15 anos da Iasmin">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $shareTitle }}">
            <meta name="twitter:description" content="{{ $shareDescription }}">
            <meta name="twitter:image" content="{{ $shareImage }}">
        @endif

        <x-inertia::head>
            <title>{{ $isInvite ? $shareTitle : config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
