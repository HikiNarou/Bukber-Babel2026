@extends('layouts.app')

@section('title', 'Login – BukberYuk')

@section('content')
<div class="min-h-screen relative flex items-center justify-center p-4 overflow-hidden">

    {{-- Background blobs --}}
    <div class="absolute top-1/4 -left-32 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">
        <div class="bg-[#1e293b] rounded-2xl shadow-2xl border border-slate-700/50 overflow-hidden">

            {{-- Header --}}
            <div class="relative bg-primary/5 border-b border-slate-700/50 px-8 py-8 text-center overflow-hidden">
                <div class="absolute -top-6 -left-6 w-20 h-20 bg-primary/20 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-primary/20 rounded-full blur-xl"></div>

                <div class="relative inline-flex items-center justify-center w-14 h-14 bg-primary/20 rounded-xl mb-4">
                    <span class="material-symbols-outlined filled text-primary" style="font-size:28px;">lock</span>
                </div>
                <h1 class="text-2xl font-extrabold text-white">Masuk ke BukberYuk</h1>
                <p class="mt-1 text-slate-400 text-sm">Akses dashboard dengan akun Anda</p>
            </div>

            {{-- Form --}}
            <form action="/login" method="POST" class="px-8 py-8 space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-400 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined" style="font-size:16px;">error</span>
                                {{ $error }}
                            </p>
                        @endforeach
                    </div>
                @endif

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400" style="font-size:20px;">mail</span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="email@example.com"
                            class="w-full bg-[#334155] text-white placeholder-slate-500 border border-slate-600/50 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400" style="font-size:20px;">key</span>
                        <input type="password" id="password" name="password"
                            placeholder="••••••••"
                            class="w-full bg-[#334155] text-white placeholder-slate-500 border border-slate-600/50 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/25">
                    <span class="material-symbols-outlined" style="font-size:20px;">login</span>
                    Masuk
                </button>

                <p class="text-center text-sm text-slate-500">
                    Belum punya akun?
                    <a href="/register" class="text-primary hover:underline font-semibold">Daftar sekarang</a>
                </p>
            </form>
        </div>
        <p class="text-center text-xs text-slate-600 mt-6">© 2024 Panitia Ramadan Ceria.</p>
    </div>
</div>
@endsection
