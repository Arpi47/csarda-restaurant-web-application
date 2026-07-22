import { Link } from "react-router-dom";
import { useLanguage } from "../../contexts/LanguageContext";
import { MapPin,Phone,Mail,Clock } from "lucide-react";
import { FaFacebookF,FaInstagram,FaTiktok,FaYoutube,FaGoogle } from "react-icons/fa";

export default function Footer() {
    const { t } = useLanguage();
    const links = [
        {
            name:"nav.home",
            path:"/"
        },
        {
            name:"nav.menu",
            path:"/menu"
        },
        {
            name:"nav.gallery",
            path:"/gallery"
        },
        {
            name:"nav.about",
            path:"/about"
        },
        {
            name:"nav.contact",
            path:"/contact"
        },
        {
            name:"nav.reservation",
            path:"/reservation"
        }
    ];
    const socialLinks = [
        {
            name:"Facebook",
            url:"#",
            icon:<FaFacebookF size={20}/>
        },
        {
            name:"Instagram",
            url:"#",
            icon:<FaInstagram size={20}/>
        },
        {
            name:"TikTok",
            url:"#",
            icon:<FaTiktok size={20}/>
        },
        {
            name:"YouTube",
            url:"#",
            icon:<FaYoutube size={20}/>
        },
    ];
    return (
        <footer className="
            theme-footer
            px-6
            py-16
        ">
            <div className="
                max-w-7xl
                mx-auto
                grid
                grid-cols-1
                md:grid-cols-3
                gap-12
            ">
                <div>
                    <h3 className="
                        text-3xl
                        font-bold
                        mb-5
                    ">
                        Csárda
                    </h3>
                    <p className="
                        theme-footer-muted
                        leading-relaxed
                    ">
                        {t("footer.description")}
                    </p>
                </div>
                <div>
                    <h4 className="
                        text-xl
                        font-bold
                        mb-5
                    ">
                        {t("footer.navigation")}
                    </h4>
                    <div className="
                        flex
                        flex-col
                        gap-3
                    ">
                        {
                            links.map(link => (
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
                            ))
                        }
                    </div>
                </div>
                <div>
                    <h4 className="
                        text-xl
                        font-bold
                        mb-5
                    ">
                        {t("footer.contact")}
                    </h4>
                    <div className="
                        flex
                        flex-col
                        gap-4
                        theme-footer-muted
                    ">
                        <p className="
                            flex
                            items-center
                            gap-3
                        ">
                            <MapPin
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {t("footer.city")}
                        </p>
                        <p className="
                            flex
                            items-center
                            gap-3
                        ">
                            <Phone
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            +381 xx xxx xxxx
                        </p>
                        <p className="
                            flex
                            items-center
                            gap-3
                        ">
                            <Mail
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            info@csarda.com
                        </p>
                        <p className="
                            flex
                            items-center
                            gap-3
                        ">
                            <Clock
                                size={18}
                                className="text-[var(--color-secondary)]"
                            />
                            {t("footer.hours")}
                        </p>
                        <div className="
                            mt-4
                        ">
                            <h4 className="
                                text-xl
                                font-bold
                                mb-5
                            ">
                                {t("footer.social")}
                            </h4>
                            <div className="
                                flex
                                gap-4
                            ">
                                {
                                    socialLinks.map(item => (
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
                                    ))
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="
                max-w-7xl
                mx-auto
                mt-12
                pt-6
                border-t
                theme-footer-border
                text-center
                theme-footer-muted
            ">
                <p>

                    © {new Date().getFullYear()} Csárda.
                    {" "}
                    {t("footer.rights")}
                </p>
                <p className="
                    text-xs
                    mt-4
                    theme-footer-muted
                    opacity-70
                ">
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