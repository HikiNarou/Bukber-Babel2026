"use client";

import { useCountdown } from "@/hooks/useCountdown";

interface CountdownTimerProps {
  targetDate: string | null;
}

export function CountdownTimer({ targetDate }: CountdownTimerProps) {
  const timer = useCountdown(targetDate);

  if (timer.expired) {
    return <p className="text-sm text-slate-300">Deadline sudah lewat</p>;
  }

  return (
    <div className="flex flex-wrap items-center gap-2 text-sm text-slate-200">
      <span className="rounded-lg bg-white/10 px-2 py-1">{timer.days}h</span>
      <span className="rounded-lg bg-white/10 px-2 py-1">{timer.hours}j</span>
      <span className="rounded-lg bg-white/10 px-2 py-1">{timer.minutes}m</span>
      <span className="rounded-lg bg-white/10 px-2 py-1">{timer.seconds}d</span>
    </div>
  );
}
