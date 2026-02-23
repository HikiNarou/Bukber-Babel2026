@extends('layouts.app')

@section('title', 'Pendaftaran Bukber 2024 – BukberYuk')

@section('content')
<div class="min-h-screen relative flex items-center justify-center p-4 overflow-hidden">

    {{-- Islamic Geometric SVG Background Pattern --}}
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="islamic" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <polygon points="40,2 78,20 78,60 40,78 2,60 2,20" fill="none" stroke="#135bec" stroke-width="1"/>
                    <polygon points="40,14 66,27 66,53 40,66 14,53 14,27" fill="none" stroke="#135bec" stroke-width="0.5"/>
                    <circle cx="40" cy="40" r="6" fill="none" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="40" y1="2" x2="40" y2="14" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="40" y1="66" x2="40" y2="78" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="2" y1="20" x2="14" y2="27" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="66" y1="53" x2="78" y2="60" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="2" y1="60" x2="14" y2="53" stroke="#135bec" stroke-width="0.5"/>
                    <line x1="66" y1="27" x2="78" y2="20" stroke="#135bec" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#islamic)"/>
        </svg>
    </div>

    {{-- Ambient glow blobs --}}
    <div class="absolute top-1/4 -left-32 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-lg">

        {{-- Card --}}
        <div class="bg-[#1e293b] rounded-2xl shadow-2xl border border-slate-700/50 overflow-hidden">

            {{-- Card Header --}}
            <div class="relative bg-primary/5 border-b border-slate-700/50 px-8 py-8 text-center overflow-hidden">
                {{-- Corner decorative circles --}}
                <div class="absolute -top-6 -left-6 w-20 h-20 bg-primary/20 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-primary/20 rounded-full blur-xl"></div>

                {{-- Moon icon --}}
                <div class="relative inline-flex items-center justify-center w-16 h-16 bg-primary/20 rounded-2xl mb-4 animate-float">
                    <span class="material-symbols-outlined filled text-primary" style="font-size:32px;">nights_stay</span>
                </div>

                <h1 class="text-3xl font-extrabold text-white tracking-tight">Pendaftaran Bukber 2024</h1>
                <p class="mt-2 text-slate-400 text-sm leading-relaxed">
                    Silakan isi form di bawah ini untuk konfirmasi kehadiran<br>dan preferensi jadwal buka bersama.
                </p>
            </div>

            {{-- Form --}}
            <form action="/register-bukber" method="POST" class="px-8 py-8 space-y-7">
                @csrf

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-400 text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">error</span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 1. Nama Lengkap --}}
                <div>
                    <label for="nama_lengkap" class="block text-sm font-semibold text-slate-300 mb-2">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400" style="font-size:20px;">person</span>
                        <input
                            type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap') }}"
                            placeholder="Masukkan nama lengkap Anda"
                            class="w-full bg-[#334155] text-white placeholder-slate-500 border border-slate-600/50 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                        >
                    </div>
                </div>

                {{-- 2. Pilih Minggu --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-slate-300">Pilih Minggu</label>
                        <span class="text-xs text-slate-500 bg-slate-700/50 px-2 py-0.5 rounded-full">Bisa pilih lebih dari satu</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ([1, 2, 3, 4] as $week)
                            @php $checked = in_array($week, old('weeks', [])); @endphp
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="weeks[]" value="{{ $week }}"
                                    {{ $checked ? 'checked' : '' }}
                                    class="sr-only peer"
                                    onchange="toggleWeekCard(this)">
                                <div class="week-card border-2 {{ $checked ? 'border-primary bg-primary/10' : 'border-slate-600/50 bg-[#334155]' }} rounded-xl p-4 flex items-center gap-3 transition-all hover:border-primary/60">
                                    <span class="material-symbols-outlined text-primary" style="font-size:22px;">date_range</span>
                                    <span class="text-sm font-medium text-white">Minggu {{ $week }}</span>
                                </div>
                                {{-- Dot indicator --}}
                                <div class="dot-indicator absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-primary {{ $checked ? 'opacity-100' : 'opacity-0' }} transition-opacity"></div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Preferensi Hari --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-3">Preferensi Hari</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                            @php $checked = in_array($day, old('days', [])); @endphp
                            <label class="cursor-pointer">
                                <input type="checkbox" name="days[]" value="{{ $day }}"
                                    {{ $checked ? 'checked' : '' }}
                                    class="sr-only peer">
                                <span class="day-chip inline-block px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all select-none
                                    {{ $checked
                                        ? 'bg-primary border-primary text-white'
                                        : 'bg-transparent border-slate-600 text-slate-400 hover:border-primary/60 hover:text-slate-200' }}"
                                    onclick="toggleDayChip(this)">
                                    {{ $day }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Budget --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-slate-300">Batas Budget (Per Orang)</label>
                        <span id="budgetDisplay" class="bg-primary/20 text-primary font-mono text-sm font-bold px-3 py-1 rounded-lg">IDR 150.000</span>
                    </div>
                    <input
                        type="range"
                        id="budgetRange"
                        name="budget"
                        min="50000"
                        max="500000"
                        step="10000"
                        value="{{ old('budget', 150000) }}"
                        class="w-full"
                        oninput="updateBudget(this.value)"
                    >
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-slate-500">50rb</span>
                        <span class="text-xs text-slate-500">500rb</span>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full bg-primary hover:bg-primary/90 active:scale-[0.98] text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/25">
                    <span>Daftar Sekarang</span>
                    <span class="material-symbols-outlined" style="font-size:20px;">arrow_forward</span>
                </button>

                {{-- Privacy note --}}
                <p class="text-center text-xs text-slate-500 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:14px;">lock</span>
                    Data Anda aman dan hanya digunakan untuk keperluan acara ini.
                </p>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-slate-600 mt-6">© 2024 Panitia Ramadan Ceria.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateBudget(val) {
    const num = parseInt(val);
    document.getElementById('budgetDisplay').textContent = 'IDR ' + num.toLocaleString('id-ID');
}

function toggleWeekCard(checkbox) {
    const label = checkbox.closest('label');
    const card  = label.querySelector('.week-card');
    const dot   = label.querySelector('.dot-indicator');
    if (checkbox.checked) {
        card.classList.add('border-primary', 'bg-primary/10');
        card.classList.remove('border-slate-600/50', 'bg-[#334155]');
        dot.classList.remove('opacity-0');
        dot.classList.add('opacity-100');
    } else {
        card.classList.remove('border-primary', 'bg-primary/10');
        card.classList.add('border-slate-600/50', 'bg-[#334155]');
        dot.classList.add('opacity-0');
        dot.classList.remove('opacity-100');
    }
}

function toggleDayChip(span) {
    const input = span.closest('label').querySelector('input[type=checkbox]');
    // The click on span fires before checkbox toggles, so we invert current state
    if (!input.checked) {
        span.classList.add('bg-primary', 'border-primary', 'text-white');
        span.classList.remove('bg-transparent', 'border-slate-600', 'text-slate-400', 'hover:border-primary/60', 'hover:text-slate-200');
    } else {
        span.classList.remove('bg-primary', 'border-primary', 'text-white');
        span.classList.add('bg-transparent', 'border-slate-600', 'text-slate-400', 'hover:border-primary/60', 'hover:text-slate-200');
    }
}

// Init budget display on load
document.addEventListener('DOMContentLoaded', function() {
    updateBudget(document.getElementById('budgetRange').value);
});
</script>
@endpush
