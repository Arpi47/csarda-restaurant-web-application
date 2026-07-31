import { Moon, Sun } from "lucide-react";
import { useTheme } from "../../contexts/ThemeContext";

export default function ThemeSwitcher({ mobile = false }) {
    const { theme, toggleTheme } = useTheme();

    return (
        <button
            onClick={toggleTheme}
            className={`
                w-10
                h-10
                flex
                items-center
                justify-center
                rounded-full
                bg-[var(--color-theme-switcher-bg)]
                transition
                cursor-pointer
                ${mobile ? "" : "theme-hover-bg"}
            `}
        >
            {theme === "dark" ? <Sun size={20} /> : <Moon size={20} />}
        </button>
    );
}
