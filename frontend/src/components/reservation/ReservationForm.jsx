import { useState } from "react";
import { motion } from "framer-motion";
import client from "../../api/client";
import { useLanguage } from "../../contexts/LanguageContext";
import { useGoogleReCaptcha } from "react-google-recaptcha-v3";
import PageHeader from "../common/PageHeader";

export default function ReservationForm() {
    const { t } = useLanguage();
    const { executeRecaptcha } = useGoogleReCaptcha();
    const [form, setForm] = useState({
        first_name: "",
        last_name: "",
        email: "",
        date: "",
        time: "",
        guests: 1,
    });
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
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
                    first_name: "",
                    last_name: "",
                    email: "",
                    date: "",
                    time: "",
                    guests: 1,
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
                                name="first_name"
                                value={form.first_name}
                                onChange={handleChange}
                                placeholder={t("reservation.firstName")}
                                className={inputClass}
                            />
                            <input
                                name="last_name"
                                value={form.last_name}
                                onChange={handleChange}
                                placeholder={t("reservation.lastName")}
                                className={inputClass}
                            />
                        </div>
                        <input
                            type="email"
                            name="email"
                            value={form.email}
                            onChange={handleChange}
                            placeholder="Email"
                            className={inputClass}
                        />
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
