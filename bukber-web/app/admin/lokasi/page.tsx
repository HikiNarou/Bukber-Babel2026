"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Card } from "@/components/ui/Card";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { getLokasi, parseApiError } from "@/lib/api";
import type { Lokasi } from "@/lib/types";

export default function AdminLokasiPage() {
  const router = useRouter();
  const { token, hydrated } = useAdminAuth();
  const [data, setData] = useState<Lokasi[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!hydrated) {
      return;
    }
    if (!token) {
      router.replace("/admin/login");
      return;
    }
    getLokasi({ per_page: 50 })
      .then((response) => setData(response.data))
      .catch((requestError) => setError(parseApiError(requestError)));
  }, [hydrated, router, token]);

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-semibold text-white md:text-3xl">Lokasi Usulan</h1>
      {error ? <Card className="p-4 text-rose-200">{error}</Card> : null}
      <div className="grid gap-3 md:grid-cols-2">
        {data.map((lokasi, index) => (
          <Card key={`${lokasi.nama_tempat}-${index}`} className="p-4">
            <p className="text-base font-semibold text-white md:text-lg">{lokasi.nama_tempat}</p>
            <p className="text-sm text-slate-300">{lokasi.alamat || "-"}</p>
            <p className="mt-1 text-sm text-slate-400">
              {lokasi.total_votes ?? 0} suara •{" "}
              {lokasi.latitude && lokasi.longitude
                ? `${lokasi.latitude.toFixed(4)}, ${lokasi.longitude.toFixed(4)}`
                : "Tanpa koordinat"}
            </p>
          </Card>
        ))}
      </div>
    </section>
  );
}
