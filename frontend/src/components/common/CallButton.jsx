import { useEffect, useState } from "react";
import { Phone } from "lucide-react";

export default function CallButton() {
    const [phone, setPhone] = useState("");
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
                    setPhone(
                        contactData.information?.phone || "",
                    );
                    setOpeningHours({
                        weekly:
                            openingHoursData.kitchen_weekly || [],
                        special:
                            openingHoursData.kitchen_special || [],
                        holidays:
                            openingHoursData.serbian_holidays || [],
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
    const getTodayKitchenHours = () => {
        if (!openingHours) {
            return null;
        }
        const today = new Date();
        const todayDate = today.toLocaleDateString("en-CA");
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
                last_order_time:
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
                last_order_time:
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
            last_order_time:
                weeklyKitchenHours.last_reservation_time,
        };
    };
    const todayKitchenHours = getTodayKitchenHours();
    const isKitchenAvailable = () => {
        if (!todayKitchenHours) {
            return false;
        }
        if (!todayKitchenHours.is_active) {
            return false;
        }
        if (
            !todayKitchenHours.open_time ||
            !todayKitchenHours.close_time ||
            !todayKitchenHours.last_order_time
        ) {
            return false;
        }
        const now = new Date();
        const currentMinutes =
            now.getHours() * 60 +
            now.getMinutes();
        const [openHour, openMinute] =
            todayKitchenHours.open_time
                .slice(0, 5)
                .split(":")
                .map(Number);
        const [closeHour, closeMinute] =
            todayKitchenHours.close_time
                .slice(0, 5)
                .split(":")
                .map(Number);
        const [lastOrderHour, lastOrderMinute] =
            todayKitchenHours.last_order_time
                .slice(0, 5)
                .split(":")
                .map(Number);
        const openMinutes =
            openHour * 60 +
            openMinute;
        const closeMinutes =
            closeHour * 60 +
            closeMinute;
        const lastOrderMinutes =
            lastOrderHour * 60 +
            lastOrderMinute;
        if (
            openMinutes >= closeMinutes
        ) {
            return false;
        }
        if (
            lastOrderMinutes < openMinutes ||
            lastOrderMinutes >= closeMinutes
        ) {
            return false;
        }
        return (
            currentMinutes >= openMinutes &&
            currentMinutes <= lastOrderMinutes
        );
    };
    const kitchenAvailable = isKitchenAvailable();
    const phoneLink =
        phone.replace(
            /[^\d+]/g,
            "",
        );
    return (
        <a
            href={
                kitchenAvailable && phoneLink
                    ? `tel:${phoneLink}`
                    : undefined
            }
            aria-label="Call Csárda"
            aria-disabled={!kitchenAvailable}
            className={`
                fixed
                bottom-6
                left-6
                z-50
                w-14
                h-14
                rounded-full
                text-[var(--color-button-text)]
                text-white
                shadow-lg
                flex
                items-center
                justify-center
                transition-all
                duration-300
                desktop:hidden
                ${
                    kitchenAvailable
                        ? `
                            bg-[var(--color-secondary)]
                            hover:bg-[var(--color-button-hover)]
                            hover:cursor-pointer
                        `
                        : `
                            bg-gray-400
                            opacity-60
                            cursor-not-allowed
                        `
                }
            `}
            onClick={(event) => {
                if (!kitchenAvailable) {
                    event.preventDefault();
                }
            }}
        >
            <Phone size={26} />
        </a>
    );
}
