import { Card } from "@/components/ui/Card";
import type { DashboardStats } from "@/lib/types";
import { formatCompactRupiah, labelHari } from "@/lib/utils";

interface RekomendasiHariCardProps {
  stats: DashboardStats | null;
}

function sortTransparansi(stats: DashboardStats): DashboardStats["transparansi_hari"] {
  const dayOrder = ["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"];
  return [...stats.transparansi_hari].sort((a, b) => {
    if (a.jumlah_peserta !== b.jumlah_peserta) {
      return b.jumlah_peserta - a.jumlah_peserta;
    }

    const budgetA = a.rata_rata_budget ?? Number.MAX_SAFE_INTEGER;
    const budgetB = b.rata_rata_budget ?? Number.MAX_SAFE_INTEGER;
    if (budgetA !== budgetB) {
      return budgetA - budgetB;
    }

    return dayOrder.indexOf(a.hari) - dayOrder.indexOf(b.hari);
  });
}

function formatPercent(value: number): string {
  return `${value.toFixed(2)}%`;
}

function tieBreakerLabel(value: "jumlah_peserta_tertinggi" | "budget_terendah"): string {
  return value === "budget_terendah"
    ? "Saat jumlah peserta seri, dipilih hari dengan rata-rata budget lebih rendah."
    : "Dipilih berdasarkan jumlah peserta terbanyak.";
}

export function RekomendasiHariCard({ stats }: RekomendasiHariCardProps) {
  if (!stats || !stats.rekomendasi_hari) {
    return (
      <Card className="p-4 md:p-8">
        <h3 className="text-2xl font-semibold text-white md:text-4xl">Transparansi Rekomendasi Hari</h3>
        <p className="mt-2 text-sm text-slate-300 md:text-lg">Data peserta belum cukup untuk menyusun rekomendasi hari yang adil.</p>
      </Card>
    );
  }

  const rekomendasi = stats.rekomendasi_hari;
  const ranking = sortTransparansi(stats);

  return (
    <Card className="p-4 md:p-8">
      <div>
        <h3 className="text-2xl font-semibold text-white md:text-4xl">Transparansi Rekomendasi Hari</h3>
        <p className="mt-2 text-sm text-slate-300 md:text-lg">
          Metode: jumlah peserta terbanyak. Jika seri, gunakan rata-rata budget terendah.
        </p>
        <p className="mt-2 text-sm text-slate-400">{tieBreakerLabel(rekomendasi.tie_breaker)}</p>
      </div>

      <div className="mt-5 space-y-2.5 md:hidden">
        {ranking.map((item, index) => (
          <article key={item.hari} className="rounded-xl border border-white/8 bg-[#132542] px-3 py-3 text-sm text-slate-200">
            <p className="text-xs text-slate-400">Ranking #{index + 1}</p>
            <p className="mt-1 text-base font-semibold text-white">{labelHari(item.hari)}</p>
            <p className="mt-1 text-slate-300">{item.jumlah_peserta} orang • {formatPercent(item.persentase_peserta)}</p>
            <p className="mt-1 text-slate-400">
              Budget: {item.rata_rata_budget !== null ? `Rp${Math.round(item.rata_rata_budget / 1000)}rb` : "-"}
            </p>
          </article>
        ))}
      </div>

      <div className="mt-5 hidden overflow-x-auto md:block">
        <table className="min-w-full text-left text-sm text-slate-200">
          <thead className="bg-[#132542] text-slate-300">
            <tr>
              <th className="rounded-l-xl px-4 py-3 font-medium">Ranking</th>
              <th className="px-4 py-3 font-medium">Hari</th>
              <th className="px-4 py-3 font-medium">Peserta Bisa</th>
              <th className="px-4 py-3 font-medium">Cakupan</th>
              <th className="rounded-r-xl px-4 py-3 font-medium">Rata-rata Budget</th>
            </tr>
          </thead>
          <tbody>
            {ranking.map((item, index) => (
              <tr key={item.hari} className="border-t border-white/8">
                <td className="px-4 py-3 text-slate-300">#{index + 1}</td>
                <td className="px-4 py-3 font-medium text-white">{labelHari(item.hari)}</td>
                <td className="px-4 py-3">{item.jumlah_peserta} orang</td>
                <td className="px-4 py-3">{formatPercent(item.persentase_peserta)}</td>
                <td className="px-4 py-3">
                  {item.rata_rata_budget !== null ? formatCompactRupiah(item.rata_rata_budget) : "-"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </Card>
  );
}
