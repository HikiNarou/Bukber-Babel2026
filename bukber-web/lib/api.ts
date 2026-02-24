import axios, { AxiosError } from "axios";
import type {
  ApiResponse,
  ChartBudgetItem,
  ChartHariItem,
  ChartMingguItem,
  DashboardStats,
  EventSetting,
  Lokasi,
  PaginatedResponse,
  Peserta,
  RespondenAvailability,
  RegistrasiInput,
  TanggalFinal,
  VoteInput,
  VotingData,
} from "./types";

export const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api/v1",
  headers: {
    "Content-Type": "application/json",
  },
  timeout: 15000,
});

export function parseApiError(error: unknown): string {
  if (error instanceof AxiosError) {
    const data = error.response?.data as
      | { message?: string; errors?: Record<string, string[]> }
      | undefined;
    if (data?.errors) {
      const firstEntry = Object.values(data.errors)[0];
      if (firstEntry?.length) {
        return firstEntry[0];
      }
    }
    if (data?.message) {
      return data.message;
    }
  }

  return "Terjadi kesalahan. Silakan coba lagi.";
}

export async function submitRegistrasi(payload: RegistrasiInput): Promise<ApiResponse<Peserta>> {
  const response = await api.post<ApiResponse<Peserta>>("/registrasi", payload);
  return response.data;
}

export async function getRegistrasi(params?: {
  page?: number;
  per_page?: number;
  q?: string;
}): Promise<PaginatedResponse<Peserta>> {
  const response = await api.get<PaginatedResponse<Peserta>>("/registrasi", { params });
  return response.data;
}

export async function getRegistrasiByUuid(uuid: string): Promise<ApiResponse<Peserta>> {
  const response = await api.get<ApiResponse<Peserta>>(`/registrasi/${uuid}`);
  return response.data;
}

export async function getDashboardStats(): Promise<ApiResponse<DashboardStats>> {
  const response = await api.get<ApiResponse<DashboardStats>>("/dashboard/stats");
  return response.data;
}

export async function getChartHari(): Promise<ApiResponse<ChartHariItem[]>> {
  const response = await api.get<ApiResponse<ChartHariItem[]>>("/dashboard/chart/hari");
  return response.data;
}

export async function getChartMinggu(): Promise<ApiResponse<ChartMingguItem[]>> {
  const response = await api.get<ApiResponse<ChartMingguItem[]>>("/dashboard/chart/minggu");
  return response.data;
}

export async function getChartBudget(): Promise<ApiResponse<{ ranges: ChartBudgetItem[] }>> {
  const response = await api.get<ApiResponse<{ ranges: ChartBudgetItem[] }>>("/dashboard/chart/budget");
  return response.data;
}

export async function getResponden(params?: {
  page?: number;
  per_page?: number;
  availability?: RespondenAvailability;
}): Promise<PaginatedResponse<Peserta>> {
  const response = await api.get<PaginatedResponse<Peserta>>("/dashboard/responden", { params });
  return response.data;
}

export async function getLokasi(params?: {
  page?: number;
  per_page?: number;
}): Promise<PaginatedResponse<Lokasi>> {
  const response = await api.get<PaginatedResponse<Lokasi>>("/lokasi", { params });
  return response.data;
}

export async function searchLokasi(q: string): Promise<ApiResponse<Lokasi[]>> {
  const response = await api.get<ApiResponse<Lokasi[]>>("/lokasi/search", { params: { q } });
  return response.data;
}

export async function createLokasi(payload: Partial<Lokasi>): Promise<ApiResponse<Lokasi>> {
  const response = await api.post<ApiResponse<Lokasi>>("/lokasi", payload);
  return response.data;
}

export async function getVoting(): Promise<ApiResponse<VotingData>> {
  const response = await api.get<ApiResponse<VotingData>>("/voting");
  return response.data;
}

export async function submitVote(payload: VoteInput): Promise<ApiResponse<null>> {
  const response = await api.post<ApiResponse<null>>("/voting", payload);
  return response.data;
}

export async function getVotingHasil(): Promise<ApiResponse<VotingData>> {
  const response = await api.get<ApiResponse<VotingData>>("/voting/hasil");
  return response.data;
}

export async function getTanggalFinal(): Promise<ApiResponse<TanggalFinal>> {
  const response = await api.get<ApiResponse<TanggalFinal>>("/tanggal");
  return response.data;
}

export async function adminLogin(payload: {
  username: string;
  password: string;
}): Promise<ApiResponse<{ token: string; token_type: string; admin: { id: number; username: string } }>> {
  const response = await api.post<ApiResponse<{ token: string; token_type: string; admin: { id: number; username: string } }>>(
    "/admin/login",
    payload
  );
  return response.data;
}

export async function adminGetSettings(token: string): Promise<ApiResponse<EventSetting>> {
  const response = await api.get<ApiResponse<EventSetting>>("/admin/settings", {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}

export async function adminUpdateSettings(
  token: string,
  payload: Partial<EventSetting>
): Promise<ApiResponse<EventSetting>> {
  const response = await api.put<ApiResponse<EventSetting>>("/admin/settings", payload, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}

export async function adminGetPeserta(
  token: string,
  params?: { page?: number; per_page?: number; q?: string }
): Promise<PaginatedResponse<Peserta>> {
  const response = await api.get<PaginatedResponse<Peserta>>("/admin/peserta", {
    params,
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}

export async function adminDeletePeserta(token: string, id: number): Promise<ApiResponse<null>> {
  const response = await api.delete<ApiResponse<null>>(`/admin/peserta/${id}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}

export async function adminSetTanggal(
  token: string,
  payload: {
    tanggal: string;
    jam?: string;
    lokasi_id?: number | null;
    catatan?: string;
    is_locked?: boolean;
  }
): Promise<ApiResponse<TanggalFinal>> {
  const response = await api.post<ApiResponse<TanggalFinal>>("/admin/tanggal", payload, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}

export async function adminUpdateTanggal(
  token: string,
  payload: {
    tanggal: string;
    jam?: string;
    lokasi_id?: number | null;
    catatan?: string;
    is_locked?: boolean;
  }
): Promise<ApiResponse<TanggalFinal>> {
  const response = await api.put<ApiResponse<TanggalFinal>>("/admin/tanggal", payload, {
    headers: { Authorization: `Bearer ${token}` },
  });
  return response.data;
}
