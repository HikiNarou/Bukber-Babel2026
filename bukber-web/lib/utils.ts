import type { Hari } from "./types";

const idrFormatter = new Intl.NumberFormat("id-ID", {
  style: "currency",
  currency: "IDR",
  maximumFractionDigits: 0,
});

export function formatRupiah(value: number): string {
  return idrFormatter.format(value);
}

export function formatCompactRupiah(value: number): string {
  if (value >= 1_000_000) {
    return `Rp${(value / 1_000_000).toFixed(1)}jt`;
  }

  return `Rp${Math.round(value / 1000)}rb`;
}

export function capitalize(value: string): string {
  return value.charAt(0).toUpperCase() + value.slice(1);
}

export function labelHari(hari: Hari): string {
  return capitalize(hari);
}

export function formatDateIndonesia(dateValue: string | null | undefined): string {
  if (!dateValue) {
    return "-";
  }

  const date = new Date(dateValue);
  return date.toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

export function formatDateTimeAgo(dateValue: string): string {
  const now = Date.now();
  const target = new Date(dateValue).getTime();
  const diffMs = Math.max(now - target, 0);
  const hours = Math.floor(diffMs / (1000 * 60 * 60));

  if (hours < 1) {
    return "baru saja";
  }
  if (hours < 24) {
    return `${hours} jam lalu`;
  }
  const days = Math.floor(hours / 24);
  return `${days} hari lalu`;
}

export function toSlug(input: string): string {
  return input
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}
