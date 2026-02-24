"use client";

import { useMemo, useState } from "react";
import { Card } from "@/components/ui/Card";
import type { DashboardStats } from "@/lib/types";
import { labelHari } from "@/lib/utils";

interface RekomendasiHariCardProps {
  stats: DashboardStats | null;
}

const PAGE_SIZE = 5;

function sortTransparansi(stats: DashboardStats): DashboardStats["transparansi_hari"] {
  const dayOrder = ["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"];
  return [...stats.transparansi_hari].sort((a, b) => {
    if (a.jumlah_peserta !== b.jumlah_peserta) {
      return b.jumlah_peserta - a.jumlah_peserta;
    }

    if (a.minggu !== b.minggu) {
      return a.minggu - b.minggu;
    }

    return dayOrder.indexOf(a.hari) - dayOrder.indexOf(b.hari);
  });
}

function formatPercent(value: number): string {
  return `${value.toFixed(2)}%`;
}

function tieBreakerLabel(value: "jumlah_peserta_tertinggi" | "minggu_terawal_lalu_hari_terawal"): string {
  return value === "minggu_terawal_lalu_hari_terawal"
    ? "Saat jumlah peserta seri, dipilih minggu terawal lalu urutan hari."
    : "Dipilih berdasarkan jumlah peserta terbanyak.";
}

export function RekomendasiHariCard({ stats }: RekomendasiHariCardProps) {
  const [requestedPage, setRequestedPage] = useState(1);
  const rekomendasi = stats?.rekomendasi_hari ?? null;
  const ranking = useMemo(() => (stats ? sortTransparansi(stats) : []), [stats]);
  const totalPages = Math.max(1, Math.ceil(ranking.length / PAGE_SIZE));
  const page = Math.min(requestedPage, totalPages);

  const pagedRanking = useMemo(() => {
    const start = (page - 1) * PAGE_SIZE;
    return ranking.slice(start, start + PAGE_SIZE);
  }, [page, ranking]);

  const startRank = ranking.length > 0 ? (page - 1) * PAGE_SIZE + 1 : 0;
  const endRank = Math.min(page * PAGE_SIZE, ranking.length);

  if (!stats || !rekomendasi) {
    return (
      <Card className="p-4 md:p-8">
        <h3 className="text-2xl font-semibold text-white md:text-4xl">Transparansi Rekomendasi Hari</h3>
        <p className="mt-2 text-sm text-slate-300 md:text-lg">Data peserta belum cukup untuk menyusun rekomendasi hari yang adil.</p>
      </Card>
    );
  }

  return (
    <Card className="p-4 md:p-8">
      <div>
        <h3 className="text-2xl font-semibold text-white md:text-4xl">Transparansi Rekomendasi Hari</h3>
        <p className="mt-2 text-sm text-slate-300 md:text-lg">
          Metode: kombinasi minggu dan hari dengan jumlah peserta terbanyak (tanpa perhitungan budget).
        </p>
        <p className="mt-2 text-sm text-slate-400">{tieBreakerLabel(rekomendasi.tie_breaker)}</p>
      </div>

      <div className="mt-5 space-y-2.5 md:hidden">
        {pagedRanking.map((item, index) => (
          <article key={`${item.minggu}-${item.hari}`} className="rounded-xl border border-white/8 bg-[#132542] px-3 py-3 text-sm text-slate-200">
            <p className="text-xs text-slate-400">Ranking #{startRank + index}</p>
            <p className="mt-1 text-base font-semibold text-white">Minggu {item.minggu} • {labelHari(item.hari)}</p>
            <p className="mt-1 text-slate-300">{item.jumlah_peserta} orang • {formatPercent(item.persentase_peserta)}</p>
          </article>
        ))}
      </div>

      <div className="mt-5 hidden overflow-x-auto md:block">
        <table className="min-w-full text-left text-sm text-slate-200">
          <thead className="bg-[#132542] text-slate-300">
            <tr>
              <th className="rounded-l-xl px-4 py-3 font-medium">Ranking</th>
              <th className="px-4 py-3 font-medium">Minggu</th>
              <th className="px-4 py-3 font-medium">Hari</th>
              <th className="px-4 py-3 font-medium">Peserta Bisa</th>
              <th className="px-4 py-3 font-medium">Cakupan</th>
              <th className="rounded-r-xl px-4 py-3 font-medium">Metode</th>
            </tr>
          </thead>
          <tbody>
            {pagedRanking.map((item, index) => (
              <tr key={`${item.minggu}-${item.hari}`} className="border-t border-white/8">
                <td className="px-4 py-3 text-slate-300">#{startRank + index}</td>
                <td className="px-4 py-3">{item.minggu}</td>
                <td className="px-4 py-3 font-medium text-white">{labelHari(item.hari)}</td>
                <td className="px-4 py-3">{item.jumlah_peserta} orang</td>
                <td className="px-4 py-3">{formatPercent(item.persentase_peserta)}</td>
                <td className="px-4 py-3">Jumlah peserta</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="mt-4 flex flex-col gap-3 md:mt-5 md:flex-row md:items-center md:justify-between">
        <p className="text-xs text-slate-400 md:text-sm">
          Menampilkan ranking {startRank}-{endRank} dari {ranking.length}.
        </p>
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={() => setRequestedPage((value) => Math.max(1, value - 1))}
            disabled={page === 1}
            className="h-9 rounded-full border border-[#355180] px-4 text-xs text-slate-100 transition enabled:hover:bg-[#1b3561] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
          >
            Prev
          </button>
          <span className="rounded-full border border-[#355180] bg-[#13284d] px-3 py-1 text-xs text-slate-200 md:text-sm">
            Hal {page}/{totalPages}
          </span>
          <button
            type="button"
            onClick={() => setRequestedPage((value) => Math.min(totalPages, value + 1))}
            disabled={page === totalPages}
            className="h-9 rounded-full border border-[#355180] px-4 text-xs text-slate-100 transition enabled:hover:bg-[#1b3561] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
          >
            Next
          </button>
        </div>
      </div>
    </Card>
  );
}
