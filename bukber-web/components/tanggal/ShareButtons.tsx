"use client";

import { Share2 } from "lucide-react";
import { Button } from "@/components/ui/Button";

interface ShareButtonsProps {
  text: string;
}

export function ShareButtons({ text }: ShareButtonsProps) {
  const shareToWhatsapp = () => {
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, "_blank");
  };

  const shareNative = async () => {
    if (navigator.share) {
      await navigator.share({ text });
      return;
    }
    await navigator.clipboard.writeText(text);
    alert("Teks pengumuman disalin ke clipboard.");
  };

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
      <Button variant="secondary" onClick={shareNative} className="w-full sm:w-auto">
        <Share2 className="h-4 w-4" />
        Bagikan
      </Button>
      <Button onClick={shareToWhatsapp} className="w-full sm:w-auto">
        Kirim ke WhatsApp
      </Button>
    </div>
  );
}
