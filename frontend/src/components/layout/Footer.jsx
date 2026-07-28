import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useLanguage } from "../../contexts/LanguageContext";
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

export default function Footer() {
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
    const links = [
        {
            name: "nav.home",
            path: "/",
        },
        {
            name: "nav.menu",
            path: "/menu",
        },
        {
            name: "nav.gallery",
            path: "/gallery",
        },
        {
            name: "nav.about",
            path: "/about",
        },
        {
            name: "nav.contact",
            path: "/contact",
        },
        {
            name: "nav.reservation",
            path: "/reservation",
        },
    ];

    return (
        <footer
            className="
                theme-footer
                px-6
                py-16
            "
        >
            <div
                className="
                    max-w-7xl
                    mx-auto
                    grid
                    grid-cols-1
                    md:grid-cols-3
                    gap-12
                    text-center
                    md:text-left
                "
            >
                <div>
                    <h3
                        className="
                            text-3xl
                            font-bold
                            mb-5
                        "
                    >
                        Csárda
                    </h3>
                    <p
                        className="
                            theme-footer-muted
                            leading-relaxed
                            max-w-xl
                            mx-auto
                            md:mx-0
                        "
                    >
                        {t("footer.description")}
                    </p>
                </div>
                <div>
                    <h4
                        className="
                            text-xl
                            font-bold
                            mb-5
                        "
                    >
                        {t("footer.navigation")}
                    </h4>
                    <div
                        className="
                            flex
                            flex-wrap
                            justify-center
                            gap-x-6
                            gap-y-3
                            md:flex-col
                            md:items-start
                            md:gap-3
                        "
                    >
                        {links.map((link) => (
                            <Link
                                key={link.path}
                                to={link.path}
                                className="
                                    theme-footer-muted
                                    hover:text-[var(--color-secondary)]
                                    transition
                                "
                            >
                                {t(link.name)}
                            </Link>
                        ))}
                    </div>
                </div>
                <div>
                    <h4
                        className="
                            text-xl
                            font-bold
                            mb-5
                        "
                    >
                        {t("footer.contact")}
                    </h4>
                    <div
                        className="
                            flex
                            flex-col
                            gap-4
                            theme-footer-muted
                            items-center
                            md:items-start
                        "
                    >
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                md:justify-start
                                gap-3
                            "
                        >
                            <MapPin
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {t("footer.city")}
                        </p>
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                md:justify-start
                                gap-3
                            "
                        >
                            <Phone
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {contactInformation?.phone || "+381 XX XXX XXXX"}
                        </p>
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                md:justify-start
                                gap-3
                            "
                        >
                            <Mail
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {contactInformation?.email || "info@csarda.com"}
                        </p>
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                md:justify-start
                                gap-3
                            "
                        >
                            <Clock
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {t("footer.hours")}
                        </p>
                        <div className="mt-4">
                            <h4
                                className="
                                    theme-footer
                                    text-xl
                                    font-bold
                                    mb-5
                                "
                            >
                                {t("footer.social")}
                            </h4>
                            <div
                                className="
                                    flex
                                    flex-wrap
                                    gap-4
                                    justify-center
                                    md:justify-start
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
                                                bg-[var(--footer-social-bg)]
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
                </div>
            </div>
            <div
                className="
                    max-w-7xl
                    mx-auto
                    mt-12
                    pt-6
                    border-t
                    theme-footer-border
                    text-center
                    theme-footer-muted
                "
            >
                <p>
                    © {new Date().getFullYear()} Csárda. {t("footer.rights")}
                </p>

                <p
                    className="
                        text-xs
                        mt-4
                        theme-footer-muted
                        opacity-70
                    "
                >
                    This site is protected by reCAPTCHA and the Google
                    <a
                        href="https://policies.google.com/privacy"
                        target="_blank"
                        rel="noreferrer"
                        className="
                            underline
                            mx-1
                            hover:text-white
                        "
                    >
                        Privacy Policy
                    </a>
                    and
                    <a
                        href="https://policies.google.com/terms"
                        target="_blank"
                        rel="noreferrer"
                        className="
                            underline
                            mx-1
                            hover:text-white
                        "
                    >
                        Terms of Service
                    </a>
                    apply.
                </p>
            </div>
        </footer>
    );
}
