"use client";

import { useEffect, useMemo, useState } from "react";
import { BarChart3, CalendarCheck2, Users, Wallet } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { StatCard } from "@/components/dashboard/StatCard";
import { HariChart } from "@/components/dashboard/HariChart";
import { RespondenList } from "@/components/dashboard/RespondenList";
import { RespondenTable } from "@/components/dashboard/RespondenTable";
import { KetersediaanHeatmap } from "@/components/dashboard/KetersediaanHeatmap";
import { RekomendasiHariCard } from "@/components/dashboard/RekomendasiHariCard";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { getChartHari, getDashboardStats, getResponden, parseApiError } from "@/lib/api";
import type { ChartHariItem, DashboardStats, Peserta, RespondenAvailability, RespondenFilter } from "@/lib/types";
import { formatCompactRupiah, formatDateTimeAgo, labelHari } from "@/lib/utils";

const RESPONDEN_LIST_LIMIT = 5;
const RESPONDEN_TABLE_LIMIT = 25;

export default function DashboardPage() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [chartHari, setChartHari] = useState<ChartHariItem[]>([]);
  const [responden, setResponden] = useState<Peserta[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [lastUpdated, setLastUpdated] = useState<string>(new Date().toISOString());
  const [tableExpanded, setTableExpanded] = useState(false);
  const [tableFilter, setTableFilter] = useState<RespondenFilter>("all");
  const [tableResponden, setTableResponden] = useState<Peserta[]>([]);
  const [tableLoading, setTableLoading] = useState(false);
  const [tableError, setTableError] = useState<string | null>(null);

  useEffect(() => {
    let active = true;
    async function load() {
      setLoading(true);
      setError(null);

      try {
        const [statsRes, chartRes, respondenRes] = await Promise.all([
          getDashboardStats(),
          getChartHari(),
          getResponden({ per_page: RESPONDEN_LIST_LIMIT }),
        ]);
        if (!active) return;
        setStats(statsRes.data);
        setChartHari(chartRes.data);
        setResponden(respondenRes.data);
        setLastUpdated(new Date().toISOString());
      } catch (requestError) {
        if (!active) return;
        setError(parseApiError(requestError));
      } finally {
        if (!active) return;
        setLoading(false);
      }
    }

    void load();
    return () => {
      active = false;
    };
  }, []);

  useEffect(() => {
    let active = true;

    if (!tableExpanded) {
      return () => {
        active = false;
      };
    }

    async function loadFilteredResponden() {
      setTableLoading(true);
      setTableError(null);

      try {
        const availability: RespondenAvailability | undefined = tableFilter === "all" ? undefined : tableFilter;
        const response = await getResponden({ per_page: RESPONDEN_TABLE_LIMIT, availability });
        if (!active) return;
        setTableResponden(response.data);
      } catch (requestError) {
        if (!active) return;
        setTableError(parseApiError(requestError));
      } finally {
        if (!active) return;
        setTableLoading(false);
      }
    }

    void loadFilteredResponden();

    return () => {
      active = false;
    };
  }, [tableExpanded, tableFilter]);

  const mingguFavorit = useMemo(() => {
    if (!stats?.minggu_terfavorit) return "-";
    return `Minggu Ke-${stats.minggu_terfavorit.minggu}`;
  }, [stats?.minggu_terfavorit]);

  const rekomendasiHari = stats?.rekomendasi_hari ?? null;
  const rekomendasiHariLabel = rekomendasiHari ? labelHari(rekomendasiHari.hari) : null;
  const rekomendasiBudget = rekomendasiHari?.rata_rata_budget ?? stats?.rata_rata_budget ?? 0;
  const rekomendasiSubtitle = rekomendasiHari
    ? `Rekomendasi saat ini: ${rekomendasiHariLabel} • ${rekomendasiHari.jumlah_peserta} peserta (${rekomendasiHari.persentase_peserta.toFixed(2)}%) • budget ${formatCompactRupiah(rekomendasiBudget)}.`
    : "Belum ada data yang cukup untuk menentukan hari rekomendasi.";

  return (
    <Container className="py-7 md:py-10">
      <section className="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <h1 className="text-3xl font-semibold text-white sm:text-4xl md:text-6xl">Kesimpulan Bukber 2026</h1>
          <p className="mt-2 text-sm text-slate-300 md:text-xl">
            Analisa data dari <span className="font-semibold text-[#72a6ff]">{stats?.total_peserta ?? responden.length} responden</span> teman-teman.
          </p>
        </div>
        <span className="inline-flex items-center rounded-full border border-[#37527f] bg-[#12274a] px-4 py-2 text-sm text-slate-300">
          Updated {formatDateTimeAgo(lastUpdated)}
        </span>
      </section>

      {error ? (
        <Card className="mb-6 p-5 text-rose-200">{error}</Card>
      ) : null}

      <section className="grid gap-4 md:grid-cols-3">
        <StatCard icon={<Users className="h-6 w-6" />} label="TOTAL PESERTA" value={`${stats?.total_peserta ?? 0} Orang`} helper="↑ +5 dari minggu lalu" />
        <StatCard
          icon={<Wallet className="h-6 w-6" />}
          label="RATA-RATA BUDGET"
          value={formatCompactRupiah(stats?.rata_rata_budget ?? 0)}
          helper="Per orang (estimasi)"
          accent="green"
        />
        <StatCard
          icon={<CalendarCheck2 className="h-6 w-6" />}
          label="MINGGU TERFAVORIT"
          value={mingguFavorit}
          helper={`${stats?.minggu_terfavorit?.jumlah_peserta ?? 0} votes`}
          accent="violet"
        />
      </section>

      {loading ? (
        <Card className="mt-6 p-5 text-sm text-slate-300 md:p-8 md:text-base">Memuat data dashboard...</Card>
      ) : (
        <>
          <section className="mt-6 grid gap-4 lg:h-[560px] lg:grid-cols-[minmax(0,1fr)_minmax(0,360px)] lg:items-stretch lg:[&>*]:min-w-0">
            <HariChart data={chartHari} />
            <RespondenList data={responden} />
          </section>

          <section className="mt-6">
            <KetersediaanHeatmap data={chartHari} />
          </section>

          <section className="mt-6">
            <RekomendasiHariCard stats={stats} />
          </section>

          <section className="mt-6">
            <RespondenTable
              data={tableResponden}
              isExpanded={tableExpanded}
              isLoading={tableLoading}
              error={tableError}
              filter={tableFilter}
              onToggleExpand={() => setTableExpanded((value) => !value)}
              onFilterChange={setTableFilter}
            />
          </section>
        </>
      )}

      <Card className="mt-7 flex flex-col items-start justify-between gap-4 bg-[linear-gradient(90deg,#2759c7_0%,#2f6df2_55%,#295ecf_100%)] px-4 py-4 md:flex-row md:items-center md:px-6 md:py-5">
        <div>
          <p className="text-2xl font-semibold text-white md:text-3xl">Siap menentukan tanggal?</p>
          <p className="mt-1 text-sm text-blue-100 md:text-lg">{rekomendasiSubtitle}</p>
        </div>
        <div className="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:gap-3">
          <Button variant="secondary" className="w-full sm:w-auto">
            Diskusi Dulu
          </Button>
          <Button disabled={!rekomendasiHari} className="w-full sm:w-auto">
            <BarChart3 className="h-4 w-4" />
            {rekomendasiHariLabel ? `Tetapkan ${rekomendasiHariLabel}` : "Tetapkan Hari"}
          </Button>
        </div>
      </Card>
    </Container>
  );
}
