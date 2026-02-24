import type { ButtonHTMLAttributes } from "react";
import { clsx } from "clsx";

type Variant = "primary" | "secondary" | "ghost" | "danger";
type Size = "sm" | "md" | "lg";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
  size?: Size;
  fullWidth?: boolean;
}

const variantClasses: Record<Variant, string> = {
  primary:
    "bg-[linear-gradient(90deg,#2f6df2_0%,#235fdf_100%)] text-white shadow-[0_20px_45px_-22px_rgba(47,109,242,0.85)] hover:brightness-110",
  secondary: "bg-white/10 text-slate-100 ring-1 ring-white/20 hover:bg-white/16",
  ghost: "bg-transparent text-slate-200 ring-1 ring-white/20 hover:bg-white/10",
  danger: "bg-rose-500 text-white hover:bg-rose-400",
};

const sizeClasses: Record<Size, string> = {
  sm: "h-9 px-3.5 text-sm",
  md: "h-10 px-4 text-sm md:h-11 md:px-5",
  lg: "h-12 px-6 text-base font-semibold md:h-14 md:px-8 md:text-xl",
};

export function Button({
  className,
  variant = "primary",
  size = "md",
  fullWidth = false,
  type = "button",
  ...props
}: ButtonProps) {
  return (
    <button
      type={type}
      className={clsx(
        "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full transition duration-200 disabled:cursor-not-allowed disabled:opacity-50",
        variantClasses[variant],
        sizeClasses[size],
        fullWidth && "w-full",
        className
      )}
      {...props}
    />
  );
}
