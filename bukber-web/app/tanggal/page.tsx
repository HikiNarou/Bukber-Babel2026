"use client";

import { useEffect, useMemo, useState } from "react";
import { AlertTriangle } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { AnnouncementCard } from "@/components/tanggal/AnnouncementCard";
import { ShareButtons } from "@/components/tanggal/ShareButtons";
import { Card } from "@/components/ui/Card";
import { getTanggalFinal, parseApiError } from "@/lib/api";
import type { TanggalFinal } from "@/lib/types";
import { formatDateIndonesia, formatRupiah } from "@/lib/utils";

export default function TanggalPage() {
  const [data, setData] = useState<TanggalFinal | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const response = await getTanggalFinal();
        setData(response.data);
        setError(null);
      } catch (requestError) {
        setError(parseApiError(requestError));
      } finally {
        setLoading(false);
      }
    }

    void load();
  }, []);

  const shareText = useMemo(() => {
    if (!data || !data.is_locked) {
      return "Tanggal final bukber belum ditentukan. Pantau update terbaru di BukberYuk.";
    }
    return `Tanggal bukber final: ${formatDateIndonesia(data.tanggal)} pukul ${data.jam || "18:00"} di ${
      data.lokasi?.nama_tempat ?? "lokasi terpilih"
    }. Estimasi budget ${formatRupiah(data.estimasi_budget)}.`;
  }, [data]);

  return (
    <Container className="py-8 md:py-11">
      {loading ? <Card className="p-6 text-slate-300">Memuat tanggal final...</Card> : null}
      {error ? <Card className="p-6 text-rose-200">{error}</Card> : null}
      {!loading && !error && data && data.is_locked ? <AnnouncementCard data={data} /> : null}

      {!loading && !error && data && !data.is_locked ? (
        <Card className="flex items-start gap-3 p-4 md:p-6">
          <AlertTriangle className="mt-0.5 h-6 w-6 text-amber-300" />
          <div>
            <p className="text-lg font-semibold text-white md:text-xl">Tanggal final belum ditentukan.</p>
            <p className="mt-1 text-sm text-slate-300 md:text-base">Panitia masih menunggu finalisasi voting dan ketersediaan peserta.</p>
          </div>
        </Card>
      ) : null}

      {!loading && !error ? (
        <div className="mt-5">
          <ShareButtons text={shareText} />
        </div>
      ) : null}
    </Container>
  );
}
