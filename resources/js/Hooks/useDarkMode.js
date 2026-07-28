import { useCallback, useEffect, useState } from 'react';

const STORAGE_KEY = 'arkaledger_dark_mode';

export function useDarkMode(initialDark = true) {
    const [isDark, setIsDark] = useState(() => {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored !== null) {
            return stored === 'true';
        }
        return initialDark;
    });

    useEffect(() => {
        localStorage.setItem(STORAGE_KEY, String(isDark));
    }, [isDark]);

    const toggleTheme = useCallback(() => {
        setIsDark((prev) => !prev);
    }, []);

    return { isDark, toggleTheme, setIsDark };
}
