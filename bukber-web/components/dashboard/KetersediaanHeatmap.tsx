import { Card } from "@/components/ui/Card";
import type { ChartHariItem } from "@/lib/types";
import { labelHari } from "@/lib/utils";

interface KetersediaanHeatmapProps {
  data: ChartHariItem[];
}

const hourLabels = ["16:00", "17:00", "18:00"];

function intensityClass(value: number): string {
  if (value >= 0.75) return "bg-[#2f6df2]";
  if (value >= 0.45) return "bg-[#204da5]";
  return "bg-[#1a2942]";
}

export function KetersediaanHeatmap({ data }: KetersediaanHeatmapProps) {
  const max = Math.max(...data.map((item) => item.jumlah), 1);

  return (
    <Card className="p-4 md:p-8">
      <div className="mb-4 flex items-center justify-between md:mb-6">
        <div>
          <h3 className="text-2xl font-semibold text-white md:text-4xl">Detail Ketersediaan</h3>
          <p className="mt-1 text-sm text-slate-400 md:text-xl">Heatmap ketersediaan jam per hari</p>
        </div>
        <div className="hidden items-center gap-2 text-sm text-slate-300 md:flex">
          <span className="h-3 w-3 rounded-full bg-[#1a2942]" />
          Low
          <span className="h-3 w-3 rounded-full bg-[#204da5]" />
          Med
          <span className="h-3 w-3 rounded-full bg-[#2f6df2]" />
          High
        </div>
      </div>

      <div className="space-y-2.5 md:hidden">
        <div className="flex items-center gap-2 text-xs text-slate-300">
          <span className="h-2.5 w-2.5 rounded-full bg-[#1a2942]" />
          Low
          <span className="h-2.5 w-2.5 rounded-full bg-[#204da5]" />
          Med
          <span className="h-2.5 w-2.5 rounded-full bg-[#2f6df2]" />
          High
        </div>
        {data.map((item) => {
          const normalized = item.jumlah / max;
          return (
            <article key={item.hari} className="rounded-xl border border-white/8 bg-[#132542] p-3">
              <div className="mb-2 flex items-center justify-between">
                <p className="text-sm font-semibold text-white">{labelHari(item.hari)}</p>
                <p className="text-xs text-slate-400">{item.jumlah} peserta</p>
              </div>
              <div className="grid grid-cols-3 gap-2">
                {hourLabels.map((label, rowIndex) => {
                  const adjusted = normalized * (rowIndex === 2 ? 1 : rowIndex === 1 ? 0.85 : 0.6);
                  return (
                    <div key={`${item.hari}-${label}`} className="space-y-1">
                      <p className="text-[11px] text-slate-400">{label}</p>
                      <div className={`h-8 rounded-lg ${intensityClass(adjusted)}`} />
                    </div>
                  );
                })}
              </div>
            </article>
          );
        })}
      </div>

      <div className="hidden overflow-x-auto md:block">
        <div className="min-w-[680px] space-y-3">
          <div className="grid grid-cols-8 gap-2 text-sm text-slate-400">
            <span className="px-2">Jam</span>
            {data.map((item) => (
              <span key={item.hari} className="text-center">
                {labelHari(item.hari)}
              </span>
            ))}
          </div>

          {hourLabels.map((label, rowIndex) => (
            <div key={label} className="grid grid-cols-8 gap-2">
              <span className="px-2 py-2 text-sm text-slate-400">{label}</span>
              {data.map((item) => {
                const normalized = item.jumlah / max;
                const adjusted = normalized * (rowIndex === 2 ? 1 : rowIndex === 1 ? 0.85 : 0.6);
                return <div key={`${item.hari}-${label}`} className={`h-10 rounded-lg ${intensityClass(adjusted)}`} />;
              })}
            </div>
          ))}
        </div>
      </div>
    </Card>
  );
}
