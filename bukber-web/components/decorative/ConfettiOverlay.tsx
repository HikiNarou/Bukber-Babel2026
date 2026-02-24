"use client";

export function ConfettiOverlay() {
  const pieces = Array.from({ length: 24 }).map((_, index) => ({
    id: index,
    left: `${(index * 17) % 100}%`,
    delay: `${(index % 7) * 0.18}s`,
    duration: `${2 + (index % 5) * 0.35}s`,
  }));

  return (
    <div aria-hidden className="pointer-events-none absolute inset-0 overflow-hidden">
      {pieces.map((piece) => (
        <span
          key={piece.id}
          className="absolute top-[-20px] h-3 w-2 rounded-full bg-[#2f6df2] opacity-75 [animation-name:confetti-fall] [animation-iteration-count:infinite] [animation-timing-function:linear]"
          style={{
            left: piece.left,
            animationDelay: piece.delay,
            animationDuration: piece.duration,
          }}
        />
      ))}
    </div>
  );
}
