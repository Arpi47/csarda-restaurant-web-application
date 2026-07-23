import { motion } from "framer-motion";
import { useLanguage } from "../contexts/LanguageContext";
import PageHeader from "../components/common/PageHeader";
import { MapPin, Phone, Mail, Clock } from "lucide-react";
import { FaFacebookF, FaInstagram, FaTiktok, FaYoutube } from "react-icons/fa";

export default function Contact() {
    const { t } = useLanguage();
    const socialLinks = [
        {
            name: "Facebook",
            icon: <FaFacebookF size={20} />,
            url: "#",
        },
        {
            name: "Instagram",
            icon: <FaInstagram size={20} />,
            url: "#",
        },
        {
            name: "TikTok",
            icon: <FaTiktok size={20} />,
            url: "#",
        },
        {
            name: "YouTube",
            icon: <FaYoutube size={20} />,
            url: "#",
        },
    ];
    return (
        <div className="page-container">
            <main
                className="
                py-12
                px-6
            "
            >
                <div
                    className="
                    max-w-5xl
                    mx-auto
                "
                >
                    <PageHeader
                        title={t("contact.title")}
                        subtitle={t("contact.subtitle")}
                    />
                    <motion.div
                        initial={{
                            opacity: 0,
                            y: 30,
                        }}
                        animate={{
                            opacity: 1,
                            y: 0,
                        }}
                        transition={{
                            duration: 0.6,
                        }}
                        className="
                            theme-card
                            rounded-3xl
                            shadow-xl
                            p-8
                            md:p-10
                            border
                            theme-border
                        "
                    >
                        <div
                            className="
                            grid
                            md:grid-cols-2
                            gap-10
                            items-stretch
                        "
                        >
                            {/* CONTACT INFO */}
                            <div className="h-full">
                                <h2
                                    className="
                                        text-3xl
                                        font-bold
                                        mb-8
                                    "
                                >
                                    {t("contact.information")}
                                </h2>
                                <div
                                    className="
                                        flex
                                        flex-col
                                        gap-6
                                        theme-muted
                                    "
                                >
                                    <p
                                        className="
                                        flex
                                        items-center
                                        gap-4
                                    "
                                    >
                                        <MapPin className="text-[var(--color-secondary)]" />
                                        Szabadka / Subotica
                                    </p>
                                    <p
                                        className="
                                        flex
                                        items-center
                                        gap-4
                                    "
                                    >
                                        <Phone className="text-[var(--color-secondary)]" />
                                        +381 xx xxx xxxx
                                    </p>
                                    <p
                                        className="
                                        flex
                                        items-center
                                        gap-4
                                    "
                                    >
                                        <Mail className="text-[var(--color-secondary)]" />
                                        info@csarda.com
                                    </p>
                                    <p
                                        className="
                                        flex
                                        items-center
                                        gap-4
                                    "
                                    >
                                        <Clock className="text-[var(--color-secondary)]" />
                                        {t("contact.weekdays")}: 10:00 - 22:00
                                    </p>
                                </div>
                                {/* SOCIAL */}
                                <div className="mt-10">
                                    <h3
                                        className="
                                        text-xl
                                        font-semibold
                                        mb-4
                                    "
                                    >
                                        {t("contact.social")}
                                    </h3>
                                    <div
                                        className="
                                        flex
                                        gap-4
                                    "
                                    >
                                        {socialLinks.map((item) => (
                                            <a
                                                key={item.name}
                                                href={item.url}
                                                target="_blank"
                                                rel="noreferrer"
                                                title={item.name}
                                                className="
                                                        w-10
                                                        h-10
                                                        rounded-full
                                                        bg-[var(--color-overlay)]
                                                        flex
                                                        items-center
                                                        justify-center
                                                        text-[var(--color-secondary)]
                                                        hover:bg-[var(--color-secondary)]
                                                        hover:text-black
                                                        transition
                                                    "
                                            >
                                                {item.icon}
                                            </a>
                                        ))}
                                    </div>
                                </div>
                            </div>
                            {/* MAP */}
                            <div
                                className="
                                h-full
                                flex
                                flex-col
                            "
                            >
                                <h2
                                    className="
                                    text-3xl
                                    font-bold
                                    mb-8
                                "
                                >
                                    {t("contact.location")}
                                </h2>
                                <div
                                    className="
                                    flex-1
                                    overflow-hidden
                                    rounded-2xl
                                    border
                                    theme-border
                                    shadow-lg
                                    min-h-[350px]
                                "
                                >
                                    <iframe
                                        title="Csárda location"
                                        src="https://www.google.com/maps?q=Subotica,Serbia&output=embed"
                                        className="
                                            w-full
                                            h-full
                                        "
                                        style={{
                                            border: 0,
                                        }}
                                        loading="lazy"
                                        allowFullScreen
                                        referrerPolicy="no-referrer-when-downgrade"
                                    />
                                </div>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </main>
        </div>
    );
}
