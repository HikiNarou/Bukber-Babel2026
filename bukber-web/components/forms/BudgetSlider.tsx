import { BUDGET_MAX, BUDGET_MIN, BUDGET_STEP } from "@/lib/constants";
import { formatRupiah } from "@/lib/utils";

interface BudgetSliderProps {
  value: number;
  onChange: (value: number) => void;
}

export function BudgetSlider({ value, onChange }: BudgetSliderProps) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <p className="text-lg text-slate-100 md:text-3xl">Batas Budget (Per Orang)</p>
        <span className="rounded-full bg-[#1d448f] px-4 py-1.5 text-base font-semibold text-[#5f95ff] md:px-5 md:py-2 md:text-2xl">
          {formatRupiah(value)}
        </span>
      </div>
      <input
        type="range"
        min={BUDGET_MIN}
        max={BUDGET_MAX}
        step={BUDGET_STEP}
        value={value}
        onChange={(event) => onChange(Number(event.target.value))}
        className="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-600/80 accent-[#2f6df2]"
      />
      <div className="flex justify-between text-sm text-slate-400 md:text-2xl">
        <span>50rb</span>
        <span>500rb</span>
      </div>
    </div>
  );
}
