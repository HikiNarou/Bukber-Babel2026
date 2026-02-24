import { CalendarDays, Clock3, MapPin } from "lucide-react";
import { Card } from "@/components/ui/Card";
import type { TanggalFinal } from "@/lib/types";
import { formatDateIndonesia, formatRupiah } from "@/lib/utils";

interface AnnouncementCardProps {
  data: TanggalFinal;
}

export function AnnouncementCard({ data }: AnnouncementCardProps) {
  return (
    <Card className="overflow-hidden">
      <div className="bg-[linear-gradient(100deg,#1f4aa3_0%,#2f6df2_55%,#173b82_100%)] px-5 py-6 md:px-10 md:py-8">
        <h2 className="text-2xl font-semibold text-white sm:text-3xl md:text-5xl">Pengumuman Tanggal Bukber</h2>
        <p className="mt-2 text-sm text-blue-100 md:text-xl">Tanggal final sudah ditetapkan berdasarkan voting dan preferensi peserta.</p>
      </div>

      <div className="space-y-5 px-4 py-5 md:px-10 md:py-8">
        <div className="grid gap-4 md:grid-cols-3">
          <div className="rounded-2xl border border-white/10 bg-[#142642] p-4">
            <p className="mb-2 flex items-center gap-2 text-sm text-slate-400">
              <CalendarDays className="h-4 w-4" />
              Tanggal
            </p>
            <p className="text-base font-semibold text-white md:text-lg">{formatDateIndonesia(data.tanggal)}</p>
          </div>
          <div className="rounded-2xl border border-white/10 bg-[#142642] p-4">
            <p className="mb-2 flex items-center gap-2 text-sm text-slate-400">
              <Clock3 className="h-4 w-4" />
              Jam Kumpul
            </p>
            <p className="text-base font-semibold text-white md:text-lg">{data.jam || "18:00"}</p>
          </div>
          <div className="rounded-2xl border border-white/10 bg-[#142642] p-4">
            <p className="mb-2 text-sm text-slate-400">Estimasi Budget</p>
            <p className="text-base font-semibold text-white md:text-lg">{formatRupiah(data.estimasi_budget)}</p>
          </div>
        </div>

        <div className="rounded-2xl border border-white/10 bg-[#142642] p-5">
          <p className="mb-2 flex items-center gap-2 text-sm text-slate-400">
            <MapPin className="h-4 w-4" />
            Lokasi Terpilih
          </p>
          <p className="text-lg font-semibold text-white md:text-xl">{data.lokasi?.nama_tempat || "-"}</p>
          <p className="mt-1 text-sm text-slate-300 md:text-base">{data.lokasi?.alamat || "Alamat belum tersedia."}</p>
        </div>

        <div className="rounded-2xl border border-white/10 bg-[#142642] p-5">
          <p className="text-sm text-slate-400">Catatan</p>
          <p className="mt-1 text-sm text-slate-100 md:text-base">{data.catatan || "Datang 30 menit sebelum berbuka untuk koordinasi panitia."}</p>
        </div>
      </div>
    </Card>
  );
}
