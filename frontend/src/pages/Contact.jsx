import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { useLanguage } from "../contexts/LanguageContext";
import PageHeader from "../components/common/PageHeader";
import { MapPin, Phone, Mail } from "lucide-react";
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
    const [restaurantOpeningHours, setRestaurantOpeningHours] = useState([]);
    const [kitchenOpeningHours, setKitchenOpeningHours] = useState([]);
    useEffect(() => {
        Promise.all([
            fetch(`${import.meta.env.VITE_API_URL}/contact`),
            fetch(`${import.meta.env.VITE_API_URL}/opening-hours`),
        ])
            .then(async ([contactResponse, openingHoursResponse]) => {
                if (!contactResponse.ok || !openingHoursResponse.ok) {
                    throw new Error(
                        "Failed to fetch contact or opening hours data.",
                    );
                }
                const contactData = await contactResponse.json();
                const openingHoursData = await openingHoursResponse.json();
                return {
                    contactData,
                    openingHoursData,
                };
            })
            .then(({ contactData, openingHoursData }) => {
                setContactInformation(contactData.information);
                setSocialLinks(contactData.socialLinks);
                setRestaurantOpeningHours(
                    openingHoursData.restaurant_weekly || [],
                );
                setKitchenOpeningHours(
                    openingHoursData.kitchen_weekly || [],
                );
            })
            .catch((error) => {
                console.error(
                    "Failed to load contact or opening hours data:",
                    error,
                );
            });
    }, []);
    const getDayTranslationKey = (dayOfWeek) => {
        const days = [
            "days.monday",
            "days.tuesday",
            "days.wednesday",
            "days.thursday",
            "days.friday",
            "days.saturday",
            "days.sunday",
        ];
        return days[dayOfWeek - 1];
    };
    const OpeningHours = ({
        restaurantHours,
        kitchenHours,
        }) => ( <div
            className="
                flex
                flex-col
                gap-2
            "
        > <h3
                className="
                    text-xs
                    min-[350px]:text-sm
                    min-[400px]:text-base
                    min-[450px]:text-lg
                    min-[768px]:text-base
                    min-[850px]:text-xl
                    font-semibold
                    mb-2
                "
            >
        {t("contact.openingHours")} </h3>
            <div
                className="
                    flex
                    flex-col
                    gap-1
                    theme-muted
                    text-[11px]
                    min-[350px]:text-xs
                    min-[400px]:text-sm
                    min-[450px]:text-base
                    min-[768px]:text-sm
                    min-[850px]:text-base
                "
            >
                <div
                    className="
                        grid
                        grid-cols-3
                        gap-4
                        min-[768px]:gap-2
                        min-[850px]:gap-4
                        font-semibold
                        mb-2
                    "
                >
                    <span></span>
                    <span className="whitespace-nowrap">
                        {t("restaurant")}
                    </span>
                    <span className="whitespace-nowrap">
                        {t("kitchen")}
                    </span>
                </div>
                {restaurantHours.map((restaurantDay) => {
                    const kitchenDay = kitchenHours.find(
                        (day) =>
                            day.day_of_week ===
                            restaurantDay.day_of_week
                    );
                    return (
                        <div
                            key={restaurantDay.day_of_week}
                            className="
                                grid
                                grid-cols-3
                                gap-4
                                min-[768px]:gap-2
                                min-[850px]:gap-4
                                items-center
                            "
                        >
                            <span className="whitespace-nowrap">
                                {t(
                                    getDayTranslationKey(
                                        restaurantDay.day_of_week
                                    )
                                )}
                            </span>
                            <span className="whitespace-nowrap">
                                {restaurantDay.is_active &&
                                restaurantDay.open_time &&
                                restaurantDay.close_time
                                    ? `${restaurantDay.open_time.slice(0, 5)} - ${restaurantDay.close_time.slice(0, 5)}`
                                    : t("days.closed")}
                            </span>
                            <span className="whitespace-nowrap">
                                {kitchenDay?.is_active &&
                                kitchenDay?.open_time &&
                                kitchenDay?.close_time
                                    ? `${kitchenDay.open_time.slice(0, 5)} - ${kitchenDay.close_time.slice(0, 5)}`
                                    : t("days.closed")}
                            </span>
                        </div>
                    );
                })}
            </div>
        </div>
        );
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
                                    "
                                >
                                    <p
                                        className="
                                            flex
                                            items-center
                                            gap-4
                                            theme-muted
                                        "
                                    >
                                        <MapPin
                                            className="
                                                text-[var(--color-secondary)]
                                            "
                                        />
                                        {t("footer.city")}
                                    </p>
                                    <p
                                        className="
                                            flex
                                            items-center
                                            gap-4
                                            theme-muted
                                        "
                                    >
                                        <Phone
                                            className="
                                                text-[var(--color-secondary)]
                                            "
                                        />
                                        {contactInformation?.phone ||
                                            "+381 XX XXX XXXX"}
                                    </p>
                                    <p
                                        className="
                                            flex
                                            items-center
                                            gap-4
                                            theme-muted
                                        "
                                    >
                                        <Mail
                                            className="
                                                text-[var(--color-secondary)]
                                            "
                                        />
                                        {contactInformation?.email ||
                                            "info@csarda.com"}
                                    </p>
                                    <OpeningHours
                                        restaurantHours={restaurantOpeningHours}
                                        kitchenHours={kitchenOpeningHours}
                                    />
                                </div>
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
                                                facebook: (
                                                    <FaFacebookF size={20} />
                                                ),
                                                instagram: (
                                                    <FaInstagram size={20} />
                                                ),
                                                tiktok: (
                                                    <FaTiktok size={20} />
                                                ),
                                                youtube: (
                                                    <FaYoutube size={20} />
                                                ),
                                                x: (
                                                    <FaXTwitter size={20} />
                                                ),
                                                linkedin: (
                                                    <FaLinkedinIn size={20} />
                                                ),
                                                whatsapp: (
                                                    <FaWhatsapp size={20} />
                                                ),
                                                telegram: (
                                                    <FaTelegram size={20} />
                                                ),
                                                snapchat: (
                                                    <FaSnapchat size={20} />
                                                ),
                                                pinterest: (
                                                    <FaPinterestP size={20} />
                                                ),
                                                reddit: (
                                                    <FaReddit size={20} />
                                                ),
                                                threads: (
                                                    <FaThreads size={20} />
                                                ),
                                                discord: (
                                                    <FaDiscord size={20} />
                                                ),
                                                twitch: (
                                                    <FaTwitch size={20} />
                                                ),
                                                vk: (
                                                    <FaVk size={20} />
                                                ),
                                                wechat: (
                                                    <FaWeixin size={20} />
                                                ),
                                                messenger: (
                                                    <FaFacebookMessenger
                                                        size={20}
                                                    />
                                                ),
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
