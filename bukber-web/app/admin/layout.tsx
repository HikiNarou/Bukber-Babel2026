"use client";

import Link from "next/link";
import type { ReactNode } from "react";
import { usePathname } from "next/navigation";
import { Container } from "@/components/layout/Container";

const links = [
  { href: "/admin", label: "Overview" },
  { href: "/admin/peserta", label: "Peserta" },
  { href: "/admin/lokasi", label: "Lokasi" },
  { href: "/admin/voting", label: "Voting" },
  { href: "/admin/tanggal", label: "Tanggal" },
  { href: "/admin/settings", label: "Settings" },
];

export default function AdminLayout({ children }: { children: ReactNode }) {
  const pathname = usePathname();
  const isLoginPage = pathname === "/admin/login";

  return (
    <Container className="py-7 md:py-10">
      {!isLoginPage ? (
        <div className="mb-5 overflow-x-auto rounded-2xl border border-[#2f4360] bg-[#11233d] p-2">
          <nav className="flex min-w-max gap-2">
            {links.map((link) => (
              <Link key={link.href} href={link.href} className="rounded-xl px-4 py-2 text-sm text-slate-200 hover:bg-white/10">
                {link.label}
              </Link>
            ))}
          </nav>
        </div>
      ) : null}
      {children}
    </Container>
  );
}
