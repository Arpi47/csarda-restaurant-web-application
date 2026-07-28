import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../contexts/LanguageContext";
import PageHeader from "../components/common/PageHeader";
import { MapPin, Phone, Mail, Clock } from "lucide-react";
import {
    FaFacebookF,
    FaInstagram,
    FaTiktok,
    FaYoutube,
    FaXTwitter,
    FaLinkedinIn,
    FaWhatsapp,
    FaTelegram,
    FaSnapchat,
    FaPinterestP,
    FaReddit,
    FaThreads,
    FaDiscord,
    FaTwitch,
    FaVk,
    FaWeixin,
    FaFacebookMessenger,
} from "react-icons/fa6";

export default function Contact() {
    const { t } = useLanguage();
    const [contactInformation, setContactInformation] = useState(null);
    const [socialLinks, setSocialLinks] = useState([]);
    useEffect(() => {
        fetch(`${import.meta.env.VITE_API_URL}/contact`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch contact data.");
                }
                return response.json();
            })
            .then((data) => {
                setContactInformation(data.information);
                setSocialLinks(data.socialLinks);
            })
            .catch((error) => {
                console.error("Failed to load contact data:", error);
            });
    }, []);
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
                                        {contactInformation?.phone || "+381 XX XXX XXXX"}
                                    </p>
                                    <p
                                        className="
                                        flex
                                        items-center
                                        gap-4
                                    "
                                    >
                                        <Mail className="text-[var(--color-secondary)]" />
                                        {contactInformation?.email || "info@csarda.com"}
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
                                            flex-wrap
                                            gap-4
                                        "
                                    >
                                        {socialLinks.map((item) => {
                                            const icons = {
                                                facebook: <FaFacebookF size={20} />,
                                                instagram: <FaInstagram size={20} />,
                                                tiktok: <FaTiktok size={20} />,
                                                youtube: <FaYoutube size={20} />,
                                                x: <FaXTwitter size={20} />,
                                                linkedin: <FaLinkedinIn size={20} />,
                                                whatsapp: <FaWhatsapp size={20} />,
                                                telegram: <FaTelegram size={20} />,
                                                snapchat: <FaSnapchat size={20} />,
                                                pinterest: <FaPinterestP size={20} />,
                                                reddit: <FaReddit size={20} />,
                                                threads: <FaThreads size={20} />,
                                                discord: <FaDiscord size={20} />,
                                                twitch: <FaTwitch size={20} />,
                                                vk: <FaVk size={20} />,
                                                wechat: <FaWeixin size={20} />,
                                                messenger: <FaFacebookMessenger size={20} />,
                                            };

                                            return (
                                                <a
                                                    key={item.platform}
                                                    href={item.url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    title={item.platform}
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
                                                    {icons[item.platform]}
                                                </a>
                                            );
                                        })}
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
