import type { Hari, Minggu } from "./types";

export const APP_NAME = "BukberYuk";

export const HARI_OPTIONS: { key: Hari; label: string }[] = [
  { key: "senin", label: "Senin" },
  { key: "selasa", label: "Selasa" },
  { key: "rabu", label: "Rabu" },
  { key: "kamis", label: "Kamis" },
  { key: "jumat", label: "Jumat" },
  { key: "sabtu", label: "Sabtu" },
  { key: "minggu", label: "Minggu" },
];

export const WEEK_OPTIONS: { key: Minggu; label: string }[] = [
  { key: 1, label: "Minggu 1" },
  { key: 2, label: "Minggu 2" },
  { key: 3, label: "Minggu 3" },
  { key: 4, label: "Minggu 4" },
];

export const BUDGET_MIN = 50_000;
export const BUDGET_MAX = 500_000;
export const BUDGET_STEP = 5_000;

export const REGISTRATION_DRAFT_KEY = "bukber.registration.draft";
export const REGISTRATION_RESULT_KEY = "bukber.registration.result";
export const VOTING_SESSION_KEY = "bukber.voting.session";
