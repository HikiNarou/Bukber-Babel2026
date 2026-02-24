export type Hari = "senin" | "selasa" | "rabu" | "kamis" | "jumat" | "sabtu" | "minggu";
export type Minggu = 1 | 2 | 3 | 4;
export type RespondenAvailability = "bisa" | "mungkin" | "tidak";
export type RespondenFilter = "all" | RespondenAvailability;

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: {
    total: number;
    page: number;
    per_page: number;
    last_page: number;
  };
}

export interface Lokasi {
  id?: number | null;
  nama_tempat: string;
  alamat?: string | null;
  latitude?: number | null;
  longitude?: number | null;
  google_place_id?: string | null;
  total_votes?: number;
  percentage?: number | null;
  source?: "database" | "nominatim";
}

export interface PreferensiMinggu {
  minggu: Minggu;
  hari: Hari[];
}

export interface Peserta {
  id: number;
  uuid: string;
  nama_lengkap: string;
  minggu: Minggu[];
  hari: Hari[];
  preferensi_minggu: PreferensiMinggu[];
  total_slot_ketersediaan: number;
  budget_per_orang: number;
  catatan?: string | null;
  lokasi?: Lokasi | null;
  created_at: string;
}

export interface DashboardStats {
  total_peserta: number;
  rata_rata_budget: number;
  min_budget: number;
  max_budget: number;
  minggu_terfavorit: {
    minggu: Minggu;
    jumlah_peserta: number;
  } | null;
  distribusi_minggu: { minggu: Minggu; jumlah: number }[];
  rekomendasi_hari: {
    minggu: Minggu;
    hari: Hari;
    jumlah_peserta: number;
    persentase_peserta: number;
    is_tie: boolean;
    tie_breaker: "jumlah_peserta_tertinggi" | "minggu_terawal_lalu_hari_terawal";
    kandidat_teratas: {
      minggu: Minggu;
      hari: Hari;
      jumlah_peserta: number;
      persentase_peserta: number;
    }[];
  } | null;
  transparansi_hari: {
    minggu: Minggu;
    hari: Hari;
    jumlah_peserta: number;
    persentase_peserta: number;
  }[];
  detail_ketersediaan: {
    minggu: Minggu;
    hari: {
      hari: Hari;
      jumlah_peserta: number;
      persentase_peserta: number;
    }[];
  }[];
}

export interface ChartHariItem {
  hari: Hari;
  jumlah: number;
}

export interface ChartMingguItem {
  label: string;
  minggu: Minggu;
  jumlah: number;
}

export interface ChartBudgetItem {
  label: string;
  jumlah: number;
}

export interface VotingData {
  is_voting_open: boolean;
  deadline: string | null;
  lokasi: Lokasi[];
  total_voters: number;
  winner?: Lokasi | null;
}

export interface TanggalFinal {
  is_locked: boolean;
  tanggal: string | null;
  hari: string | null;
  jam: string | null;
  lokasi: Lokasi | null;
  estimasi_budget: number;
  total_peserta: number;
  catatan?: string | null;
}

export interface RegistrasiInput {
  nama_lengkap: string;
  preferensi_minggu: PreferensiMinggu[];
  budget_per_orang: number;
  catatan?: string;
  lokasi: {
    nama_tempat: string;
    alamat?: string;
    latitude?: number;
    longitude?: number;
    google_place_id?: string;
  };
}

export interface VoteInput {
  lokasi_id: number;
  voter_name: string;
  session_token?: string;
}

export interface EventSetting {
  id: number;
  nama_event: string;
  deadline_registrasi: string | null;
  deadline_voting: string | null;
  is_registration_open: boolean;
  is_voting_open: boolean;
}
