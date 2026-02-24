import { z } from "zod";
import { BUDGET_MAX, BUDGET_MIN } from "./constants";

const hariEnum = z.enum(["senin", "selasa", "rabu", "kamis", "jumat", "sabtu", "minggu"]);

export const registrasiDraftSchema = z.object({
  nama_lengkap: z
    .string()
    .min(3, "Nama minimal 3 karakter")
    .max(100, "Nama maksimal 100 karakter")
    .regex(/^[a-zA-Z\s'.]+$/, "Nama hanya boleh huruf, titik, petik, dan spasi"),
  minggu: z.number().int().min(1).max(4),
  hari: z.array(hariEnum).min(1, "Pilih minimal 1 hari"),
  budget_per_orang: z.number().int().min(BUDGET_MIN).max(BUDGET_MAX),
  catatan: z.string().max(500, "Catatan maksimal 500 karakter").optional(),
});

export const lokasiSchema = z.object({
  nama_tempat: z.string().min(3, "Nama tempat minimal 3 karakter").max(200),
  alamat: z.string().max(500).optional(),
  latitude: z.number().min(-90).max(90).optional(),
  longitude: z.number().min(-180).max(180).optional(),
  google_place_id: z.string().max(100).optional(),
});

export const registrasiSchema = registrasiDraftSchema.extend({
  lokasi: lokasiSchema,
});

export const voteSchema = z.object({
  lokasi_id: z.number().int().positive(),
  voter_name: z.string().min(3, "Nama voter minimal 3 karakter").max(100),
  session_token: z.string().min(20).optional(),
});

export const adminLoginSchema = z.object({
  username: z.string().min(3).max(50),
  password: z.string().min(6),
});

export const adminTanggalSchema = z.object({
  tanggal: z.string().min(1),
  jam: z.string().optional(),
  lokasi_id: z.number().int().positive().nullable().optional(),
  catatan: z.string().max(1000).optional(),
  is_locked: z.boolean().default(true),
});

export const adminSettingsSchema = z.object({
  nama_event: z.string().max(200),
  deadline_registrasi: z.string().nullable(),
  deadline_voting: z.string().nullable(),
  is_registration_open: z.boolean(),
  is_voting_open: z.boolean(),
});

export type RegistrasiDraftForm = z.infer<typeof registrasiDraftSchema>;
export type RegistrasiForm = z.infer<typeof registrasiSchema>;
