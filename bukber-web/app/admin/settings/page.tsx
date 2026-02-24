"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";
import { useAdminAuth } from "@/hooks/useAdminAuth";
import { adminGetSettings, adminUpdateSettings, parseApiError } from "@/lib/api";
import type { EventSetting } from "@/lib/types";

type FormValues = {
  nama_event: string;
  deadline_registrasi: string;
  deadline_voting: string;
  is_registration_open: boolean;
  is_voting_open: boolean;
};

function toInputDateTime(value: string | null): string {
  if (!value) return "";
  const date = new Date(value);
  const pad = (num: number) => String(num).padStart(2, "0");
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

export default function AdminSettingsPage() {
  const router = useRouter();
  const { token, hydrated } = useAdminAuth();
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const form = useForm<FormValues>({
    defaultValues: {
      nama_event: "",
      deadline_registrasi: "",
      deadline_voting: "",
      is_registration_open: true,
      is_voting_open: false,
    },
  });

  useEffect(() => {
    if (!hydrated) return;
    if (!token) {
      router.replace("/admin/login");
      return;
    }

    adminGetSettings(token)
      .then((response) => {
        const setting: EventSetting = response.data;
        form.reset({
          nama_event: setting.nama_event,
          deadline_registrasi: toInputDateTime(setting.deadline_registrasi),
          deadline_voting: toInputDateTime(setting.deadline_voting),
          is_registration_open: setting.is_registration_open,
          is_voting_open: setting.is_voting_open,
        });
      })
      .catch((requestError) => setError(parseApiError(requestError)));
  }, [form, hydrated, router, token]);

  const onSubmit = form.handleSubmit(async (values) => {
    if (!token) return;
    setError(null);
    setSuccess(null);
    try {
      await adminUpdateSettings(token, {
        nama_event: values.nama_event,
        deadline_registrasi: values.deadline_registrasi || null,
        deadline_voting: values.deadline_voting || null,
        is_registration_open: values.is_registration_open,
        is_voting_open: values.is_voting_open,
      });
      setSuccess("Pengaturan berhasil diperbarui.");
    } catch (requestError) {
      setError(parseApiError(requestError));
    }
  });

  return (
    <section className="space-y-4">
      <h1 className="text-2xl font-semibold text-white md:text-3xl">Pengaturan Event</h1>

      <Card className="p-5">
        <form className="space-y-4" onSubmit={onSubmit}>
          <div>
            <label className="mb-2 block text-sm text-slate-300">Nama Event</label>
            <Input className="h-11 rounded-xl text-base" {...form.register("nama_event")} />
          </div>

          <div className="grid gap-4 md:grid-cols-2">
            <div>
              <label className="mb-2 block text-sm text-slate-300">Deadline Registrasi</label>
              <Input type="datetime-local" className="h-11 rounded-xl text-base" {...form.register("deadline_registrasi")} />
            </div>
            <div>
              <label className="mb-2 block text-sm text-slate-300">Deadline Voting</label>
              <Input type="datetime-local" className="h-11 rounded-xl text-base" {...form.register("deadline_voting")} />
            </div>
          </div>

          <label className="flex items-center gap-2 text-sm text-slate-200">
            <input type="checkbox" {...form.register("is_registration_open")} />
            Registrasi dibuka
          </label>
          <label className="flex items-center gap-2 text-sm text-slate-200">
            <input type="checkbox" {...form.register("is_voting_open")} />
            Voting dibuka
          </label>

          {error ? <p className="text-sm text-rose-300">{error}</p> : null}
          {success ? <p className="text-sm text-emerald-300">{success}</p> : null}

          <Button type="submit">Simpan Pengaturan</Button>
        </form>
      </Card>
    </section>
  );
}
