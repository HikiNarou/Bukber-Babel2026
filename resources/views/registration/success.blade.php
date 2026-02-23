@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil – BukberYuk')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="relative z-10 w-full max-w-md text-center">
        <div class="bg-[#1e293b] rounded-2xl shadow-2xl border border-slate-700/50 px-8 py-12">

            {{-- Success icon --}}
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500/20 rounded-full mb-6 animate-float">
                <span class="material-symbols-outlined filled text-green-400" style="font-size:42px;">check_circle</span>
            </div>

            <h1 class="text-3xl font-extrabold text-white mb-3">Pendaftaran Berhasil!</h1>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">
                Terima kasih telah mendaftar. Data Anda sudah kami terima dan akan diproses oleh panitia.
                Nantikan informasi selanjutnya ya!
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/"
                   class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/25">
                    <span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span>
                    Kembali ke Form
                </a>
                <a href="/dashboard"
                   class="inline-flex items-center justify-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold px-6 py-3 rounded-xl transition-all">
                    <span class="material-symbols-outlined" style="font-size:18px;">dashboard</span>
                    Lihat Dashboard
                </a>
            </div>
        </div>
        <p class="text-center text-xs text-slate-600 mt-6">© 2024 Panitia Ramadan Ceria.</p>
    </div>
</div>
@endsection
