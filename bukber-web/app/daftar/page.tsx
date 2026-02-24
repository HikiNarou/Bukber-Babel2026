"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { UserRound } from "lucide-react";
import { Controller, useForm, useWatch } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Card } from "@/components/ui/Card";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { WeekSelector } from "@/components/forms/WeekSelector";
import { DayChips } from "@/components/forms/DayChips";
import { BudgetSlider } from "@/components/forms/BudgetSlider";
import { REGISTRATION_DRAFT_KEY } from "@/lib/constants";
import type { RegistrasiDraftForm } from "@/lib/validations";
import { registrasiDraftSchema } from "@/lib/validations";
import { Container } from "@/components/layout/Container";
import type { Hari, Minggu, PreferensiMinggu } from "@/lib/types";

const validDays: ReadonlySet<Hari> = new Set(["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"]);
const validWeeks: ReadonlySet<Minggu> = new Set([1, 2, 3, 4]);

const defaultValues: RegistrasiDraftForm = {
  nama_lengkap: "",
  preferensi_minggu: [{ minggu: 2, hari: ["jumat", "sabtu"] }],
  budget_per_orang: 150_000,
  catatan: "",
};

type LegacyDraft = Partial<RegistrasiDraftForm> & {
  minggu?: number | number[];
  hari?: Hari[];
};

function normalizePreferensi(preferensi: unknown): PreferensiMinggu[] {
  if (!Array.isArray(preferensi)) {
    return [];
  }

  const result: PreferensiMinggu[] = [];
  const seen = new Set<number>();

  for (const item of preferensi) {
    if (typeof item !== "object" || item === null) continue;
    const mingguRaw = (item as { minggu?: unknown }).minggu;
    const hariRaw = (item as { hari?: unknown }).hari;

    if (typeof mingguRaw !== "number" || !validWeeks.has(mingguRaw as Minggu) || seen.has(mingguRaw)) continue;

    const hari = Array.isArray(hariRaw)
      ? [...new Set(hariRaw.filter((day): day is Hari => typeof day === "string" && validDays.has(day as Hari)))]
      : [];

    result.push({
      minggu: mingguRaw as Minggu,
      hari,
    });
    seen.add(mingguRaw);
  }

  return result.sort((a, b) => a.minggu - b.minggu);
}

function normalizeDraft(raw: LegacyDraft): RegistrasiDraftForm {
  const preferensiFromNewShape = normalizePreferensi(raw.preferensi_minggu);

  if (preferensiFromNewShape.length > 0) {
    return {
      nama_lengkap: raw.nama_lengkap ?? defaultValues.nama_lengkap,
      preferensi_minggu: preferensiFromNewShape,
      budget_per_orang: raw.budget_per_orang ?? defaultValues.budget_per_orang,
      catatan: raw.catatan ?? defaultValues.catatan,
    };
  }

  if (typeof raw.minggu === "number" && validWeeks.has(raw.minggu as Minggu)) {
    const hari = Array.isArray(raw.hari)
      ? [...new Set(raw.hari.filter((day): day is Hari => validDays.has(day)))]
      : [];

    return {
      nama_lengkap: raw.nama_lengkap ?? defaultValues.nama_lengkap,
      preferensi_minggu: [{ minggu: raw.minggu as Minggu, hari }],
      budget_per_orang: raw.budget_per_orang ?? defaultValues.budget_per_orang,
      catatan: raw.catatan ?? defaultValues.catatan,
    };
  }

  return defaultValues;
}

