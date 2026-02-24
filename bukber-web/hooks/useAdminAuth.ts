"use client";

import { useState } from "react";

const TOKEN_KEY = "bukber.admin.token";

export function useAdminAuth() {
  const [token, setTokenState] = useState<string>(() => {
    if (typeof window === "undefined") {
      return "";
    }
    return localStorage.getItem(TOKEN_KEY) ?? "";
  });

  const setToken = (value: string) => {
    setTokenState(value);
    localStorage.setItem(TOKEN_KEY, value);
  };

  const clearToken = () => {
    setTokenState("");
    localStorage.removeItem(TOKEN_KEY);
  };

  return { token, setToken, clearToken, hydrated: true };
}
