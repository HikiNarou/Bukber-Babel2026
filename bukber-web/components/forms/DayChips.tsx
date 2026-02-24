import { clsx } from "clsx";
import { HARI_OPTIONS } from "@/lib/constants";
import type { Hari } from "@/lib/types";

interface DayChipsProps {
  value: Hari[];
  onChange: (value: Hari[]) => void;
}

export function DayChips({ value, onChange }: DayChipsProps) {
  const toggle = (day: Hari) => {
    if (value.includes(day)) {
      onChange(value.filter((item) => item !== day));
      return;
    }
    onChange([...value, day]);
  };

  return (
    <div className="flex flex-wrap gap-2.5 md:gap-3">
      {HARI_OPTIONS.map((item) => {
        const active = value.includes(item.key);
        return (
          <button
            key={item.key}
            type="button"
            className={clsx(
              "rounded-full border px-4 py-2 text-sm transition md:px-6 md:py-2.5 md:text-2xl",
              active
                ? "border-[#2f6df2] bg-[#2f6df2] text-white shadow-[0_10px_28px_-15px_rgba(47,109,242,0.9)]"
                : "border-slate-500/70 bg-transparent text-slate-200 hover:border-slate-300"
            )}
            onClick={() => toggle(item.key)}
          >
            {item.label}
          </button>
        );
      })}
    </div>
  );
}
