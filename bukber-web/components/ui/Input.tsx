import { clsx } from "clsx";
import type { InputHTMLAttributes } from "react";

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  error?: string;
}

export function Input({ className, error, ...props }: InputProps) {
  return (
    <div className="space-y-2">
      <input
        {...props}
        className={clsx(
          "h-12 w-full rounded-2xl border border-slate-500/45 bg-slate-700/60 px-4 text-base text-slate-50 placeholder:text-slate-400 md:h-14 md:rounded-full md:px-6 md:text-lg",
          "outline-none ring-0 transition focus:border-[#2f6df2] focus:shadow-[0_0_0_4px_rgba(47,109,242,0.24)]",
          error && "border-rose-400/70 focus:border-rose-400 focus:shadow-[0_0_0_4px_rgba(251,113,133,0.2)]",
          className
        )}
      />
      {error ? <p className="text-sm text-rose-300">{error}</p> : null}
    </div>
  );
}
