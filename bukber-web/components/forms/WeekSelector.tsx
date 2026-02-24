import { CalendarDays } from "lucide-react";
import { clsx } from "clsx";
import { WEEK_OPTIONS } from "@/lib/constants";
import type { Minggu } from "@/lib/types";

interface WeekSelectorProps {
  value: Minggu[];
  onChange: (value: Minggu[]) => void;
}

export function WeekSelector({ value, onChange }: WeekSelectorProps) {
  const toggleWeek = (week: Minggu) => {
    if (value.includes(week)) {
      onChange(value.filter((item) => item !== week));
      return;
    }

    onChange([...value, week].sort((a, b) => a - b) as Minggu[]);
  };

  return (
    <div className="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-5">
      {WEEK_OPTIONS.map((week) => {
        const active = value.includes(week.key);
        return (
          <button
            key={week.key}
            type="button"
            className={clsx(
              "group h-24 rounded-2xl border text-center transition-all md:h-28 md:rounded-[28px]",
              active
                ? "border-[#3f7af5] bg-[#295fd4]/24 shadow-[0_18px_35px_-20px_rgba(63,122,245,0.9)]"
                : "border-[#35496a] bg-[#1f2d46]/85 hover:border-[#4f6790]"
            )}
            onClick={() => toggleWeek(week.key)}
          >
            <CalendarDays
              className={clsx(
                "mx-auto mb-2 mt-4 h-5 w-5 md:mb-3 md:mt-5 md:h-6 md:w-6",
                active ? "text-[#71a1ff]" : "text-slate-400 group-hover:text-slate-200"
              )}
            />
            <span className={clsx("text-lg md:text-2xl", active ? "text-white" : "text-slate-200")}>{week.label}</span>
          </button>
        );
      })}
    </div>
  );
}
