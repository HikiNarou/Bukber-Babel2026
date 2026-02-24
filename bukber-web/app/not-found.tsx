import Link from "next/link";
import { Container } from "@/components/layout/Container";
import { Button } from "@/components/ui/Button";

export default function NotFound() {
  return (
    <Container className="py-10 md:py-16">
      <div className="mx-auto max-w-xl rounded-2xl border border-[#2f4260] bg-[#12223b] p-6 text-center md:rounded-3xl md:p-8">
        <h1 className="text-2xl font-semibold text-white sm:text-3xl md:text-4xl">Halaman Tidak Ditemukan</h1>
        <p className="mt-3 text-sm text-slate-300 md:text-lg">
          URL yang Anda buka tidak tersedia. Kembali ke dashboard untuk melihat data bukber.
        </p>
        <Link href="/dashboard" className="mt-6 inline-block">
          <Button>Lihat Dashboard</Button>
        </Link>
      </div>
    </Container>
  );
}
