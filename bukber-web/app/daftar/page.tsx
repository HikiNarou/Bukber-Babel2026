"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { UserRound } from "lucide-react";
import { Controller, useForm } from "react-hook-form";
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

const defaultValues: RegistrasiDraftForm = {
  nama_lengkap: "",
  minggu: 2,
  hari: ["jumat", "sabtu"],
  budget_per_orang: 150_000,
  catatan: "",
};

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
      const parsed = JSON.parse(stored) as Partial<RegistrasiDraftForm>;
      form.reset({
        nama_lengkap: parsed.nama_lengkap ?? defaultValues.nama_lengkap,
        minggu: parsed.minggu ?? defaultValues.minggu,
        hari: parsed.hari ?? defaultValues.hari,
        budget_per_orang: parsed.budget_per_orang ?? defaultValues.budget_per_orang,
        catatan: parsed.catatan ?? defaultValues.catatan,
      });
    } catch {
      form.reset(defaultValues);
    }
  }, [form]);

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
            <Controller
              control={form.control}
              name="minggu"
              render={({ field }) => <WeekSelector value={field.value} onChange={field.onChange} />}
            />
          </div>

          <div>
            <label className="mb-2 block text-base text-slate-100 md:mb-3 md:text-2xl">Preferensi Hari</label>
            <Controller
              control={form.control}
              name="hari"
              render={({ field }) => <DayChips value={field.value} onChange={field.onChange} />}
            />
            {form.formState.errors.hari?.message ? (
              <p className="mt-2 text-sm text-rose-300">{form.formState.errors.hari.message}</p>
            ) : null}
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
