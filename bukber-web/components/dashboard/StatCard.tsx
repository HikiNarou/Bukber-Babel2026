import { clsx } from "clsx";
import type { ReactNode } from "react";

interface StatCardProps {
  icon: ReactNode;
  label: string;
  value: string;
  helper?: string;
  accent?: "blue" | "green" | "violet";
}

const accentClass = {
  blue: "before:bg-[#2f6df2]/18",
  green: "before:bg-emerald-400/15",
  violet: "before:bg-violet-400/15",
};

export function StatCard({ icon, label, value, helper, accent = "blue" }: StatCardProps) {
  return (
    <article
      className={clsx(
        "relative overflow-hidden rounded-2xl border border-[#2a3f5f] bg-[#1a263d] px-5 py-5 md:rounded-[28px] md:px-6 md:py-7",
        "before:absolute before:right-[-35px] before:top-[-35px] before:h-32 before:w-32 before:rounded-full",
        accentClass[accent]
      )}
    >
      <div className="mb-4 flex items-center gap-3 text-slate-300 md:mb-5 md:gap-4">
        <span className="grid h-10 w-10 place-content-center rounded-xl bg-[#1f3a66] text-[#5b94ff] md:h-12 md:w-12 md:rounded-2xl">{icon}</span>
        <span className="text-sm md:text-xl">{label}</span>
      </div>
      <p className="text-3xl font-semibold leading-none text-white md:text-5xl">{value}</p>
      {helper ? <p className="mt-2 text-sm text-slate-400 md:text-lg">{helper}</p> : null}
    </article>
  );
}
