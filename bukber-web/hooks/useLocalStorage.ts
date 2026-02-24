"use client";

import { useCallback, useEffect, useState } from "react";

export function useLocalStorage<T>(key: string, initialValue: T) {
  const [value, setValue] = useState<T>(initialValue);
  const [hydrated, setHydrated] = useState(false);

  useEffect(() => {
    try {
      const raw = localStorage.getItem(key);
      if (raw) {
        setValue(JSON.parse(raw) as T);
      }
    } catch {
      setValue(initialValue);
    } finally {
      setHydrated(true);
    }
  }, [initialValue, key]);

  const save = useCallback(
    (nextValue: T) => {
      setValue(nextValue);
      localStorage.setItem(key, JSON.stringify(nextValue));
    },
    [key]
  );

  const clear = useCallback(() => {
    localStorage.removeItem(key);
    setValue(initialValue);
  }, [initialValue, key]);

  return { value, save, clear, hydrated };
}
