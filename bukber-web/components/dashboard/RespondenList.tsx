import { Card } from "@/components/ui/Card";
import type { Peserta } from "@/lib/types";
import { formatPreferensiMinggu } from "@/lib/utils";

function getStatus(peserta: Peserta): { label: string; className: string } {
  if (peserta.total_slot_ketersediaan >= 3) {
    return { label: "Bisa", className: "bg-emerald-500/20 text-emerald-300" };
  }
  if (peserta.total_slot_ketersediaan === 2) {
    return { label: "Mungkin", className: "bg-amber-500/20 text-amber-300" };
  }
  return { label: "Tidak", className: "bg-rose-500/20 text-rose-300" };
}

interface RespondenListProps {
  data: Peserta[];
}

export function RespondenList({ data }: RespondenListProps) {
  return (
    <Card className="min-w-0 flex h-full flex-col overflow-hidden p-4 md:p-7">
      <div className="mb-4 flex items-start justify-between gap-3 md:mb-5">
        <h3 className="text-2xl font-semibold leading-tight text-white md:text-4xl">Responden Terbaru</h3>
        <a href="/dashboard#responden-table" className="shrink-0 whitespace-nowrap text-sm text-[#64a0ff] md:text-lg">
          Lihat Semua
        </a>
      </div>

      <div className="min-h-0 flex-1 space-y-3 overflow-x-hidden overflow-y-auto overscroll-x-none pr-1 [scrollbar-gutter:stable]">
        {data.slice(0, 5).map((peserta) => {
          const status = getStatus(peserta);
          const preferensiLabel = formatPreferensiMinggu(peserta.preferensi_minggu);
          return (
            <article
              key={peserta.uuid}
              className="flex w-full min-w-0 max-w-full items-center gap-3 overflow-hidden rounded-2xl border border-white/5 bg-[#1b2b47]/80 px-4 py-3"
            >
              <div className="grid h-11 w-11 shrink-0 place-content-center rounded-full bg-[#325a9c] text-base font-semibold text-white">
                {peserta.nama_lengkap.slice(0, 1)}
              </div>

              <div className="min-w-0 flex-1">
                <p className="truncate text-base font-medium text-slate-100 md:text-2xl">{peserta.nama_lengkap}</p>
                <p className="truncate text-sm text-slate-400 md:text-lg">{preferensiLabel || "Belum memilih preferensi minggu/hari"}</p>
              </div>

              <span className={`shrink-0 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold md:text-sm ${status.className}`}>{status.label}</span>
            </article>
          );
        })}
      </div>
    </Card>
  );
}
