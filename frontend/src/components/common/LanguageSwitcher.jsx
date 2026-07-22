import { useRef, useState } from "react";
import { useLanguage } from "../../contexts/LanguageContext";
import useDropdown from "../../hooks/useDropdown";

export default function LanguageSwitcher({ mobile = false }) {
    const { language, setLanguage } = useLanguage();
    const languages = [
        {
            code: "sr_lat",
            label: " 🇷🇸 SR"
        },
        {
            code: "sr_cyr",
            label: " 🇷🇸 СР"
        },
        {
            code: "en",
            label: " 🇬🇧 EN"
        },
        {
            code: "hu",
            label: " 🇭🇺 HU"
        }
    ];
    if (mobile) {
        return (
            <div className="
                flex
                gap-2
            ">
                {
                    languages.map(lang => (
                        <button
                            key={lang.code}
                            onClick={() =>
                                setLanguage(lang.code)
                            }
                            className={`
                                w-16
                                h-10
                                flex
                                items-center
                                justify-center
                                rounded-full
                                text-sm
                                transition
                                cursor-pointer
                                ${
                                    language === lang.code
                                    ?
                                    "theme-button"
                                    :
                                    "theme-hover-bg theme-surface"
                                }
                            `}
                        >
                            {lang.label}
                        </button>
                    ))
                }
            </div>
        );
    }
    const [open,setOpen] = useState(false);
    const ref = useRef(null);
    useDropdown(
        ref,
        () => setOpen(false)
    );
    const current = languages.find( lang => lang.code === language );
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
                🌐 {current.label} ▾
            </button>
            {
                open && (
                    <div
                        className="
                            absolute
                            right-0
                            mt-2
                            bg-[var(--color-surface)]
                            shadow-lg
                            rounded-lg
                            overflow-hidden
                            w-22
                            z-50
                        "
                    >
                        {
                            languages.map(lang => (
                                <button
                                    key={lang.code}
                                    onClick={() => {
                                        setLanguage(lang.code);
                                        setOpen(false);

                                    }}
                                    className={`
                                        block
                                        w-full
                                        px-4
                                        py-3
                                        text-left
                                        cursor-pointer
                                        theme-hover-bg
                                        ${
                                            language === lang.code
                                            ?
                                            "font-bold"
                                            :
                                            ""
                                        }
                                    `}
                                >
                                    {lang.label}
                                </button>
                            ))
                        }
                    </div>
                )
            }
        </div>
    );
}