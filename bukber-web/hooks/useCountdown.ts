"use client";

import { useEffect, useMemo, useState } from "react";

type Countdown = {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
  expired: boolean;
};

export function useCountdown(targetDate: string | null | undefined): Countdown {
  const target = useMemo(() => (targetDate ? new Date(targetDate).getTime() : null), [targetDate]);
  const [now, setNow] = useState(() => Date.now());

  useEffect(() => {
    if (!target) {
      return;
    }
    const id = window.setInterval(() => setNow(Date.now()), 1000);
    return () => window.clearInterval(id);
  }, [target]);

  if (!target) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, expired: true };
  }

  const diff = target - now;
  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0, expired: true };
  }

  return {
    days: Math.floor(diff / (1000 * 60 * 60 * 24)),
    hours: Math.floor((diff / (1000 * 60 * 60)) % 24),
    minutes: Math.floor((diff / (1000 * 60)) % 60),
    seconds: Math.floor((diff / 1000) % 60),
    expired: false,
  };
}
