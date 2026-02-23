<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BukberYuk')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Tailwind CSS with Forms & Container Queries plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#135bec',
                            50:  '#eff4ff',
                            100: '#dbe6fe',
                            200: '#bfd3fe',
                            300: '#93b4fd',
                            400: '#608bfa',
                            500: '#3b63f6',
                            600: '#135bec',
                            700: '#1d4ed8',
                            800: '#1e3a8a',
                            900: '#1e3a8a',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Range slider */
        input[type=range] {
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
            cursor: pointer;
            width: 100%;
        }
        input[type=range]::-webkit-slider-runnable-track {
            background: #334155;
            height: 6px;
            border-radius: 3px;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            margin-top: -5px;
            background: #135bec;
            height: 16px;
            width: 16px;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(19,91,236,0.3);
        }
        input[type=range]:focus::-webkit-slider-thumb {
            box-shadow: 0 0 0 4px rgba(19,91,236,0.5);
        }
        input[type=range]::-moz-range-track {
            background: #334155;
            height: 6px;
            border-radius: 3px;
        }
        input[type=range]::-moz-range-thumb {
            border: none;
            background: #135bec;
            height: 16px;
            width: 16px;
            border-radius: 50%;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen antialiased">
    @yield('content')

    @stack('scripts')
</body>
</html>
