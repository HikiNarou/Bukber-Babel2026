"use client";

import dynamic from "next/dynamic";
import { useRouter } from "next/navigation";
import { useCallback, useEffect, useMemo, useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { LocateFixed } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";
import { Card } from "@/components/ui/Card";
import { Container } from "@/components/layout/Container";
import { parseApiError, submitRegistrasi } from "@/lib/api";
import { REGISTRATION_DRAFT_KEY, REGISTRATION_RESULT_KEY } from "@/lib/constants";
import { lokasiSchema } from "@/lib/validations";
import type { RegistrasiInput } from "@/lib/types";

type LokasiForm = {
  nama_tempat: string;
  alamat?: string;
  latitude?: number;
  longitude?: number;
  google_place_id?: string;
};

function toFiniteNumber(value: number | undefined): number | undefined {
  return Number.isFinite(value) ? value : undefined;
}

const DynamicMapPicker = dynamic(
  () => import("@/components/map/MapPicker").then((module) => module.MapPicker),
  { ssr: false }
);

export default function LokasiPage() {
  const router = useRouter();
  const [draft, setDraft] = useState<Omit<RegistrasiInput, "lokasi"> | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [geoStatus, setGeoStatus] = useState<string | null>(null);
  const [addressResolving, setAddressResolving] = useState(false);
  const [requestError, setRequestError] = useState<string | null>(null);

  const form = useForm<LokasiForm>({
    resolver: zodResolver(lokasiSchema),
    defaultValues: {
      nama_tempat: "",
      alamat: "",
    },
  });

  useEffect(() => {
    const raw = localStorage.getItem(REGISTRATION_DRAFT_KEY);
    if (!raw) {
      router.replace("/daftar");
      return;
    }

    try {
      const parsed = JSON.parse(raw) as Omit<RegistrasiInput, "lokasi">;
      setDraft(parsed);
    } catch {
      router.replace("/daftar");
    }
  }, [router]);

  const watchedLatitude = form.watch("latitude");
  const watchedLongitude = form.watch("longitude");
  const selectedCoords = useMemo(
    () => ({
      latitude: toFiniteNumber(watchedLatitude),
      longitude: toFiniteNumber(watchedLongitude),
    }),
    [watchedLatitude, watchedLongitude]
  );

  const resolveAddress = useCallback(
    async (latitude: number, longitude: number) => {
      setAddressResolving(true);

      try {
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`,
          {
            headers: { "Accept-Language": "id" },
          }
        );

        if (!response.ok) {
          return;
        }

        const payload = (await response.json()) as { display_name?: string };
        const currentAlamat = form.getValues("alamat")?.trim();
        if (!currentAlamat && payload.display_name) {
          form.setValue("alamat", payload.display_name, {
            shouldDirty: true,
            shouldValidate: true,
          });
        }
      } catch {
        // Keep UX resilient when reverse geocoding service is unavailable.
      } finally {
        setAddressResolving(false);
      }
    },
    [form]
  );

  const applyCoords = useCallback(
    (latitude: number, longitude: number) => {
      const safeLatitude = Number(latitude.toFixed(6));
      const safeLongitude = Number(longitude.toFixed(6));

      form.setValue("latitude", safeLatitude, { shouldDirty: true, shouldValidate: true });
      form.setValue("longitude", safeLongitude, { shouldDirty: true, shouldValidate: true });
      void resolveAddress(safeLatitude, safeLongitude);
    },
    [form, resolveAddress]
  );

  const requestCurrentLocation = useCallback(() => {
    if (!navigator.geolocation) {
      setGeoStatus("Perangkat tidak mendukung GPS. Silakan isi koordinat manual jika diperlukan.");
      return;
    }

    setGeoStatus("Meminta izin GPS...");

    navigator.geolocation.getCurrentPosition(
      (position) => {
        setGeoStatus("Lokasi GPS terdeteksi. Koordinat berhasil diisi.");
        applyCoords(position.coords.latitude, position.coords.longitude);
      },
      () => {
        setGeoStatus("Izin GPS ditolak. Koordinat tetap opsional dan bisa dikosongkan.");
      },
      {
        enableHighAccuracy: true,
        timeout: 8000,
        maximumAge: 60_000,
      }
    );
  }, [applyCoords]);

  const handleSubmit = form.handleSubmit(async (values) => {
    if (!draft) {
      return;
    }
    setSubmitting(true);
    setRequestError(null);

    try {
      const payload: RegistrasiInput = {
        ...draft,
        lokasi: {
          nama_tempat: values.nama_tempat,
          alamat: values.alamat,
          latitude: toFiniteNumber(values.latitude),
          longitude: toFiniteNumber(values.longitude),
          google_place_id: values.google_place_id,
        },
      };

      const response = await submitRegistrasi(payload);
      localStorage.setItem(REGISTRATION_RESULT_KEY, JSON.stringify(response.data));
      localStorage.removeItem(REGISTRATION_DRAFT_KEY);
      router.push(`/selesai?uuid=${response.data.uuid}`);
    } catch (error) {
      setRequestError(parseApiError(error));
    } finally {
      setSubmitting(false);
    }
  });

  return (
    <Container className="py-8 md:py-12">
      <Card className="mx-auto max-w-5xl p-4 md:p-8">
        <h1 className="text-2xl font-semibold text-white sm:text-3xl md:text-5xl">Pilih Lokasi Bukber</h1>
        <p className="mt-2 text-sm text-slate-300 md:text-lg">
          Isi nama tempat sebagai data utama. Alamat, latitude, dan longitude bersifat opsional.
        </p>

        <form className="mt-5 space-y-5 md:mt-6 md:space-y-6" onSubmit={handleSubmit}>
          <div className="grid gap-4 md:grid-cols-2">
            <div>
              <label className="mb-2 block text-base text-slate-200">Nama Tempat</label>
              <Input
                placeholder="Masukkan nama restoran/tempat"
                error={form.formState.errors.nama_tempat?.message}
                {...form.register("nama_tempat")}
              />
            </div>
            <div>
              <label className="mb-2 block text-base text-slate-200">Alamat (Opsional)</label>
              <Input placeholder="Masukkan alamat lengkap" error={form.formState.errors.alamat?.message} {...form.register("alamat")} />
            </div>
          </div>

          <div className="grid gap-4 md:grid-cols-2">
            <div>
              <label className="mb-2 block text-base text-slate-200">Latitude (Opsional)</label>
              <Input
                type="number"
                step="any"
                placeholder="-6.2088"
                {...form.register("latitude", {
                  setValueAs: (value) => {
                    if (value === "" || value === null || value === undefined) {
                      return undefined;
                    }
                    const parsed = Number(value);
                    return Number.isFinite(parsed) ? parsed : undefined;
                  },
                })}
              />
            </div>
            <div>
              <label className="mb-2 block text-base text-slate-200">Longitude (Opsional)</label>
              <Input
                type="number"
                step="any"
                placeholder="106.8456"
                {...form.register("longitude", {
                  setValueAs: (value) => {
                    if (value === "" || value === null || value === undefined) {
                      return undefined;
                    }
                    const parsed = Number(value);
                    return Number.isFinite(parsed) ? parsed : undefined;
                  },
                })}
              />
            </div>
          </div>

          <div className="space-y-3">
            <p className="text-sm text-slate-300">
              Koordinat opsional. Bisa diisi manual, klik peta, atau gunakan GPS.
            </p>
            <Button type="button" variant="secondary" size="sm" className="w-full sm:w-fit" onClick={requestCurrentLocation}>
              Gunakan Lokasi Saya (Opsional)
            </Button>
            <Controller
              control={form.control}
              name="latitude"
              render={() => (
                <DynamicMapPicker
                  latitude={selectedCoords.latitude}
                  longitude={selectedCoords.longitude}
                  onChange={(coords) => {
                    applyCoords(coords.latitude, coords.longitude);
                  }}
                />
              )}
            />
            {geoStatus ? <p className="text-sm text-slate-400">{geoStatus}</p> : null}
            {addressResolving ? (
              <p className="text-sm text-slate-400">Menerjemahkan koordinat menjadi alamat...</p>
            ) : null}
          </div>

          {requestError ? (
            <p className="rounded-xl border border-rose-400/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{requestError}</p>
          ) : null}

          <div className="flex flex-col gap-3 md:flex-row">
            <Button type="button" variant="ghost" onClick={() => router.push("/daftar")} className="md:min-w-[220px]">
              Kembali ke Form
            </Button>
            <Button type="submit" className="flex-1" disabled={submitting}>
              <LocateFixed className="h-4 w-4" />
              {submitting ? "Menyimpan..." : "Konfirmasi & Daftar"}
            </Button>
          </div>
        </form>
      </Card>
    </Container>
  );
}
