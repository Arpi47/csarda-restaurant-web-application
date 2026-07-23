import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import PageHeader from "../components/common/PageHeader";
import { getReservations, deleteReservation } from "../api/reservations";
import { useLanguage } from "../contexts/LanguageContext";

export default function UserReservations() {
    const { t, language } = useLanguage();
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        loadReservations();
    }, []);
    function formatDate(date) {
        const localeMap = {
            en: "en-US",
            hu: "hu-HU",
            sr_lat: "sr-Latn-RS",
            sr_cyr: "sr-Cyrl-RS",
        };
        return new Intl.DateTimeFormat(localeMap[language] ?? "en-US", {
            dateStyle: "long",
            timeStyle: "short",
        }).format(new Date(date));
    }
    async function loadReservations() {
        try {
            const data = await getReservations();
            setReservations(data);
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    }
    async function handleDelete(id) {
        if (!confirm(t("reservations.delete_confirm"))) {
            return;
        }
        await deleteReservation(id);
        setReservations(reservations.filter((item) => item.id !== id));
    }
    if (loading) {
        return (
            <div className="page-container py-20 text-center">
                {t("loading")}
            </div>
        );
    }
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
                        title={t("reservations.title")}
                        subtitle={t("reservations.subtitle")}
                    />
                    <div
                        className="
                        flex
                        flex-col
                        gap-6
                    "
                    >
                        {reservations.length === 0 ? (
                            <p
                                className="
                            text-center
                            theme-muted
                        "
                            >
                                {t("reservations.empty")}
                            </p>
                        ) : (
                            reservations.map((reservation) => (
                                <motion.div
                                    key={reservation.id}
                                    initial={{
                                        opacity: 0,
                                        y: 30,
                                    }}
                                    animate={{
                                        opacity: 1,
                                        y: 0,
                                    }}
                                    className="
                                    theme-card
                                    rounded-3xl
                                    shadow-lg
                                    p-6
                                    border
                                    theme-border
                                "
                                >
                                    <div
                                        className="
                                    flex
                                    justify-between
                                    items-center
                                "
                                    >
                                        <div>
                                            <h3
                                                className="
                                            text-xl
                                            font-bold
                                        "
                                            >
                                                {reservation.fname}{" "}
                                                {reservation.lname}
                                            </h3>
                                            <p className="theme-muted">
                                                {reservation.email}
                                            </p>
                                            <p>
                                                📅{" "}
                                                {formatDate(
                                                    reservation.date_time,
                                                )}
                                            </p>
                                            <p>👥 {reservation.guests}</p>
                                        </div>
                                        <div
                                            className="
                                        flex
                                        flex-col
                                        gap-3
                                        items-end
                                    "
                                        >
                                            <span
                                                className={`reservation-status ${reservation.status}`}
                                            >
                                                {t(
                                                    `reservation_status.${reservation.status}`,
                                                )}
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleDelete(reservation.id)
                                                }
                                                className="
                                                bg-red-600
                                                text-white
                                                px-4
                                                py-2
                                                rounded-full
                                                hover:cursor-pointer
                                            "
                                            >
                                                🗑 {t("delete")}
                                            </button>
                                        </div>
                                    </div>
                                </motion.div>
                            ))
                        )}
                    </div>
                </div>
            </main>
        </div>
    );
}
