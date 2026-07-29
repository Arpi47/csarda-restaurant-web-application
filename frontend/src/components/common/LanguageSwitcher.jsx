import { useRef, useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../../contexts/LanguageContext";
import useDropdown from "../../hooks/useDropdown";

export default function LanguageSwitcher({ mobile = false }) {
    const { language, setLanguage } = useLanguage();
    const languages = [
        {
            code: "sr_lat",
            label: "SR",
            flag: "fi fi-rs",
        },
        {
            code: "sr_cyr",
            label: "СР",
            flag: "fi fi-rs",
        },
        {
            code: "en",
            label: "EN",
            flag: "fi fi-gb",
        },
        {
            code: "hu",
            label: "HU",
            flag: "fi fi-hu",
        },
    ];
    if (mobile) {
        return (
            <div
                className="
                    flex
                    gap-2
                    w-full
                "
            >
                {languages.map((lang) => (
                    <motion.button
                        key={lang.code}
                        whileTap={{
                            scale: 0.95,
                        }}
                        onClick={() => setLanguage(lang.code)}
                        className={`
                            relative
                            flex-1
                            h-10
                            flex
                            items-center
                            justify-center
                            gap-1.5
                            rounded-full
                            text-sm
                            font-medium
                            border
                            theme-border
                            transition-all
                            duration-300
                            whitespace-nowrap
                            cursor-pointer
                            ${
                                language === lang.code
                                    ? "theme-button"
                                    : "bg-[var(--color-overlay)] text-[var(--color-text)] hover:opacity-80"
                            }
                        `}
                    >
                        {language === lang.code && (
                            <motion.div
                                layoutId="activeLanguage"
                                className="
                                    absolute
                                    inset-0
                                    bg-[var(--color-text)]
                                    rounded-full
                                    -z-10
                                "
                            />
                        )}
                        {/* <span>🌐</span> */}
                        <span
                            className={`
                                ${lang.flag}
                                rounded-sm
                                shadow-sm
                            `}
                        ></span>
                        <span>{lang.label}</span>
                    </motion.button>
                ))}
            </div>
        );
    }
    const [open, setOpen] = useState(false);
    const ref = useRef(null);
    useDropdown(ref, () => setOpen(false));
    const current = languages.find((lang) => lang.code === language);
    return (
        <div
            ref={ref}
            className="
                relative
            "
        >
            <button
                onClick={() => setOpen(!open)}
                className="
                    flex
                    items-center
                    gap-2
                    px-3
                    py-2
                    rounded-lg
                    theme-hover-bg
                    cursor-pointer
                "
            >
                <span
                    className={`
                        ${current.flag}
                        rounded-sm
                        shadow-sm
                    `}
                ></span>
                <span>{current.label}</span>
                <span>▾</span>
            </button>
            {open && (
                <div
                    className="
                        absolute
                        right-0
                        mt-2
                        bg-[var(--color-surface)]
                        shadow-lg
                        rounded-lg
                        overflow-hidden
                        w-24
                        z-50
                    "
                >
                    {languages.map((lang) => (
                        <button
                            key={lang.code}
                            onClick={() => {
                                setLanguage(lang.code);
                                setOpen(false);
                            }}
                            className={`
                                flex
                                items-center
                                gap-2
                                w-full
                                px-4
                                py-3
                                text-left
                                cursor-pointer
                                theme-hover-bg
                                ${language === lang.code ? "font-bold" : ""}
                            `}
                        >
                            <span
                                className={`
                                    ${lang.flag}
                                    rounded-sm
                                    shadow-sm
                                `}
                            ></span>
                            <span>{lang.label}</span>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
