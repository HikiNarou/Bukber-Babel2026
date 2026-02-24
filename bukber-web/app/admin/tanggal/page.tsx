"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { adminSetTanggal, adminUpdateTanggal, getLokasi, getTanggalFinal, parseApiError } from "@/lib/api";
import type { Lokasi, TanggalFinal } from "@/lib/types";

type FormValues = {
  tanggal: string;
  jam: string;
  lokasi_id: string;
  catatan: string;
  is_locked: boolean;
};

export default function AdminTanggalPage() {
  const router = useRouter();
  const { token, hydrated } = useAdminAuth();
  const [lokasi, setLokasi] = useState<Lokasi[]>([]);
  const [existing, setExisting] = useState<TanggalFinal | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const form = useForm<FormValues>({
    defaultValues: {
      tanggal: "",
      jam: "18:00",
      lokasi_id: "",
      catatan: "",
      is_locked: true,
    },
  });

  useEffect(() => {
    if (!hydrated) return;
    if (!token) {
      router.replace("/admin/login");
      return;
    }

    Promise.all([getLokasi({ per_page: 100 }), getTanggalFinal()])
      .then(([lokasiRes, tanggalRes]) => {
        setLokasi(lokasiRes.data);
        setExisting(tanggalRes.data);
        if (tanggalRes.data.tanggal) {
          form.reset({
            tanggal: tanggalRes.data.tanggal,
            jam: tanggalRes.data.jam || "18:00",
            lokasi_id: tanggalRes.data.lokasi?.id ? String(tanggalRes.data.lokasi.id) : "",
            catatan: tanggalRes.data.catatan || "",
            is_locked: tanggalRes.data.is_locked,
          });
        }
      })
      .catch((requestError) => setError(parseApiError(requestError)));
  }, [form, hydrated, router, token]);

  const onSubmit = form.handleSubmit(async (values) => {
    if (!token) return;
    setError(null);
    setSuccess(null);
    try {
      const payload = {
        tanggal: values.tanggal,
        jam: values.jam || undefined,
        lokasi_id: values.lokasi_id ? Number(values.lokasi_id) : null,
        catatan: values.catatan || undefined,
        is_locked: values.is_locked,
      };

      if (existing?.tanggal) {
        await adminUpdateTanggal(token, payload);
        setSuccess("Tanggal final berhasil diperbarui.");
      } else {
        await adminSetTanggal(token, payload);
        setSuccess("Tanggal final berhasil disimpan.");
      }
    } catch (requestError) {
      setError(parseApiError(requestError));
    }
  });

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-semibold text-white md:text-3xl">Set Tanggal Final</h1>

      <Card className="p-5">
        <form className="space-y-4" onSubmit={onSubmit}>
          <div className="grid gap-4 md:grid-cols-2">
            <div>
              <label className="mb-2 block text-sm text-slate-300">Tanggal</label>
              <Input type="date" className="h-11 rounded-xl text-base" {...form.register("tanggal")} />
            </div>
            <div>
              <label className="mb-2 block text-sm text-slate-300">Jam</label>
              <Input type="time" className="h-11 rounded-xl text-base" {...form.register("jam")} />
            </div>
          </div>

          <div>
            <label className="mb-2 block text-sm text-slate-300">Lokasi</label>
            <select
              className="h-11 w-full rounded-xl border border-slate-600 bg-slate-800 px-3 text-sm text-slate-100"
              {...form.register("lokasi_id")}
            >
              <option value="">Pilih lokasi</option>
              {lokasi.map((item, index) => (
                <option key={`${item.nama_tempat}-${index}`} value={item.id ?? ""}>
                  {item.nama_tempat}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="mb-2 block text-sm text-slate-300">Catatan</label>
            <textarea
              rows={3}
              className="w-full rounded-xl border border-slate-600 bg-slate-800 px-3 py-2 text-sm text-slate-100"
              {...form.register("catatan")}
            />
          </div>

          <label className="flex items-center gap-2 text-sm text-slate-200">
            <input type="checkbox" {...form.register("is_locked")} />
            Lock tanggal final
          </label>

          {error ? <p className="text-sm text-rose-300">{error}</p> : null}
          {success ? <p className="text-sm text-emerald-300">{success}</p> : null}

          <Button type="submit">Simpan Tanggal Final</Button>
        </form>
      </Card>
    </section>
  );
}
