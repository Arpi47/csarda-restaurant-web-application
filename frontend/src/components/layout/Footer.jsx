import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useLanguage } from "../../contexts/LanguageContext";
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

export default function Footer() {
const { t } = useLanguage();
const [contactInformation, setContactInformation] = useState(null);
const [socialLinks, setSocialLinks] = useState([]);
const [restaurantOpeningHours, setRestaurantOpeningHours] =
useState([]);
const [kitchenOpeningHours, setKitchenOpeningHours] = useState([]);
useEffect(() => {
    Promise.all([
        fetch(`${import.meta.env.VITE_API_URL}/contact`),
        fetch(`${import.meta.env.VITE_API_URL}/opening-hours`),
    ])
        .then(async ([contactResponse, openingHoursResponse]) => {
            if (
                !contactResponse.ok ||
                !openingHoursResponse.ok
            ) {
                throw new Error(
                    "Failed to fetch contact or opening hours data.",
                );
            }
            const contactData = await contactResponse.json();
            const openingHoursData =
                await openingHoursResponse.json();
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
    }) => ( 
    <div
        className="
            flex
            flex-col
            gap-2
            min-w-0
            items-center
            min-[768px]:items-start
        "
    > 
        <h3
            className="
                text-xs
                min-[370px]:text-sm
                min-[900px]:text-base
                lg:text-lg
                xl:text-xl
                font-semibold
                mb-2
            "
        >
            {t("contact.openingHours")}
        </h3>
        <div
            className="
                flex
                flex-col
                gap-1
                theme-muted
                text-[11px]
                min-[370px]:text-sm
                min-[768px]:text-[11px]
                min-[900px]:text-xs
                lg:text-sm
                xl:text-base
                w-fit
                max-w-full
            "
        >
            <div
                className="
                    grid
                    grid-cols-[80px_100px_100px]
                    min-[370px]:grid-cols-[90px_110px_110px]
                    min-[768px]:grid-cols-[100px_minmax(0,1fr)_minmax(0,1fr)]
                    min-[900px]:grid-cols-[105px_minmax(0,1fr)_minmax(0,1fr)]
                    lg:grid-cols-[110px_minmax(0,1fr)_minmax(0,1fr)]
                    gap-x-2
                    min-[370px]:gap-x-5
                    min-[768px]:gap-x-2
                    min-[900px]:gap-x-3
                    lg:gap-x-4
                    xl:gap-x-16
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
                        restaurantDay.day_of_week,
                );
                return (
                    <div
                        key={restaurantDay.day_of_week}
                        className="
                            grid
                            grid-cols-[80px_100px_100px]
                            min-[370px]:grid-cols-[90px_110px_110px]
                            min-[768px]:grid-cols-[100px_minmax(0,1fr)_minmax(0,1fr)]
                            min-[900px]:grid-cols-[105px_minmax(0,1fr)_minmax(0,1fr)]
                            lg:grid-cols-[110px_minmax(0,1fr)_minmax(0,1fr)]
                            gap-x-2
                            min-[370px]:gap-x-5
                            min-[768px]:gap-x-2
                            min-[900px]:gap-x-3
                            lg:gap-x-4
                            xl:gap-x-16
                            items-center
                        "
                    >
                        <span className="whitespace-nowrap text-left">
                            {t(
                                getDayTranslationKey(
                                    restaurantDay.day_of_week,
                                ),
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
                px-4
                md:px-6
                py-16
            "
        >
            <div
                className="
                    max-w-7xl
                    mx-auto
                    grid
                    grid-cols-1
                    min-[768px]:grid-cols-[1.15fr_0.7fr_1.9fr_1.25fr]
                    min-[900px]:grid-cols-[1.1fr_0.65fr_1.9fr_1.2fr]
                    lg:grid-cols-[1fr_0.5fr_2fr_1fr]
                    gap-5
                    min-[900px]:gap-6
                    lg:gap-8
                    xl:gap-12
                    text-center
                    min-[768px]:text-left
                    min-[768px]:items-start
                "
            >
                <div className="min-w-0">
                    <h3
                        className="
                            text-2xl
                            lg:text-3xl
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
                            text-sm
                            lg:text-base
                            max-w-xl
                            mx-auto
                            min-[768px]:mx-0
                        "
                    >
                        {t("footer.description")}
                    </p>
                </div>
                <div className="min-w-0">
                    <h4
                        className="
                            text-base
                            min-[900px]:text-lg
                            lg:text-xl
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
                            gap-x-4
                            gap-y-2
                            min-[768px]:flex-col
                            min-[768px]:items-start
                            min-[768px]:gap-2
                            lg:gap-3
                        "
                    >
                        {links.map((link) => (
                            <Link
                                key={link.path}
                                to={link.path}
                                className="
                                    theme-footer-muted
                                    text-sm
                                    lg:text-base
                                    hover:text-[var(--color-secondary)]
                                    transition
                                "
                            >
                                {t(link.name)}
                            </Link>
                        ))}
                    </div>
                </div>
                <div
                    className="
                        flex
                        flex-col
                        gap-8
                        justify-center
                        min-[768px]:justify-start
                        min-w-0
                    "
                >
                    <OpeningHours
                        restaurantHours={restaurantOpeningHours}
                        kitchenHours={kitchenOpeningHours}
                    />
                </div>
                <div className="min-w-0">
                    <h4
                        className="
                            text-base
                            min-[900px]:text-lg
                            lg:text-xl
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
                            gap-3
                            lg:gap-4
                            theme-footer-muted
                            items-center
                            min-[768px]:items-start
                            text-sm
                            lg:text-base
                        "
                    >
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                min-[768px]:justify-start
                                gap-2
                                lg:gap-3
                            "
                        >
                            <MapPin
                                size={16}
                                className="lg:w-[18px] lg:h-[18px] text-[var(--color-secondary)]"
                            />
                            {t("footer.city")}
                        </p>
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                min-[768px]:justify-start
                                gap-2
                                lg:gap-3
                            "
                        >
                            <Phone
                                size={16}
                                className="lg:w-[18px] lg:h-[18px] text-[var(--color-secondary)]"
                            />
                            {contactInformation?.phone ||
                                "+381 XX XXX XXXX"}
                        </p>
                        <p
                            className="
                                flex
                                items-center
                                justify-center
                                min-[768px]:justify-start
                                gap-2
                                lg:gap-3
                            "
                        >
                            <Mail
                                size={16}
                                className="lg:w-[18px] lg:h-[18px] text-[var(--color-secondary)]"
                            />
                            {contactInformation?.email ||
                                "info@csarda.com"}
                        </p>
                        <div className="mt-3 lg:mt-4">
                            <h4
                                className="
                                    theme-footer
                                    text-base
                                    min-[900px]:text-lg
                                    lg:text-xl
                                    font-bold
                                    mb-4
                                    lg:mb-5
                                "
                            >
                                {t("footer.social")}
                            </h4>
                            <div
                                className="
                                    flex
                                    flex-wrap
                                    gap-2
                                    lg:gap-4
                                    justify-center
                                    min-[768px]:justify-start
                                "
                            >
                                {socialLinks.map((item) => {
                                    const icons = {
                                        facebook: (
                                            <FaFacebookF size={18} />
                                        ),
                                        instagram: (
                                            <FaInstagram size={18} />
                                        ),
                                        tiktok: (
                                            <FaTiktok size={18} />
                                        ),
                                        youtube: (
                                            <FaYoutube size={18} />
                                        ),
                                        x: (
                                            <FaXTwitter size={18} />
                                        ),
                                        linkedin: (
                                            <FaLinkedinIn size={18} />
                                        ),
                                        whatsapp: (
                                            <FaWhatsapp size={18} />
                                        ),
                                        telegram: (
                                            <FaTelegram size={18} />
                                        ),
                                        snapchat: (
                                            <FaSnapchat size={18} />
                                        ),
                                        pinterest: (
                                            <FaPinterestP size={18} />
                                        ),
                                        reddit: (
                                            <FaReddit size={18} />
                                        ),
                                        threads: (
                                            <FaThreads size={18} />
                                        ),
                                        discord: (
                                            <FaDiscord size={18} />
                                        ),
                                        twitch: (
                                            <FaTwitch size={18} />
                                        ),
                                        vk: (
                                            <FaVk size={18} />
                                        ),
                                        wechat: (
                                            <FaWeixin size={18} />
                                        ),
                                        messenger: (
                                            <FaFacebookMessenger
                                                size={18}
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
                                                w-9
                                                h-9
                                                lg:w-10
                                                lg:h-10
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
                    © {new Date().getFullYear()} Csárda.{" "}
                    {t("footer.rights")}
                </p>
                <p
                    className="
                        text-xs
                        mt-4
                        theme-footer-muted
                        opacity-70
                    "
                >
                    This site is protected by reCAPTCHA and the Google{" "}
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
                    </a>{" "}
                    and{" "}
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
                    </a>{" "}
                    apply.
                </p>
            </div>
        </footer>
    );
}
