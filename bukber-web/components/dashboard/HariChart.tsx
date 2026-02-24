"use client";

import { Bar, BarChart, Cell, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { Card } from "@/components/ui/Card";
import type { ChartHariItem } from "@/lib/types";
import { labelHari } from "@/lib/utils";

const baseColors = ["#334A6A", "#334A6A", "#1F4AA8", "#334A6A", "#2659C6", "#2F6DF2", "#2961D4"];

interface HariChartProps {
  data: ChartHariItem[];
}

export function HariChart({ data }: HariChartProps) {
  return (
    <Card className="flex h-full flex-col p-4 md:p-8">
      <div className="mb-5 flex items-end justify-between md:mb-8">
        <div>
          <h3 className="text-2xl font-semibold text-white md:text-4xl">Hari Paling Banyak Bisa</h3>
          <p className="mt-1 text-sm text-slate-400 md:mt-2 md:text-xl">Akumulasi ketersediaan peserta lintas minggu</p>
        </div>
      </div>

      <div className="h-[280px] w-full md:h-[380px] lg:h-auto lg:min-h-0 lg:flex-1">
        <ResponsiveContainer width="100%" height="100%">
          <BarChart data={data}>
            <XAxis
              dataKey="hari"
              tickLine={false}
              axisLine={false}
              tick={{ fill: "#9db2cf", fontSize: 14 }}
              tickFormatter={(value: string) => labelHari(value as ChartHariItem["hari"])}
            />
            <YAxis tickLine={false} axisLine={false} tick={{ fill: "#6f86aa", fontSize: 12 }} width={35} />
            <Tooltip
              cursor={{ fill: "rgba(47, 109, 242, 0.14)" }}
              contentStyle={{
                background: "#0f1d35",
                border: "1px solid #2b4673",
                borderRadius: "12px",
                color: "#e2e8f0",
              }}
              formatter={(value) => [`${Number(value ?? 0)} orang`, "Jumlah"]}
              labelFormatter={(label) => labelHari(String(label) as ChartHariItem["hari"])}
            />
            <Bar dataKey="jumlah" radius={[14, 14, 0, 0]}>
              {data.map((item, index) => (
                <Cell key={item.hari} fill={baseColors[index] ?? "#3B82F6"} />
              ))}
            </Bar>
          </BarChart>
        </ResponsiveContainer>
      </div>
    </Card>
  );
}
