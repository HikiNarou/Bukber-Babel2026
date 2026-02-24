import { ChevronDown, ChevronLeft, ChevronRight, ChevronUp } from "lucide-react";
import { Card } from "@/components/ui/Card";
import type { Peserta, RespondenFilter } from "@/lib/types";
import { formatCompactRupiah, formatPreferensiMinggu } from "@/lib/utils";

const filterOptions: Array<{ value: RespondenFilter; label: string }> = [
  { value: "all", label: "Semua" },
  { value: "bisa", label: "Bisa" },
  { value: "mungkin", label: "Mungkin" },
  { value: "tidak", label: "Tidak" },
];

interface RespondenTableProps {
  data: Peserta[];
  isExpanded: boolean;
  isLoading: boolean;
  error?: string | null;
  filter: RespondenFilter;
  page: number;
  totalPages: number;
  totalItems: number;
  perPage: number;
  onToggleExpand: () => void;
  onFilterChange: (value: RespondenFilter) => void;
  onPageChange: (page: number) => void;
}

export function RespondenTable({
  data,
  isExpanded,
  isLoading,
  error,
  filter,
  page,
  totalPages,
  totalItems,
  perPage,
  onToggleExpand,
  onFilterChange,
  onPageChange,
}: RespondenTableProps) {
  const contentId = "responden-table-content";
  const startItem = totalItems === 0 ? 0 : (page - 1) * perPage + 1;
  const endItem = totalItems === 0 ? 0 : Math.min(page * perPage, totalItems);

  return (
    <Card id="responden-table" className="overflow-hidden">
      <div className="border-b border-white/8 px-4 py-4 md:px-8 md:py-5">
        <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <h3 className="text-2xl font-semibold text-white md:text-4xl">Daftar Responden</h3>
            <p className="mt-1 text-sm text-slate-400 md:text-xl">Ringkasan lengkap preferensi peserta</p>
          </div>

          <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div className="inline-flex w-full flex-wrap items-center gap-1 rounded-2xl border border-[#345180] bg-[#13284d] p-1 sm:w-fit sm:rounded-full">
              {filterOptions.map((option) => {
                const active = filter === option.value;
                return (
                  <button
                    key={option.value}
                    type="button"
                    className={[
                      "rounded-full px-3 py-1.5 text-xs transition sm:text-sm",
                      active ? "bg-[#2f6df2] text-white" : "text-slate-300 hover:bg-white/10",
                    ].join(" ")}
                    onClick={() => onFilterChange(option.value)}
                    aria-pressed={active}
                  >
                    {option.label}
                  </button>
                );
              })}
            </div>

            <button
              type="button"
              className="inline-flex h-10 w-full items-center justify-center gap-2 rounded-full border border-[#345180] bg-[#13284d] px-4 text-sm text-slate-100 transition hover:bg-[#1b3561] sm:w-auto"
              onClick={onToggleExpand}
              aria-expanded={isExpanded}
              aria-controls={contentId}
            >
              {isExpanded ? "Hide" : "Extend"}
              {isExpanded ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
            </button>
          </div>
        </div>
      </div>

      <div id={contentId}>
        {!isExpanded ? (
          <p className="px-4 py-4 text-sm text-slate-400 md:px-8 md:text-base">Daftar responden sedang di-hide. Klik tombol Extend untuk menampilkan.</p>
        ) : null}
      </div>

      {isExpanded ? (
        <>
          {isLoading ? <p className="px-4 py-4 text-sm text-slate-300 md:px-8 md:text-base">Memuat daftar responden...</p> : null}
          {error ? <p className="px-4 py-4 text-sm text-rose-200 md:px-8 md:text-base">{error}</p> : null}

          {!isLoading && !error ? (
            data.length > 0 ? (
              <>
                <div className="hidden overflow-x-auto md:block">
                  <table className="min-w-full text-left">
                    <thead className="bg-[#15233b] text-slate-300">
                      <tr>
                        <th className="px-6 py-3 text-base font-medium">Nama</th>
                        <th className="px-6 py-3 text-base font-medium">Preferensi Minggu & Hari</th>
                        <th className="px-6 py-3 text-base font-medium">Slot Bisa</th>
                        <th className="px-6 py-3 text-base font-medium">Budget</th>
                        <th className="px-6 py-3 text-base font-medium">Lokasi</th>
                      </tr>
                    </thead>
                    <tbody>
                      {data.map((peserta) => (
                        <tr key={peserta.uuid} className="border-t border-white/8 text-slate-100">
                          <td className="px-6 py-4 text-lg">{peserta.nama_lengkap}</td>
                          <td className="px-6 py-4 text-lg">{formatPreferensiMinggu(peserta.preferensi_minggu) || "-"}</td>
                          <td className="px-6 py-4 text-lg">{peserta.total_slot_ketersediaan}</td>
                          <td className="px-6 py-4 text-lg">{formatCompactRupiah(peserta.budget_per_orang)}</td>
                          <td className="px-6 py-4 text-lg">{peserta.lokasi?.nama_tempat ?? "-"}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                <div className="space-y-3 p-4 md:hidden">
                  {data.map((peserta) => (
                    <article key={peserta.uuid} className="rounded-2xl border border-white/8 bg-[#1b2b47]/70 p-4">
                      <h4 className="text-lg font-semibold text-white">{peserta.nama_lengkap}</h4>
                      <p className="mt-1 text-sm text-slate-300">{formatPreferensiMinggu(peserta.preferensi_minggu) || "-"}</p>
                      <p className="mt-1 text-sm text-slate-300">Slot Bisa: {peserta.total_slot_ketersediaan}</p>
                      <p className="mt-1 text-sm text-slate-300">{formatCompactRupiah(peserta.budget_per_orang)}</p>
                      <p className="mt-1 text-sm text-slate-300">{peserta.lokasi?.nama_tempat ?? "-"}</p>
                    </article>
                  ))}
                </div>
              </>
            ) : (
              <p className="px-4 py-4 text-sm text-slate-400 md:px-8 md:text-base">Tidak ada responden untuk filter yang dipilih.</p>
            )
          ) : null}

          {!isLoading && !error && totalItems > 0 ? (
            <div className="border-t border-white/8 px-4 py-4 md:px-8">
              <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <p className="text-xs text-slate-400 md:text-sm">
                  Menampilkan {startItem}-{endItem} dari {totalItems} responden.
                </p>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => onPageChange(Math.max(1, page - 1))}
                    disabled={page === 1}
                    className="inline-flex h-9 items-center gap-1 rounded-full border border-[#355180] px-3 text-xs text-slate-100 transition enabled:hover:bg-[#1b3561] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                  >
                    <ChevronLeft className="h-4 w-4" />
                    Prev
                  </button>
                  <span className="rounded-full border border-[#355180] bg-[#13284d] px-3 py-1 text-xs text-slate-200 md:text-sm">
                    Hal {page}/{totalPages}
                  </span>
                  <button
                    type="button"
                    onClick={() => onPageChange(Math.min(totalPages, page + 1))}
                    disabled={page === totalPages}
                    className="inline-flex h-9 items-center gap-1 rounded-full border border-[#355180] px-3 text-xs text-slate-100 transition enabled:hover:bg-[#1b3561] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                  >
                    Next
                    <ChevronRight className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          ) : null}
        </>
      ) : null}
    </Card>
  );
}
