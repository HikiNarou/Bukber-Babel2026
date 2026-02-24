import Link from "next/link";
import { ArrowRight, CalendarCheck, ChartColumnBig, Vote } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Button } from "@/components/ui/Button";

export default function Home() {
  return (
    <Container className="py-8 md:py-16">
      <section className="relative overflow-hidden rounded-3xl border border-[#2d4161] bg-[radial-gradient(circle_at_80%_10%,rgba(47,109,242,0.35),transparent_45%),linear-gradient(155deg,#111f34_0%,#0c1730_60%,#091328_100%)] px-5 py-10 md:rounded-[34px] md:px-12 md:py-20">
        <div className="mx-auto max-w-4xl text-center">
          <p className="mb-3 inline-flex rounded-full border border-[#3b5f96] bg-[#17366a]/60 px-3 py-1 text-xs text-[#7eb0ff] md:mb-4 md:px-4 md:py-1.5 md:text-sm">
            Ramadhan 2026
          </p>
          <h1 className="text-3xl font-semibold leading-tight text-white sm:text-4xl md:text-6xl">
            Platform Bukber Magang BSB
          </h1>
          <p className="mx-auto mt-4 max-w-3xl text-base text-slate-300 md:mt-5 md:text-2xl">
            Satu tempat untuk pendaftaran, voting lokasi, ringkasan data peserta, sampai pengumuman tanggal final.
          </p>
          <div className="mt-7 flex flex-col justify-center gap-3 sm:mt-10 sm:flex-row">
            <Link href="/daftar">
              <Button size="lg">
                Daftar Bukber
                <ArrowRight className="h-5 w-5" />
              </Button>
            </Link>
            <Link href="/dashboard">
              <Button size="lg" variant="secondary">
                Lihat Dashboard
              </Button>
            </Link>
          </div>
        </div>
      </section>

      <section className="mt-6 grid gap-4 md:mt-8 md:grid-cols-3">
        <article className="glass-surface rounded-2xl p-5 md:rounded-3xl md:p-6">
          <CalendarCheck className="h-7 w-7 text-[#6ea3ff]" />
          <h3 className="mt-3 text-xl font-semibold text-white md:mt-4 md:text-2xl">Daftar Cepat</h3>
          <p className="mt-2 text-base text-slate-300">Isi form preferensi minggu, hari, budget, dan lokasi dalam alur yang ringkas.</p>
        </article>
        <article className="glass-surface rounded-2xl p-5 md:rounded-3xl md:p-6">
          <ChartColumnBig className="h-7 w-7 text-[#6ea3ff]" />
          <h3 className="mt-3 text-xl font-semibold text-white md:mt-4 md:text-2xl">Analitik Real-Time</h3>
          <p className="mt-2 text-base text-slate-300">Pantau statistik peserta, rata-rata budget, dan hari paling memungkinkan.</p>
        </article>
        <article className="glass-surface rounded-2xl p-5 md:rounded-3xl md:p-6">
          <Vote className="h-7 w-7 text-[#6ea3ff]" />
          <h3 className="mt-3 text-xl font-semibold text-white md:mt-4 md:text-2xl">Voting Lokasi</h3>
          <p className="mt-2 text-base text-slate-300">Tentukan tempat favorit bersama lalu kunci tanggal final bukber.</p>
        </article>
      </section>
    </Container>
  );
}
