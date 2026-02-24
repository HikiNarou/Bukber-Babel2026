import { CountdownTimer } from "@/components/decorative/CountdownTimer";

interface VoteCountdownProps {
  deadline: string | null;
}

export function VoteCountdown({ deadline }: VoteCountdownProps) {
  return (
    <div className="w-full rounded-2xl border border-[#284472] bg-[#112341] px-4 py-3 md:w-auto">
      <p className="mb-2 text-sm text-slate-300">Sisa waktu voting</p>
      <CountdownTimer targetDate={deadline} />
    </div>
  );
}
