"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { LockKeyhole } from "lucide-react";
import { adminLogin, parseApiError } from "@/lib/api";
import { adminLoginSchema } from "@/lib/validations";
import { Card } from "@/components/ui/Card";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";

type FormValues = {
  username: string;
  password: string;
};

export default function AdminLoginPage() {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const form = useForm<FormValues>({
    resolver: zodResolver(adminLoginSchema),
    defaultValues: { username: "admin", password: "" },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    setError(null);
    try {
      const response = await adminLogin(values);
      localStorage.setItem("bukber.admin.token", response.data.token);
      router.push("/admin");
    } catch (requestError) {
      setError(parseApiError(requestError));
    }
  });

  return (
    <div className="mx-auto max-w-md py-6 md:py-8">
      <Card className="p-4 md:p-6">
        <div className="mb-5 flex items-center gap-3">
          <div className="grid h-10 w-10 place-content-center rounded-full bg-[#2756bb]">
            <LockKeyhole className="h-5 w-5 text-white" />
          </div>
          <div>
            <h1 className="text-xl font-semibold text-white md:text-2xl">Admin Login</h1>
            <p className="text-sm text-slate-300">Masuk untuk mengelola event bukber</p>
          </div>
        </div>

        <form className="space-y-4" onSubmit={onSubmit}>
          <div>
            <label className="mb-2 block text-sm text-slate-300">Username</label>
            <Input error={form.formState.errors.username?.message} {...form.register("username")} />
          </div>
          <div>
            <label className="mb-2 block text-sm text-slate-300">Password</label>
            <Input type="password" error={form.formState.errors.password?.message} {...form.register("password")} />
          </div>
          {error ? <p className="text-sm text-rose-300">{error}</p> : null}
          <Button type="submit" fullWidth>
            Masuk
          </Button>
        </form>
      </Card>
    </div>
  );
}
