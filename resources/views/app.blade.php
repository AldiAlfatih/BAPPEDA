<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO Meta Tags --}}
        <title inertia>Panrita - Bappeda Kota Parepare</title>
        <meta name="description" content="Panrita adalah platform digital milik Bappeda Kota Parepare yang mencakup Sistem Inovasi Daerah, Sistem Monitoring Pembangunan, dan Sistem Indikator Kinerja untuk mendukung tata kelola pembangunan yang efisien dan berbasis data.">
        <meta name="keywords" content="Panrita, Bappeda Parepare, Sistem Inovasi Daerah, Sistem Monitoring Pembangunan, Indikator Kinerja, e-Monev, Pemerintahan Digital, Pembangunan Daerah, Kota Parepare, Inovasi Pemerintah, PDG, Parepare dalam Genggaman, ITH, Institut Teknologi Bacharuddin Jusuf Habibie">
        <meta name="author" content="Tim Penelitian dan Pengabdian ITH 2025">

        {{-- Open Graph / Facebook --}}
        <meta property="og:title" content="Panrita - Bappeda Kota Parepare">
        <meta property="og:description" content="Satu platform untuk memantau inovasi, pembangunan, dan indikator kinerja daerah di Kota Parepare.">
        <meta property="og:image" content="https://panrita.bappeda.pareparekota.go.id/assets/images/og-image.png">
        <meta property="og:url" content="https://panrita.bappeda.pareparekota.go.id">
        <meta property="og:type" content="website">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Panrita - Bappeda Kota Parepare">
        <meta name="twitter:description" content="Sistem Inovasi Daerah, Monitoring Pembangunan, dan Indikator Kinerja dalam satu platform terintegrasi.">
        <meta name="twitter:image" content="https://panrita.bappeda.pareparekota.go.id/assets/images/twitter-card.png">

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

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
