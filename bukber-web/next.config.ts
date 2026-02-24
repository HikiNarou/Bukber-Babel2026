import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  reactCompiler: true,
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "staticmap.openstreetmap.de",
      },
    ],
  },
};

export default nextConfig;
