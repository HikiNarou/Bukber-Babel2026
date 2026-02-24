"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { getVotingHasil, parseApiError } from "@/lib/api";
import type { VotingData } from "@/lib/types";

export default function AdminVotingPage() {
  const router = useRouter();
  const { token, hydrated } = useAdminAuth();
  const [data, setData] = useState<VotingData | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const response = await getVotingHasil();
      setData(response.data);
      setError(null);
    } catch (requestError) {
      setError(parseApiError(requestError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!hydrated) {
      return;
    }
    if (!token) {
      router.replace("/admin/login");
      return;
    }
    void load();
  }, [hydrated, router, token]);

  return (
    <section className="space-y-4">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-semibold text-white md:text-3xl">Monitoring Voting</h1>
        <Button variant="secondary" onClick={() => void load()}>
          Refresh
        </Button>
      </div>
      {error ? <Card className="p-4 text-rose-200">{error}</Card> : null}
      {loading ? <Card className="p-4 text-slate-300">Memuat hasil voting...</Card> : null}
      {!loading && data ? (
        <div className="grid gap-3 md:grid-cols-2">
          {data.lokasi.map((lokasi, index) => (
            <Card key={`${lokasi.nama_tempat}-${index}`} className="p-4">
              <p className="text-base font-semibold text-white md:text-lg">{lokasi.nama_tempat}</p>
              <p className="text-sm text-slate-300">{lokasi.alamat}</p>
              <p className="mt-2 text-sm text-slate-200">
                {lokasi.total_votes ?? 0} suara ({(lokasi.percentage ?? 0).toFixed(1)}%)
              </p>
            </Card>
          ))}
        </div>
      ) : null}
    </section>
  );
}
