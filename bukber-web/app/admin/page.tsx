"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { CalendarClock, Settings2, UsersRound } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { adminGetSettings, getResponden, getTanggalFinal, parseApiError } from "@/lib/api";
import type { EventSetting, TanggalFinal } from "@/lib/types";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { formatDateIndonesia } from "@/lib/utils";

export default function AdminPage() {
  const router = useRouter();
  const { token, clearToken, hydrated } = useAdminAuth();
  const [settings, setSettings] = useState<EventSetting | null>(null);
  const [totalPeserta, setTotalPeserta] = useState(0);
  const [tanggal, setTanggal] = useState<TanggalFinal | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!hydrated) {
      return;
    }
    if (!token) {
      router.replace("/admin/login");
      return;
    }

    async function load() {
      setError(null);
      try {
        const [settingsRes, pesertaRes, tanggalRes] = await Promise.all([
          adminGetSettings(token),
          getResponden({ per_page: 1 }),
          getTanggalFinal(),
        ]);
        setSettings(settingsRes.data);
        setTotalPeserta(pesertaRes.meta.total);
        setTanggal(tanggalRes.data);
      } catch (requestError) {
        setError(parseApiError(requestError));
      }
    }

    void load();
  }, [hydrated, router, token]);

  if (!hydrated) {
    return <Card className="p-6 text-slate-300">Menyiapkan admin panel...</Card>;
  }

  return (
    <section className="space-y-4">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-semibold text-white md:text-4xl">Admin Dashboard</h1>
        <Button
          variant="ghost"
          onClick={() => {
            clearToken();
            router.push("/admin/login");
          }}
        >
          Logout
        </Button>
      </div>

      {error ? <Card className="p-4 text-rose-200">{error}</Card> : null}

      <div className="grid gap-4 md:grid-cols-3">
        <Card className="p-5">
          <UsersRound className="h-6 w-6 text-[#7bb0ff]" />
          <p className="mt-3 text-sm text-slate-400">Total Peserta</p>
          <p className="text-2xl font-semibold text-white md:text-3xl">{totalPeserta}</p>
        </Card>
        <Card className="p-5">
          <Settings2 className="h-6 w-6 text-[#7bb0ff]" />
          <p className="mt-3 text-sm text-slate-400">Status Registrasi</p>
          <p className="text-lg font-semibold text-white">{settings?.is_registration_open ? "Dibuka" : "Ditutup"}</p>
        </Card>
        <Card className="p-5">
          <CalendarClock className="h-6 w-6 text-[#7bb0ff]" />
          <p className="mt-3 text-sm text-slate-400">Tanggal Final</p>
          <p className="text-lg font-semibold text-white">
            {tanggal?.tanggal ? formatDateIndonesia(tanggal.tanggal) : "Belum ditentukan"}
          </p>
        </Card>
      </div>
    </section>
  );
}
