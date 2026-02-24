"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Menu, MoonStar, Share2, X } from "lucide-react";
import { useState } from "react";
import { clsx } from "clsx";
import { APP_NAME } from "@/lib/constants";
import { Button } from "@/components/ui/Button";
import { Container } from "./Container";

const links = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/daftar", label: "Peserta" },
  { href: "/voting", label: "Voting" },
  { href: "/tanggal", label: "Lokasi" },
];

export function Header() {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);

  return (
    <header className="sticky top-0 z-40 border-b border-[#263754] bg-[#071327]/90 backdrop-blur-md">
      <Container className="flex h-16 items-center justify-between md:h-20">
        <Link href="/" className="flex items-center gap-3">
          <div className="grid h-9 w-9 place-content-center rounded-full bg-[#2f6df2] shadow-[0_0_0_4px_rgba(47,109,242,0.25)] md:h-10 md:w-10">
            <MoonStar className="h-4 w-4 text-white md:h-5 md:w-5" />
          </div>
          <span className="text-lg font-semibold tracking-tight text-slate-50 md:text-2xl">{APP_NAME}</span>
        </Link>

        <nav className="hidden items-center gap-8 lg:flex">
          {links.map((item) => {
            const active = pathname?.startsWith(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                className={clsx(
                  "text-base transition",
                  active ? "text-white" : "text-slate-300 hover:text-white"
                )}
              >
                {item.label}
              </Link>
            );
          })}
        </nav>

        <div className="hidden items-center gap-3 lg:flex">
          <Button variant="primary" size="md" className="h-10 px-5 text-sm">
            <Share2 className="h-4 w-4" />
            Bagikan
          </Button>
          <div className="h-10 w-10 rounded-full border border-white/30 bg-white/15" />
        </div>

        <button
          type="button"
          aria-label="Open menu"
          className="grid h-10 w-10 place-content-center rounded-xl border border-white/20 text-slate-100 lg:hidden"
          onClick={() => setOpen((prev) => !prev)}
        >
          {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
        </button>
      </Container>

      {open ? (
        <div className="border-t border-white/10 bg-[#0b1a33] px-4 py-2 lg:hidden">
          <div className="mx-auto flex w-full max-w-7xl flex-col gap-1">
            {links.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                className={clsx(
                  "rounded-xl px-4 py-2.5 text-sm",
                  pathname?.startsWith(item.href)
                    ? "bg-[#2f6df2]/30 text-white"
                    : "text-slate-300 hover:bg-white/6 hover:text-white"
                )}
                onClick={() => setOpen(false)}
              >
                {item.label}
              </Link>
            ))}
          </div>
        </div>
      ) : null}
    </header>
  );
}
