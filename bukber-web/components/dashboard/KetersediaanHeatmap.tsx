import { Card } from "@/components/ui/Card";
import type { DashboardStats, Hari } from "@/lib/types";
import { labelHari } from "@/lib/utils";

interface KetersediaanHeatmapProps {
  data: DashboardStats["detail_ketersediaan"];
}

const dayOrder: Hari[] = ["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"];

function intensityClass(percentage: number): string {
  if (percentage >= 60) return "bg-[#2f6df2]";
  if (percentage >= 35) return "bg-[#204da5]";
  return "bg-[#1a2942]";
}

function formatPercent(value: number): string {
  return `${value.toFixed(1)}%`;
}

function barWidth(value: number): string {
  const safeValue = Math.max(4, Math.min(100, Number.isFinite(value) ? value : 0));
  return `${safeValue}%`;
}

export function KetersediaanHeatmap({ data }: KetersediaanHeatmapProps) {
  const rows = data.length > 0 ? data : [1, 2, 3, 4].map((minggu) => ({ minggu, hari: dayOrder.map((hari) => ({ hari, jumlah_peserta: 0, persentase_peserta: 0 })) }));

  return (
    <Card className="p-4 md:p-8">
      <div className="mb-4 flex items-center justify-between md:mb-6">
        <div>
          <h3 className="text-2xl font-semibold text-white md:text-4xl">Detail Ketersediaan</h3>
          <p className="mt-1 text-sm text-slate-400 md:text-xl">Heatmap ketersediaan per minggu dan hari</p>
        </div>
        <div className="hidden items-center gap-2 text-sm text-slate-300 lg:flex">
          <span className="h-3 w-3 rounded-full bg-[#1a2942]" />
          Low
          <span className="h-3 w-3 rounded-full bg-[#204da5]" />
          Med
          <span className="h-3 w-3 rounded-full bg-[#2f6df2]" />
          High
        </div>
      </div>

      <div className="space-y-3 lg:hidden">
        <div className="flex items-center gap-2 text-xs text-slate-300">
          <span className="h-2.5 w-2.5 rounded-full bg-[#1a2942]" />
          Low
          <span className="h-2.5 w-2.5 rounded-full bg-[#204da5]" />
          Med
          <span className="h-2.5 w-2.5 rounded-full bg-[#2f6df2]" />
          High
        </div>

        {rows.map((row) => {
          const totalPesertaMinggu = row.hari.reduce((sum, cell) => sum + cell.jumlah_peserta, 0);

          return (
            <details key={row.minggu} className="group rounded-xl border border-white/8 bg-[#132542] p-3.5">
              <summary className="flex list-none cursor-pointer items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
                <div className="min-w-0">
                  <p className="text-sm font-semibold text-white">Minggu {row.minggu}</p>
                  <p className="text-[11px] text-slate-400">Tap untuk lihat detail</p>
                </div>
                <div className="flex items-center gap-2">
                  <p className="text-xs text-slate-400">{totalPesertaMinggu} total slot</p>
                  <span className="text-xs text-slate-300 transition-transform group-open:rotate-180">▾</span>
                </div>
              </summary>

              <div className="mt-3 space-y-2 border-t border-white/8 pt-3">
                {row.hari.map((cell) => {
                  return (
                    <div key={`${row.minggu}-${cell.hari}`} className="rounded-lg border border-white/6 bg-[#0f203b] p-2.5">
                      <div className="flex items-center justify-between gap-2">
                        <p className="text-xs font-medium text-slate-200">{labelHari(cell.hari)}</p>
                        <p className="text-xs text-slate-200">
                          <span className="font-semibold text-white">{cell.jumlah_peserta}</span> peserta
                        </p>
                      </div>

                      <div className="mt-1.5 flex items-center gap-2">
                        <div className="h-2 flex-1 rounded-full bg-[#09182f]">
                          <div
                            className={`h-full rounded-full ${intensityClass(cell.persentase_peserta)}`}
                            style={{ width: barWidth(cell.persentase_peserta) }}
                          />
                        </div>
                        <span className="w-11 shrink-0 text-right text-[11px] font-medium text-slate-100">
                          {formatPercent(cell.persentase_peserta)}
                        </span>
                      </div>
                    </div>
                  );
                })}
              </div>
            </details>
          );
        })}
      </div>

      <div className="hidden lg:block">
        <div className="overflow-x-auto">
          <table className="w-full min-w-[860px] table-fixed border-separate border-spacing-2">
            <thead>
              <tr>
                <th className="w-[15%] px-2 py-1 text-left text-sm font-medium text-slate-400">Minggu</th>
                {dayOrder.map((hari) => (
                  <th key={hari} className="px-2 py-1 text-center text-sm font-medium text-slate-400">
                    {labelHari(hari)}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody>
              {rows.map((row) => (
                <tr key={row.minggu}>
                  <td className="rounded-lg bg-[#132542] px-3 py-2 text-sm font-semibold text-white">Minggu {row.minggu}</td>
                  {dayOrder.map((hari) => {
                    const cell = row.hari.find((item) => item.hari === hari) ?? {
                      hari,
                      jumlah_peserta: 0,
                      persentase_peserta: 0,
                    };

                    return (
                      <td key={`${row.minggu}-${hari}`} className={`rounded-lg px-2 py-2 text-center text-sm text-slate-100 ${intensityClass(cell.persentase_peserta)}`}>
                        <p className="font-semibold">{cell.jumlah_peserta}</p>
                        <p className="text-[11px] text-slate-200/85">{formatPercent(cell.persentase_peserta)}</p>
                      </td>
                    );
                  })}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </Card>
  );
}
