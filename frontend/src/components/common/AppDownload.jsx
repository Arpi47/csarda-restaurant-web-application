import { motion } from "framer-motion";
import { useLanguage } from "../../contexts/LanguageContext";
import { useTheme } from "../../contexts/ThemeContext";
import { useEffect, useState } from "react";

export default function AppDownload() {
    const { t } = useLanguage();
    const { theme } = useTheme();
    const [mobile, setMobile] = useState(false);
    useEffect(() => {
        function checkDevice() {
            setMobile(window.matchMedia("(max-width: 1150px)").matches);
        }
        checkDevice();
        window.addEventListener("resize", checkDevice);
        return () => {
            window.removeEventListener("resize", checkDevice);
        };
    }, []);
    const googlePlayLogo =
        theme === "dark"
            ? "/images/googleplay_d.png"
            : "/images/googleplay_l.png";
    const appStoreLogo =
        theme === "dark" ? "/images/appstore_d.png" : "/images/appstore_l.png";
    return (
        <section
            className="
                py-20
                px-6
                bg-[var(--color-overlay)]
            "
        >
            <motion.div
                initial={{
                    opacity: 0,
                    y: 40,
                }}
                whileInView={{
                    opacity: 1,
                    y: 0,
                }}
                viewport={{
                    once: true,
                }}
                className="
                    max-w-6xl
                    mx-auto
                    text-center
                "
            >
                <h2
                    className="
                        text-4xl
                        font-bold
                        mb-6
                    "
                >
                    {t("appDownload.title")}
                </h2>
                <p
                    className="
                        text-lg
                        text-[var(--color-muted)]
                        mb-10
                    "
                >
                    {t("appDownload.description")}
                </p>
                <div
                    className="
                        flex
                        flex-col
                        app:flex-row
                        gap-10
                        justify-center
                        items-center
                    "
                >
                    <a
                        href="https://play.google.com/store/apps"
                        target="_blank"
                        rel="noopener noreferrer"
                        className={`
                            flex
                            items-center
                            justify-center
                            cursor-pointer
                            transition-transform
                            hover:scale-105
                            ${
                                mobile
                                    ? ""
                                    : `
                                gap-4
                                p-4
                                rounded-3xl
                                bg-[var(--color-surface)]
                                border
                                border-[var(--color-border)]
                                shadow-md
                                `
                            }
                        `}
                    >
                        {!mobile && (
                            <img
                                src="/images/playstore-qr.png"
                                className="
                                        h-24
                                        w-24
                                        object-contain
                                    "
                                alt="Google Play QR"
                            />
                        )}
                        <img
                            src={googlePlayLogo}
                            className={`
                                object-contain
                                ${
                                    mobile
                                        ? `
                                    w-64
                                    drop-shadow-xl
                                    `
                                        : `
                                    h-24
                                    w-auto
                                    `
                                }
                            `}
                            alt="Google Play"
                        />
                    </a>
                    <a
                        href="itms-apps://://apple.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        className={`
                            flex
                            items-center
                            justify-center
                            cursor-pointer
                            transition-transform
                            hover:scale-105
                            ${
                                mobile
                                    ? ""
                                    : `
                                gap-4
                                p-4
                                rounded-3xl
                                bg-[var(--color-surface)]
                                border
                                border-[var(--color-border)]
                                shadow-md
                                `
                            }
                        `}
                    >
                        {!mobile && (
                            <img
                                src="/images/appstore-qr.png"
                                className="
                                        h-24
                                        w-24
                                        object-contain
                                    "
                                alt="App Store QR"
                            />
                        )}
                        <img
                            src={appStoreLogo}
                            className={`
                                object-contain
                                ${
                                    mobile
                                        ? `
                                    w-64
                                    drop-shadow-xl
                                    `
                                        : `
                                    h-24
                                    w-auto
                                    `
                                }
                            `}
                            alt="App Store"
                        />
                    </a>
                </div>
            </motion.div>
        </section>
    );
}
