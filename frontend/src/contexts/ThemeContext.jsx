import { createContext, useContext, useEffect, useState } from "react";

const ThemeContext = createContext();

function getSystemTheme() {
    return window.matchMedia("(prefers-color-scheme: dark)").matches
        ? "dark"
        : "light";
}
export function ThemeProvider({ children }) {
    const [theme, setTheme] = useState(
        localStorage.getItem("theme") || "system"
    );
    useEffect(() => {
        const root = document.documentElement;
        function applyTheme() {
            let activeTheme;
            if (theme === "system") {
                activeTheme = getSystemTheme();
            } else {
                activeTheme = theme;
            }
            if (activeTheme === "dark") {
                root.classList.add("dark");
            } else {
                root.classList.remove("dark");
            }
        }
        applyTheme();
        localStorage.setItem("theme", theme);
        if (theme === "system") {
            const media = window.matchMedia(
                "(prefers-color-scheme: dark)"
            );
            media.addEventListener(
                "change",
                applyTheme
            );
            return () => {
                media.removeEventListener(
                    "change",
                    applyTheme
                );
            };
        }
    }, [theme]);

    function toggleTheme() {

        if (theme === "system") {
            setTheme("dark");
        }
        else if (theme === "dark") {
            setTheme("light");
        }
        else {
            setTheme("system");
        }

    }
    return (
        <ThemeContext.Provider
            value={{
                theme,
                toggleTheme,
            }}
        >
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    return useContext(ThemeContext);
}