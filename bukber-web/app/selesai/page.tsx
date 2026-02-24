"use client";

import Link from "next/link";
import { useEffect, useMemo, useState } from "react";
import { CheckCircle2 } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { ConfettiOverlay } from "@/components/decorative/ConfettiOverlay";
import { getRegistrasiByUuid } from "@/lib/api";
import { REGISTRATION_RESULT_KEY } from "@/lib/constants";
import type { Peserta } from "@/lib/types";
import { formatPreferensiMinggu, formatRupiah } from "@/lib/utils";

export default function SelesaiPage() {
  const [uuid] = useState<string | null>(() => {
    if (typeof window === "undefined") {
      return null;
    }
    return new URLSearchParams(window.location.search).get("uuid");
  });
  const [data, setData] = useState<Peserta | null>(() => {
    if (typeof window === "undefined") {
      return null;
    }
    const raw = localStorage.getItem(REGISTRATION_RESULT_KEY);
    if (!raw) {
      return null;
    }
    try {
      return JSON.parse(raw) as Peserta;
    } catch {
      return null;
    }
  });

  useEffect(() => {
    if (!uuid || data?.uuid === uuid) {
      return;
    }

    getRegistrasiByUuid(uuid)
      .then((response) => setData(response.data))
      .catch(() => undefined);
  }, [data?.uuid, uuid]);

  const shareText = useMemo(() => {
    if (!data) {
      return "Aku sudah daftar Bukber 2026! Yuk ikutan juga.";
    }
    const preferensiLabel = formatPreferensiMinggu(data.preferensi_minggu) || "Belum terisi";
    return `Aku sudah daftar Bukber 2026. Preferensi: ${preferensiLabel}. Budget ${formatRupiah(data.budget_per_orang)}. Yuk daftar juga di BukberYuk!`;
  }, [data]);

  return (
    <Container className="py-8 md:py-12">
      <Card className="relative mx-auto max-w-3xl overflow-hidden p-4 md:p-9">
        <ConfettiOverlay />
        <div className="relative z-10">
          <div className="mx-auto grid h-14 w-14 place-content-center rounded-full bg-emerald-500/20 text-emerald-300 md:h-16 md:w-16">
            <CheckCircle2 className="h-8 w-8 md:h-9 md:w-9" />
          </div>
          <h1 className="mt-4 text-center text-2xl font-semibold text-white sm:text-3xl md:text-5xl">Pendaftaran Berhasil</h1>
          <p className="mt-2 text-center text-sm text-slate-300 md:text-lg">
            Terima kasih sudah konfirmasi. Sampai jumpa di acara bukber!
          </p>

          <div className="mt-7 rounded-2xl border border-white/10 bg-[#142742] p-4">
            <p className="text-sm text-slate-400">Ringkasan Data</p>
            {data ? (
              <div className="mt-3 space-y-2 text-slate-100">
                <p>
                  <span className="text-slate-400">Nama:</span> {data.nama_lengkap}
                </p>
                <p>
                  <span className="text-slate-400">Preferensi:</span> {formatPreferensiMinggu(data.preferensi_minggu) || "-"}
                </p>
                <p>
                  <span className="text-slate-400">Budget:</span> {formatRupiah(data.budget_per_orang)}
                </p>
                <p>
                  <span className="text-slate-400">Lokasi:</span> {data.lokasi?.nama_tempat || "-"}
                </p>
              </div>
            ) : (
              <p className="mt-3 text-sm text-slate-300">Data ringkasan belum tersedia.</p>
            )}
          </div>

          <div className="mt-6 flex flex-col gap-3 md:flex-row">
            <a
              href={`https://wa.me/?text=${encodeURIComponent(shareText)}`}
              target="_blank"
              rel="noreferrer"
              className="flex-1"
            >
              <Button fullWidth variant="secondary">
                Bagikan ke WhatsApp
              </Button>
            </a>
            <Link href="/dashboard" className="flex-1">
              <Button fullWidth>Lihat Dashboard</Button>
            </Link>
          </div>
        </div>
      </Card>
    </Container>
  );
}
