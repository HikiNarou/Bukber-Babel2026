"use client";

import { useEffect, useMemo, useState } from "react";
import { Trophy } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Card } from "@/components/ui/Card";
import { Input } from "@/components/ui/Input";
import { VoteCard } from "@/components/voting/VoteCard";
import { VoteCountdown } from "@/components/voting/VoteCountdown";
import { getVoting, parseApiError, submitVote } from "@/lib/api";
import { VOTING_SESSION_KEY } from "@/lib/constants";
import type { VotingData } from "@/lib/types";

function getSessionToken(): string {
  const existing = localStorage.getItem(VOTING_SESSION_KEY);
  if (existing) {
    return existing;
  }
  const generated = crypto.randomUUID().replaceAll("-", "");
  localStorage.setItem(VOTING_SESSION_KEY, generated);
  return generated;
}

export default function VotingPage() {
  const [data, setData] = useState<VotingData | null>(null);
  const [voterName, setVoterName] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [submitLoading, setSubmitLoading] = useState<number | null>(null);

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await getVoting();
      setData(response.data);
    } catch (requestError) {
      setError(parseApiError(requestError));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    void load();
  }, []);

  const winner = useMemo(() => {
    if (!data?.lokasi?.length) return null;
    return [...data.lokasi].sort((a, b) => (b.total_votes ?? 0) - (a.total_votes ?? 0))[0];
  }, [data?.lokasi]);

  const onVote = async (lokasiId: number) => {
    if (!voterName.trim()) {
      setError("Nama voter wajib diisi sebelum voting.");
      return;
    }
    setSubmitLoading(lokasiId);
    setError(null);
    try {
      await submitVote({
        lokasi_id: lokasiId,
        voter_name: voterName.trim(),
        session_token: getSessionToken(),
      });
      await load();
    } catch (requestError) {
      setError(parseApiError(requestError));
    } finally {
      setSubmitLoading(null);
    }
  };

  return (
    <Container className="py-8 md:py-11">
      <section className="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <h1 className="text-3xl font-semibold text-white sm:text-4xl md:text-6xl">Voting Lokasi Bukber</h1>
          <p className="mt-2 text-sm text-slate-300 md:text-xl">Pilih restoran favoritmu. Satu perangkat hanya bisa voting sekali.</p>
        </div>
        <VoteCountdown deadline={data?.deadline ?? null} />
      </section>

      <Card className="mb-5 p-5">
        <label htmlFor="voter-name" className="mb-2 block text-sm text-slate-300">
          Nama Voter
        </label>
        <Input
          id="voter-name"
          placeholder="Masukkan nama Anda"
          value={voterName}
          onChange={(event) => setVoterName(event.target.value)}
        />
      </Card>

      {winner ? (
        <Card className="mb-5 flex flex-col items-start gap-2 bg-[#173460] p-4 sm:flex-row sm:items-center sm:gap-3">
          <Trophy className="h-6 w-6 text-amber-300" />
          <div>
            <p className="text-sm text-blue-100">Posisi sementara teratas</p>
            <p className="text-lg font-semibold text-white">
              {winner.nama_tempat} ({winner.total_votes ?? 0} suara)
            </p>
          </div>
        </Card>
      ) : null}

      {error ? <Card className="mb-4 p-4 text-rose-200">{error}</Card> : null}

      {loading ? (
        <Card className="p-6 text-slate-300">Memuat data voting...</Card>
      ) : (
        <section className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {data?.lokasi.map((lokasi) => (
            <VoteCard
              key={`${lokasi.id}-${lokasi.nama_tempat}`}
              lokasi={lokasi}
              disabled={!data.is_voting_open || submitLoading !== null}
              onVote={() => onVote(lokasi.id as number)}
            />
          ))}
        </section>
      )}
    </Container>
  );
}
