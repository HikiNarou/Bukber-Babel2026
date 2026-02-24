import { clsx } from "clsx";
import type { HTMLAttributes } from "react";

export function Card({ className, ...props }: HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      className={clsx(
        "rounded-2xl border border-[#2f4060] bg-[#121f35]/95 shadow-[0_30px_80px_-35px_rgba(10,30,70,0.95)] md:rounded-[28px]",
        className
      )}
      {...props}
    />
  );
}
