"use client";

import { useCallback, useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Search } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { adminDeletePeserta, adminGetPeserta, parseApiError } from "@/lib/api";
import type { Peserta } from "@/lib/types";
import { formatCompactRupiah, labelHari } from "@/lib/utils";

export default function AdminPesertaPage() {
  const router = useRouter();
  const { token, hydrated } = useAdminAuth();
  const [data, setData] = useState<Peserta[]>([]);
  const [query, setQuery] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const load = useCallback(
    async (search?: string) => {
      if (!token) {
        return;
      }
      setLoading(true);
      try {
        const response = await adminGetPeserta(token, { q: search || undefined, per_page: 50 });
        setData(response.data);
        setError(null);
      } catch (requestError) {
        setError(parseApiError(requestError));
      } finally {
        setLoading(false);
      }
    },
    [token]
  );

  useEffect(() => {
    if (!hydrated) {
      return;
    }
    if (!token) {
      router.replace("/admin/login");
      return;
    }
    void load();
  }, [hydrated, load, router, token]);

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-semibold text-white md:text-3xl">Kelola Peserta</h1>

      <Card className="p-4">
        <form
          className="relative"
          onSubmit={(event) => {
            event.preventDefault();
            void load(query.trim());
          }}
        >
          <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
          <Input
            placeholder="Cari nama peserta..."
            className="h-11 rounded-xl pl-10 text-base"
            value={query}
            onChange={(event) => setQuery(event.target.value)}
          />
        </form>
      </Card>

      {error ? <Card className="p-4 text-rose-200">{error}</Card> : null}
      {loading ? <Card className="p-4 text-slate-300">Memuat peserta...</Card> : null}

      {!loading ? (
        <div className="space-y-3">
          {data.map((item) => (
            <Card key={item.uuid} className="flex flex-col gap-3 p-4 md:flex-row md:items-center md:justify-between">
              <div>
                <p className="text-base font-semibold text-white md:text-lg">{item.nama_lengkap}</p>
                <p className="text-sm text-slate-300">
                  Minggu {item.minggu} • {item.hari.map(labelHari).join(", ")} •{" "}
                  {formatCompactRupiah(item.budget_per_orang)}
                </p>
                <p className="text-sm text-slate-400">{item.lokasi?.nama_tempat ?? "-"}</p>
              </div>
              <Button
                variant="danger"
                size="sm"
                onClick={async () => {
                  if (!token) return;
                  try {
                    await adminDeletePeserta(token, item.id);
                    await load(query.trim());
                  } catch (requestError) {
                    setError(parseApiError(requestError));
                  }
                }}
              >
                Hapus
              </Button>
            </Card>
          ))}
        </div>
      ) : null}
    </section>
  );
}
