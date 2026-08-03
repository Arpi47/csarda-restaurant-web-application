import { useState, useRef, useEffect } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { Link, NavLink } from "react-router-dom";
import LanguageSwitcher from "../common/LanguageSwitcher";
import UserMenu from "../common/UserMenu";
import { useLanguage } from "../../contexts/LanguageContext";
import ThemeSwitcher from "../common/ThemeSwitcher";

export default function Navbar() {
    const [menuOpen, setMenuOpen] = useState(false);
    const menuRef = useRef(null);
    const { t } = useLanguage();
    const [contactInformation, setContactInformation] = useState(null);
    const [openingHours, setOpeningHours] = useState(null);
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
                const openingHoursData = await openingHoursResponse.json();
                return {
                    contactData,
                    openingHoursData,
                };
            })
            .then(
                ({
                    contactData,
                    openingHoursData,
                }) => {
                    setContactInformation(
                        contactData.information,
                    );
                    setOpeningHours({
                        weekly: openingHoursData.kitchen_weekly || [],
                        special: openingHoursData.kitchen_special || [],
                        holidays: openingHoursData.serbian_holidays || [],
                    });
                },
            )
            .catch((error) => {
                console.error(
                    "Failed to load contact or kitchen opening hours data:",
                    error,
                );
            });
    }, []);
    useEffect(() => {
        function handleKey(e) {
            if (e.key === "Escape") {
                setMenuOpen(false);
            }
        }
        function handleClick(e) {
            if (
                menuRef.current &&
                !menuRef.current.contains(e.target)
            ) {
                setMenuOpen(false);
            }
        }
        document.addEventListener(
            "keydown",
            handleKey,
        );
        document.addEventListener(
            "mousedown",
            handleClick,
        );
        return () => {
            document.removeEventListener(
                "keydown",
                handleKey,
            );
            document.removeEventListener(
                "mousedown",
                handleClick,
            );
        };
    }, []);
    const links = [
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
    const getTodayKitchenHours = () => {
        if (!openingHours) {
            return null;
        }

        const today = new Date();
        const todayDate =
            today.toLocaleDateString("en-CA");

        const specialKitchenHours =
            openingHours.special?.find(
                (item) =>
                    String(item.date).slice(0, 10) === todayDate,
            );

        if (specialKitchenHours) {
            return {
                is_active:
                    Boolean(
                        specialKitchenHours.is_active,
                    ),
                open_time:
                    specialKitchenHours.open_time,
                close_time:
                    specialKitchenHours.close_time,
                last_reservation_time:
                    specialKitchenHours.last_reservation_time,
            };
        }

        const serbianHoliday =
            openingHours.holidays?.find(
                (item) =>
                    String(item.date).slice(0, 10) === todayDate,
            );

        if (serbianHoliday) {
            return {
                is_active:
                    Boolean(
                        serbianHoliday.kitchen_is_active,
                    ),
                open_time:
                    serbianHoliday.kitchen_open_time,
                close_time:
                    serbianHoliday.kitchen_close_time,
                last_reservation_time:
                    serbianHoliday.kitchen_last_order_time,
            };
        }

        const javascriptDay =
            today.getDay();

        const databaseDay =
            javascriptDay === 0
                ? 7
                : javascriptDay;

        const weeklyKitchenHours =
            openingHours.weekly?.find(
                (item) =>
                    Number(
                        item.day_of_week,
                    ) === databaseDay,
            );

        if (!weeklyKitchenHours) {
            return null;
        }

        return {
            is_active:
                Boolean(
                    weeklyKitchenHours.is_active,
                ),
            open_time:
                weeklyKitchenHours.open_time,
            close_time:
                weeklyKitchenHours.close_time,
            last_reservation_time:
                weeklyKitchenHours.last_reservation_time,
        };
    };
    const todayKitchenHours = getTodayKitchenHours();
    const getAvailabilityText = () => {
        if (!openingHours) {
            return t("loading");
        }
        if (!todayKitchenHours) {
            return t("days.closed");
        }
        if (!todayKitchenHours.is_active) {
            return t("days.closed");
        }
        if (
            !todayKitchenHours.open_time ||
            !todayKitchenHours.last_reservation_time
        ) {
            return t("days.closed");
        }
        const openTime = todayKitchenHours.open_time.slice(
                0,
                5,
            );
        const lastOrderTime = todayKitchenHours.last_reservation_time.slice(
                0,
                5,
            );
        return t("availability")
            .replace(
                "{open}",
                openTime,
            )
            .replace(
                "{close}",
                lastOrderTime,
            );
    };
    return (
        <header
            className="
                relative
                z-50
                bg-[var(--color-surface)]
                shadow-sm
            "
        >
            <nav
                className="
                    relative
                    max-w-7xl
                    mx-auto
                    px-6
                    py-4
                "
            >
                <div
                    className="
                        relative
                        flex
                        items-center
                        justify-between
                    "
                >
                    <Link
                        to="/"
                        className="
                            text-2xl
                            navbar:text-3xl
                            font-bold
                            whitespace-nowrap
                            px-4
                            py-1
                            border
                            border-[var(--color-secondary)]
                            shrink-0
                            rounded-full
                            bg-[var(--color-hover-bg)]
                        "
                    >
                        Csárda
                    </Link>
                    <div
                        className="
                            hidden
                            desktop:flex
                            items-center
                            ml-4
                            mr-auto
                            pr-3
                            navbar:pr-4
                        "
                    >
                        <div
                            className="
                                flex
                                items-center
                                gap-2
                                whitespace-nowrap
                            "
                        >
                            {links.map((link) => (
                                <NavLink
                                    key={link.path}
                                    to={link.path}
                                    className={({
                                        isActive,
                                    }) =>
                                        `
                                            relative
                                            px-3
                                            navbar:px-4
                                            py-1.5
                                            navbar:py-2
                                            rounded-full
                                            transition-colors
                                            duration-300
                                            theme-hover
                                            whitespace-nowrap
                                            ${
                                                isActive
                                                    ? "font-bold"
                                                    : ""
                                            }
                                        `
                                    }
                                >
                                    {({
                                        isActive,
                                    }) => (
                                        <>
                                            {isActive && (
                                                <motion.div
                                                    layoutId="activeDesktopNav"
                                                    className="
                                                        absolute
                                                        inset-0
                                                        rounded-full
                                                        bg-[var(--color-hover-bg)]
                                                        -z-10
                                                    "
                                                    transition={{
                                                        type: "spring",
                                                        stiffness: 400,
                                                        damping: 30,
                                                    }}
                                                />
                                            )}
                                            <span className="relative z-10">
                                                {t(
                                                    link.name,
                                                )}
                                            </span>
                                        </>
                                    )}
                                </NavLink>
                            ))}
                        </div>
                    </div>
                    <div
                        className="
                            hidden
                            desktop:flex
                            items-center
                            gap-3
                            navbar:gap-4
                            whitespace-nowrap
                        "
                    >
                        <div
                            className="
                                text-center
                                leading-tight
                                px-3
                                navbar:px-5
                                border-l
                                border-r
                                theme-border
                            "
                        >
                            <p
                                className="
                                    text-xs
                                    ordering-info-muted
                                    mb-1
                                "
                            >
                                {t("phoneTitle")}
                            </p>
                            <p
                                className="
                                    block
                                    text-base
                                    navbar:text-lg
                                    font-bold
                                    ordering-info-phone
                                "
                            >
                                {contactInformation?.phone ||
                                    "+381 XX XXX XXXX"}
                            </p>
                            <p
                                className="
                                    text-xs
                                    ordering-info-muted
                                    mt-1
                                "
                            >
                                {getAvailabilityText()}
                            </p>
                        </div>
                        <ThemeSwitcher />
                        <LanguageSwitcher />
                        <UserMenu />
                    </div>
                    <div
                        className="
                            desktop:hidden
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <div
                            className="
                                w-10
                                h-10
                                flex
                                items-center
                                justify-center
                                rounded-full
                            "
                        >
                            <ThemeSwitcher mobile />
                        </div>
                        <button
                            onMouseDown={(e) => {
                                e.stopPropagation();
                            }}
                            onClick={() =>
                                setMenuOpen(
                                    !menuOpen,
                                )
                            }
                            className="
                                w-10
                                h-10
                                rounded-full
                                flex
                                items-center
                                justify-center
                                cursor-pointer
                                text-[var(--color-text)]
                                text-3xl
                                bg-[var(--color-theme-switcher-bg)]
                                transition
                            "
                        >
                            <span
                                className={
                                    menuOpen
                                        ? "translate-y-0"
                                        : "-translate-y-[3px]"
                                }
                            >
                                {menuOpen
                                    ? "✕"
                                    : "☰"}
                            </span>
                        </button>
                    </div>
                </div>
                <AnimatePresence>
                    {menuOpen && (
                        <>
                            <motion.div
                                initial={{opacity: 0}}
                                animate={{opacity: 1}}
                                exit={{opacity: 0}}
                                transition={{duration: 0}}
                                className="
                                    absolute
                                    left-0
                                    right-0
                                    top-full
                                    z-[60]
                                    desktop:hidden
                                    border-t
                                "
                            />
                            <motion.div
                                ref={menuRef}
                                initial={{
                                    opacity: 0,
                                    y: -40,
                                }}
                                animate={{
                                    opacity: 1,
                                    y: 0,
                                }}
                                exit={{
                                    opacity: 0,
                                    y: -40,
                                }}
                                transition={{
                                    type: "spring",
                                    stiffness: 350,
                                    damping: 30,
                                }}
                                className="
                                    absolute
                                    left-0
                                    right-0
                                    top-full
                                    z-50
                                    desktop:hidden
                                    bg-[var(--color-surface)]
                                    shadow-lg
                                    px-6
                                    py-5
                                "
                            >
                                <div
                                    className="
                                        flex
                                        flex-col
                                        items-center
                                        gap-3
                                    "
                                >
                                    {links.map(
                                        (link) => (
                                            <NavLink
                                                key={
                                                    link.path
                                                }
                                                to={
                                                    link.path
                                                }
                                                onClick={() =>
                                                    setMenuOpen(
                                                        false,
                                                    )
                                                }
                                                className={({
                                                    isActive,
                                                }) =>
                                                    `
                                                        w-full
                                                        py-3
                                                        px-4
                                                        text-center
                                                        rounded-xl
                                                        transition-all
                                                        duration-200
                                                        theme-hover
                                                        hover:bg-[var(--color-hover-bg)]
                                                        ${
                                                            isActive
                                                                ? "font-bold bg-[var(--color-hover-bg)]"
                                                                : ""
                                                        }
                                                    `
                                                }
                                            >
                                                {t(
                                                    link.name,
                                                )}
                                            </NavLink>
                                        ),
                                    )}
                                    <hr
                                        className="
                                            my-3
                                            w-full
                                        "
                                    />
                                    <div
                                        className="
                                            pt-2
                                            flex
                                            justify-center
                                            w-full
                                        "
                                    >
                                        <LanguageSwitcher mobile />
                                    </div>
                                </div>
                            </motion.div>
                        </>
                    )}
                </AnimatePresence>
            </nav>
        </header>
    );
}