export default function DaftarPage() {
  const router = useRouter();
  const form = useForm<RegistrasiDraftForm>({
    resolver: zodResolver(registrasiDraftSchema),
    defaultValues,
  });

  useEffect(() => {
    try {
      const stored = localStorage.getItem(REGISTRATION_DRAFT_KEY);
      if (!stored) {
        return;
      }
      const parsed = JSON.parse(stored) as LegacyDraft;
      form.reset(normalizeDraft(parsed));
    } catch {
      form.reset(defaultValues);
    }
  }, [form]);

  const preferensiMinggu = useWatch({
    control: form.control,
    name: "preferensi_minggu",
  }) ?? [];
  const selectedWeeks = preferensiMinggu
    .map((item) => item.minggu)
    .filter((minggu): minggu is Minggu => validWeeks.has(minggu as Minggu));

  const updateSelectedWeeks = (weeks: Minggu[]) => {
    const current = form.getValues("preferensi_minggu");
    const currentMap = new Map(current.map((item) => [item.minggu, item.hari] as const));

    const next = weeks
      .sort((a, b) => a - b)
      .map((minggu) => ({
        minggu,
        hari: currentMap.get(minggu) ?? [],
      }));

    form.setValue("preferensi_minggu", next, {
      shouldDirty: true,
      shouldValidate: true,
    });
  };

  const updateHariForMinggu = (minggu: Minggu, hari: Hari[]) => {
    const current = form.getValues("preferensi_minggu");
    const next = current.map((item) =>
      item.minggu === minggu
        ? {
            ...item,
            hari,
          }
        : item
    );

    form.setValue("preferensi_minggu", next, {
      shouldDirty: true,
      shouldValidate: true,
    });
  };

  const onSubmit = form.handleSubmit((values) => {
    localStorage.setItem(REGISTRATION_DRAFT_KEY, JSON.stringify(values));
    router.push("/lokasi");
  });

  return (
    <Container className="py-8 md:py-12">
      <Card className="mx-auto max-w-5xl overflow-hidden">
        <div className="relative bg-[linear-gradient(180deg,#1a315a_0%,#152747_100%)] px-5 py-8 text-center md:px-12 md:py-14">
          <div className="mx-auto mb-4 grid h-16 w-16 place-content-center rounded-full bg-[#1c4fc6] md:mb-5 md:h-20 md:w-20">
            <div className="h-7 w-7 rounded-full bg-[#6ea3ff] md:h-9 md:w-9" />
          </div>
          <h1 className="text-3xl font-semibold text-white sm:text-4xl md:text-6xl">Pendaftaran Bukber 2026</h1>
          <p className="mx-auto mt-3 max-w-3xl text-base text-slate-300 md:mt-4 md:text-3xl">
            Silakan isi form di bawah ini untuk konfirmasi kehadiran dan preferensi Anda.
          </p>
        </div>

        <form className="space-y-7 px-4 py-6 md:space-y-12 md:px-10 md:py-10" onSubmit={onSubmit}>
          <div>
            <label htmlFor="nama_lengkap" className="mb-2 block text-base text-slate-100 md:mb-3 md:text-2xl">
              Nama Lengkap
            </label>
            <div className="relative">
              <UserRound className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 md:left-5 md:h-5 md:w-5" />
              <Input
                id="nama_lengkap"
                placeholder="Masukkan nama lengkap Anda"
                className="pl-11 md:pl-14"
                error={form.formState.errors.nama_lengkap?.message}
                {...form.register("nama_lengkap")}
              />
            </div>
          </div>

          <div>
            <div className="mb-2 flex items-center justify-between gap-3 md:mb-3">
              <label className="text-base text-slate-100 md:text-2xl">Pilih Minggu</label>
              <span className="text-xs text-slate-400 md:text-xl">Bisa pilih lebih dari satu</span>
            </div>
            <WeekSelector value={selectedWeeks} onChange={updateSelectedWeeks} />
            {form.formState.errors.preferensi_minggu?.message ? (
              <p className="mt-2 text-sm text-rose-300">{form.formState.errors.preferensi_minggu.message}</p>
            ) : null}
          </div>

          <div>
            <label className="mb-2 block text-base text-slate-100 md:mb-3 md:text-2xl">Preferensi Hari per Minggu</label>
            <div className="space-y-4">
              {preferensiMinggu.length === 0 ? (
                <p className="rounded-xl border border-dashed border-slate-500/70 px-4 py-3 text-sm text-slate-300">
                  Pilih minimal 1 minggu terlebih dahulu, lalu tentukan hari untuk setiap minggu.
                </p>
              ) : null}

              {preferensiMinggu.map((item, index) => {
                const mingguKey = item.minggu as Minggu;
                const weekError = form.formState.errors.preferensi_minggu?.[index]?.hari?.message as string | undefined;

                return (
                  <div key={mingguKey} className="rounded-2xl border border-white/10 bg-[#162844] p-4 md:p-5">
                    <p className="mb-3 text-sm font-semibold text-slate-100 md:text-lg">Minggu {mingguKey}</p>
                    <DayChips value={item.hari} onChange={(days) => updateHariForMinggu(mingguKey, days)} />
                    {weekError ? <p className="mt-2 text-sm text-rose-300">{weekError}</p> : null}
                  </div>
                );
              })}
            </div>
          </div>

          <div>
            <Controller
              control={form.control}
              name="budget_per_orang"
              render={({ field }) => <BudgetSlider value={field.value} onChange={field.onChange} />}
            />
          </div>

          <div>
            <label htmlFor="catatan" className="mb-2 block text-base text-slate-100 md:text-2xl">
              Catatan Tambahan (Opsional)
            </label>
            <textarea
              id="catatan"
              rows={3}
              className="w-full rounded-2xl border border-slate-600/70 bg-slate-800/45 px-4 py-3 text-sm text-slate-100 outline-none focus:border-[#2f6df2] md:text-base"
              placeholder="Contoh: tidak makan pedas, alergi seafood, dsb."
              {...form.register("catatan")}
            />
          </div>

          <Button type="submit" size="lg" fullWidth className="h-12 text-base md:h-16 md:text-3xl">
            Lanjut Pilih Lokasi
          </Button>
          <p className="text-center text-sm text-slate-400 md:text-lg">
            Data Anda aman dan hanya digunakan untuk keperluan acara ini.
          </p>
        </form>
      </Card>
    </Container>
  );
}
