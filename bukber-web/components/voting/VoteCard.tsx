import { MapPin, ThumbsUp } from "lucide-react";
import type { Lokasi } from "@/lib/types";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";

interface VoteCardProps {
  lokasi: Lokasi;
  disabled?: boolean;
  onVote: () => void;
}

export function VoteCard({ lokasi, disabled = false, onVote }: VoteCardProps) {
  return (
    <Card className="flex h-full flex-col p-5 md:p-6">
      <h3 className="text-xl font-semibold text-white md:text-3xl">{lokasi.nama_tempat}</h3>
      <p className="mt-2 flex items-start gap-2 text-sm text-slate-300 md:text-lg">
        <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-[#66a4ff]" />
        <span>{lokasi.alamat || "Alamat belum tersedia"}</span>
      </p>

      <div className="mt-6">
        <div className="mb-2 flex items-center justify-between text-sm text-slate-300">
          <span>{lokasi.total_votes ?? 0} suara</span>
          <span>{(lokasi.percentage ?? 0).toFixed(1)}%</span>
        </div>
        <div className="h-3 overflow-hidden rounded-full bg-[#1d304d]">
          <div
            className="h-full rounded-full bg-[linear-gradient(90deg,#2f6df2_0%,#72a6ff_100%)] transition-all"
            style={{ width: `${Math.max(lokasi.percentage ?? 0, 3)}%` }}
          />
        </div>
      </div>

      <Button className="mt-6" fullWidth onClick={onVote} disabled={disabled}>
        <ThumbsUp className="h-4 w-4" />
        Vote Lokasi Ini
      </Button>
    </Card>
  );
}
