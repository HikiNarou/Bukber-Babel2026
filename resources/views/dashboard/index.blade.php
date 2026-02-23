<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – BukberYuk</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
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
                            400: '#608bfa',
                            500: '#3b63f6',
                            600: '#135bec',
                            700: '#1d4ed8',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #131926; }
        ::-webkit-scrollbar-thumb { background: #2a3347; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #3a4460; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .bar-winner {
            background: linear-gradient(to top, #135bec, #60a5fa);
            box-shadow: 0 0 16px rgba(19,91,236,0.5);
        }

        .bar-normal {
            background: linear-gradient(to top, #1e2a44, #2a3a5c);
        }

        .heatmap-low   { background: rgba(19,91,236,0.08); }
        .heatmap-med   { background: rgba(19,91,236,0.30); }
        .heatmap-high  { background: rgba(19,91,236,0.65); }
        .heatmap-peak  {
            background: #135bec;
            box-shadow: 0 0 8px rgba(19,91,236,0.6);
        }

        @keyframes bounce-crown {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-4px); }
        }
        .crown-bounce { animation: bounce-crown 1.2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#101622] text-slate-100 min-h-screen antialiased">

{{-- ============ TOP NAV ============ --}}
<nav class="sticky top-0 z-50 backdrop-blur-md bg-[#101622]/80 border-b border-slate-800/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo + Brand --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-blue-400 flex items-center justify-center shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined filled text-white" style="font-size:16px;">nightlight</span>
                </div>
                <span class="font-extrabold text-white text-lg tracking-tight">BukberYuk</span>
            </div>

            {{-- Nav links --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach (['Dashboard','Peserta','Voting','Lokasi'] as $nav)
                    <a href="#" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                        {{ $nav === 'Dashboard' ? 'bg-primary/15 text-primary' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        {{ $nav }}
                    </a>
                @endforeach
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                <button class="hidden sm:flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined" style="font-size:18px;">share</span>
                    Bagikan
                </button>
                {{-- User avatar --}}
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-blue-400 flex items-center justify-center font-bold text-white text-sm shadow-lg">
                    {{ strtoupper(substr(session('user_name', 'A'), 0, 1)) }}
                </div>
                {{-- Logout --}}
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Logout">
                        <span class="material-symbols-outlined" style="font-size:20px;">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ============ MAIN CONTENT ============ --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ---- HEADER SECTION ---- --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Kesimpulan Bukber 2024</h1>
            <p class="mt-1 text-slate-400">Analisa data dari <span class="text-white font-semibold">{{ $total }}</span> responden teman-teman.</p>
        </div>
        <div class="inline-flex items-center gap-2 bg-slate-800/60 border border-slate-700/50 text-slate-400 text-sm px-4 py-2 rounded-full">
            <span class="material-symbols-outlined" style="font-size:16px;">update</span>
            Updated {{ $hoursAgo }} hours ago
        </div>
    </div>

    {{-- ---- KPI CARDS ---- --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        {{-- Total Peserta --}}
        <div class="bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6 space-y-4 hover:border-primary/30 transition-colors group">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 bg-primary/15 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined filled text-primary" style="font-size:22px;">group</span>
                </div>
                <span class="material-symbols-outlined text-green-400" style="font-size:20px;">trending_up</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Total Peserta</p>
                <p class="text-4xl font-extrabold text-white mt-1">{{ $total }} <span class="text-lg text-slate-400 font-normal">Orang</span></p>
            </div>
            {{-- Progress bar --}}
            <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary to-blue-400 rounded-full transition-all duration-700" style="width: {{ $totalPct }}%"></div>
            </div>
        </div>

        {{-- Rata-rata Budget --}}
        <div class="bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6 space-y-4 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 bg-emerald-500/15 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined filled text-emerald-400" style="font-size:22px;">payments</span>
                </div>
                <span class="material-symbols-outlined text-emerald-400" style="font-size:20px;">trending_up</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Rata-rata Budget</p>
                <p class="text-4xl font-extrabold text-white mt-1">{{ $avgBudgetFmt }}</p>
                <p class="text-slate-500 text-xs mt-1">Per orang (Estimasi)</p>
            </div>
            <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full" style="width: {{ $budgetPct }}%"></div>
            </div>
        </div>

        {{-- Minggu Terfavorit --}}
        <div class="bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6 space-y-4 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 bg-violet-500/15 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined filled text-violet-400" style="font-size:22px;">event_available</span>
                </div>
                <span class="bg-violet-500/15 text-violet-400 text-xs font-bold px-2.5 py-1 rounded-full">{{ $favoriteWeekPct }}%</span>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Minggu Terfavorit</p>
                <p class="text-4xl font-extrabold text-white mt-1">Minggu Ke-{{ $favoriteWeek }}</p>
                <p class="text-slate-500 text-xs mt-1">{{ $favoriteWeekCount }} suara · {{ $favoriteWeekPct }}% responden</p>
            </div>
            <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-violet-500 to-purple-400 rounded-full" style="width: {{ $weekPct }}%"></div>
            </div>
        </div>
    </div>

    {{-- ---- BAR CHART + SIDEBAR ---- --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- BAR CHART --}}
        <div class="lg:col-span-2 bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Hari Paling Banyak Bisa</h2>
                    <p class="text-slate-500 text-xs mt-0.5">Distribusi preferensi hari dari semua responden</p>
                </div>
                <span class="bg-primary/15 text-primary text-xs font-bold px-3 py-1.5 rounded-full">7 Hari</span>
            </div>

            <div class="flex items-end gap-2 h-48 relative">
                {{-- Y-axis grid lines --}}
                @php $gridLines = 4; @endphp
                @for ($i = 0; $i <= $gridLines; $i++)
                    <div class="absolute left-0 right-0 border-t border-dashed border-slate-700/40"
                         style="bottom: {{ ($i / $gridLines) * 100 }}%"></div>
                @endfor

                {{-- Bars --}}
                @foreach ($allDays as $day)
                    @php
                        $count  = $dayCounts[$day];
                        $pct    = $maxDayCount > 0 ? round($count / $maxDayCount * 100) : 0;
                        $isWinner = ($day === $majorityDay);
                    @endphp
                    <div class="relative flex-1 flex flex-col items-center gap-1 group" style="height:100%">
                        {{-- Crown for winner --}}
                        @if ($isWinner)
                            <div class="absolute -top-6 crown-bounce">
                                <span class="text-yellow-400" style="font-size:16px;">👑</span>
                            </div>
                        @endif

                        {{-- Bar container --}}
                        <div class="w-full flex items-end justify-center" style="height:calc(100% - 24px)">
                            <div class="relative w-full rounded-t-lg transition-all duration-500
                                {{ $isWinner ? 'bar-winner' : 'bar-normal' }}"
                                style="height: {{ max($pct, 4) }}%">
                                {{-- Count badge on top --}}
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2
                                    {{ $isWinner ? 'bg-primary/20 text-primary' : 'bg-slate-700/60 text-slate-400' }}
                                    text-xs font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    {{ $count }}
                                </div>
                            </div>
                        </div>

                        {{-- Day label --}}
                        <span class="text-xs {{ $isWinner ? 'text-primary font-bold' : 'text-slate-500' }} truncate">
                            {{ substr($day, 0, 3) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SIDEBAR: Responden Terbaru --}}
        <div class="bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-white">Responden Terbaru</h2>
                <span class="bg-slate-700/60 text-slate-400 text-xs px-2.5 py-1 rounded-full">{{ min($recent->count(), 10) }}</span>
            </div>

            <div class="space-y-3 overflow-y-auto max-h-64">
                @forelse ($recent as $reg)
                    @php
                        $statusColor = match($reg->status) {
                            'bisa'    => 'bg-green-500/15 text-green-400',
                            'mungkin' => 'bg-yellow-500/15 text-yellow-400',
                            'tidak'   => 'bg-red-500/15 text-red-400',
                            default   => 'bg-slate-700 text-slate-400',
                        };
                        $statusLabel = match($reg->status) {
                            'bisa'    => 'Bisa',
                            'mungkin' => 'Mungkin',
                            'tidak'   => 'Tidak',
                            default   => $reg->status,
                        };
                        $initial = strtoupper(substr($reg->nama_lengkap, 0, 1));
                    @endphp
                    <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-800/50 transition-colors">
                        <div class="w-9 h-9 flex-shrink-0 rounded-full bg-gradient-to-br from-primary/60 to-blue-500/60 flex items-center justify-center font-bold text-white text-sm">
                            {{ $initial }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $reg->nama_lengkap }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ implode(', ', array_slice($reg->days, 0, 2)) }}</p>
                        </div>
                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-500 text-sm text-center py-4">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ---- HEATMAP ---- --}}
    <div class="bg-[#1e2330] rounded-2xl border border-slate-700/40 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Detail Ketersediaan</h2>
                <p class="text-slate-500 text-xs mt-0.5">Estimasi jam berdasarkan preferensi hari</p>
            </div>
            {{-- Legend --}}
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span class="font-semibold">Intensitas:</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded heatmap-low border border-slate-600/30 inline-block"></span>Low
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded heatmap-med inline-block"></span>Med
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded heatmap-high inline-block"></span>High
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded heatmap-peak inline-block"></span>Peak
                </span>
            </div>
        </div>

        @php
            $timeSlots = ['16:00', '17:00', '18:00'];
            $heatDays  = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

            // Compute intensity based on day counts + time multipliers
            $timeMultipliers = [0.6, 0.8, 1.0];
            $maxHeat = $maxDayCount > 0 ? $maxDayCount : 1;
        @endphp

        <div class="overflow-x-auto">
            <div class="min-w-[480px]">
                {{-- Header row --}}
                <div class="grid grid-cols-8 gap-1.5 mb-2">
                    <div class="text-xs text-slate-500 font-semibold text-right pr-2 py-1">Jam</div>
                    @foreach ($heatDays as $hd)
                        <div class="text-xs text-slate-400 font-semibold text-center py-1
                            {{ $hd === $majorityDay ? 'text-primary' : '' }}">
                            {{ substr($hd, 0, 3) }}
                        </div>
                    @endforeach
                </div>

                {{-- Data rows --}}
                @foreach ($timeSlots as $ti => $time)
                    <div class="grid grid-cols-8 gap-1.5 mb-1.5">
                        <div class="text-xs text-slate-500 font-mono text-right pr-2 flex items-center justify-end">{{ $time }}</div>
                        @foreach ($heatDays as $hd)
                            @php
                                $cnt = $dayCounts[$hd] ?? 0;
                                $intensity = ($maxHeat > 0) ? ($cnt / $maxHeat) * $timeMultipliers[$ti] : 0;

                                if ($hd === $majorityDay && $ti === 2) {
                                    $cellClass = 'heatmap-peak';
                                } elseif ($intensity >= 0.65) {
                                    $cellClass = 'heatmap-high';
                                } elseif ($intensity >= 0.3) {
                                    $cellClass = 'heatmap-med';
                                } else {
                                    $cellClass = 'heatmap-low';
                                }
                            @endphp
                            <div class="h-9 rounded-lg {{ $cellClass }} flex items-center justify-center text-xs font-semibold
                                {{ $cellClass === 'heatmap-peak' ? 'text-white' : 'text-slate-500' }}
                                hover:scale-105 transition-transform cursor-default"
                                title="{{ $hd }} {{ $time }}: {{ $cnt }} orang">
                                {{ $cnt > 0 ? $cnt : '' }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ---- CTA BANNER ---- --}}
    <div class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-primary to-blue-600 p-8">
        {{-- Decorative blobs --}}
        <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-extrabold text-white">Siap menentukan tanggal?</h2>
                <p class="text-blue-100 text-sm mt-1">
                    Mayoritas memilih hari <strong>{{ $majorityDay }}</strong> dengan budget <strong>{{ $majorityBudgetFmt }}</strong>.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px;">forum</span>
                    Diskusi Dulu
                </button>
                <button class="bg-white text-primary hover:bg-blue-50 font-bold px-5 py-2.5 rounded-xl transition-colors text-sm flex items-center gap-2 shadow-lg">
                    <span class="material-symbols-outlined filled" style="font-size:18px;">check_circle</span>
                    Tetapkan {{ $majorityDay }}
                </button>
            </div>
        </div>
    </div>

</main>

{{-- Footer --}}
<footer class="border-t border-slate-800/60 mt-8 py-6 text-center text-xs text-slate-600">
    © 2024 BukberYuk – Panitia Ramadan Ceria.
</footer>

</body>
</html>
