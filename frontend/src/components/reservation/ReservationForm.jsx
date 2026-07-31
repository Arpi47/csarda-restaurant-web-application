import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import client from "../../api/client";
import { useLanguage } from "../../contexts/LanguageContext";
import { useGoogleReCaptcha } from "react-google-recaptcha-v3";
import PageHeader from "../common/PageHeader";

export default function ReservationForm() {
    const { t, language } = useLanguage();
    const { executeRecaptcha } = useGoogleReCaptcha();
    const [form, setForm] = useState({
        date: "",
        time: "",
        guests: 1,
        event_type_id: "",
    });
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
    const [eventTypes, setEventTypes] = useState([]);
    useEffect(() => {
        async function loadEventTypes() {
            try {
                const response = await client.get("/reservation-event-types");
                setEventTypes(response.data);
            } catch (error) {
                console.error("Failed to load reservation event types:", error);
            }
        }
        loadEventTypes();
    }, []);
    function handleChange(e) {
        setForm({
            ...form,
            [e.target.name]: e.target.value,
        });
    }
    async function submit(e) {
        e.preventDefault();
        setMessage("");
        setError("");
        try {
            if (!executeRecaptcha) {
                setError(t("recaptcha_not_loaded"));
                return;
            }
            const token = await executeRecaptcha("reservation");
            const response = await client.post("/reservation", {
                ...form,
                "g-recaptcha-response": token,
            });
            if (response.data.success) {
                setMessage(t("reservation.success"));
                setForm({
                    date: "",
                    time: "",
                    guests: 1,
                    event_type_id: "",
                });
            } else {
                setError(response.data.message);
            }
        } catch (err) {
            console.error("Reservation error:", err);
            setError(err.response?.data?.message ?? t("something_went_wrong"));
        }
    }
    const inputClass = `
        theme-input
        w-full
        rounded-xl
        px-4
        py-3
        bg-[var(--color-background)]
        border
        border-[var(--color-border)]
        text-[var(--color-text)]
        outline-none
        transition
        focus:border-[var(--color-secondary)]
        focus:ring-2
        focus:ring-[var(--color-secondary)]
        placeholder:text-[var(--color-muted)]
    `;
    return (
        <div className="page-container">
            <main
                className="
                px-6
            "
            >
                <div
                    className="
                    max-w-3xl
                    mx-auto
                "
                >
                    <PageHeader
                        title={t("reservation.title")}
                        subtitle={t("reservation.subtitle")}
                    />
                    <motion.form
                        onSubmit={submit}
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
                            flex
                            flex-col
                            gap-5
                            border
                            theme-border
                        "
                    >
                        <div
                            className="
                            grid
                            grid-cols-1
                            md:grid-cols-2
                            gap-5
                        "
                        >
                            <input
                                type="date"
                                name="date"
                                value={form.date}
                                onChange={handleChange}
                                className={inputClass}
                            />
                            <input
                                type="time"
                                name="time"
                                value={form.time}
                                onChange={handleChange}
                                className={inputClass}
                            />
                        </div>
                        <input
                            type="number"
                            name="guests"
                            min="1"
                            max="70"
                            value={form.guests}
                            onChange={handleChange}
                            placeholder={t("reservation.guests")}
                            className={inputClass}
                        />
                        <div className="relative">
                            <select
                                name="event_type_id"
                                value={form.event_type_id}
                                onChange={handleChange}
                                className={`${inputClass} appearance-none pr-12`}
                                required
                            >
                                <option value="" disabled>
                                    {t("reservation.eventType")}
                                </option>
                                {eventTypes.map((eventType) => {
                                    const nameField =
                                        language === "hu"
                                            ? "name_hu"
                                            : language === "sr"
                                            ? "name_sr"
                                            : language === "sr_cyrl"
                                                ? "name_sr_cyrl"
                                                : "name_en";
                                    return (
                                        <option
                                            key={eventType.id}
                                            value={eventType.id}
                                        >
                                            {eventType[nameField]}
                                        </option>
                                    );
                                })}
                            </select>
                            <div
                                className="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    right-4
                                    flex
                                    items-center
                                "
                            >
                                <svg
                                    className="w-5 h-5 text-[var(--color-text)]"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="m6 9 6 6 6-6"
                                    />
                                </svg>
                            </div>
                        </div>
                        <button
                            className="
                                theme-button
                                rounded-full
                                py-4
                                mt-4
                                font-semibold
                                text-lg
                                hover:cursor-pointer
                                hover:scale-105
                                transition
                            "
                        >
                            {t("reservation.send")}
                        </button>
                        {message && (
                            <div
                                className="
                                rounded-xl
                                bg-green-100
                                text-green-700
                                p-4
                                text-center
                            "
                            >
                                {message}
                            </div>
                        )}
                        {error && (
                            <div
                                className="
                                rounded-xl
                                bg-red-100
                                text-red-700
                                p-4
                                text-center
                            "
                            >
                                {error}
                            </div>
                        )}
                    </motion.form>
                </div>
            </main>
        </div>
    );
}
