import { useState } from "react";
import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { useGoogleReCaptcha } from "react-google-recaptcha-v3";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { forgotPassword } from "../api/auth";

export default function ForgotPassword() {
    const { t } = useLanguage();
    const { executeRecaptcha } = useGoogleReCaptcha();
    const [email, setEmail] = useState("");
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(false);
    const inputClass = `
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
    `;
    async function handleSubmit(e) {
        e.preventDefault();
        setMessage("");
        setError("");
        if (!executeRecaptcha) {
            setError("Captcha is not ready");
            return;
        }
        setLoading(true);
        try {
            const token = await executeRecaptcha("forgot_password");
            const response = await forgotPassword({
                email,
                recaptcha_token: token,
            });
            setMessage(t(response.message));
        } catch (error) {
            console.error(error);
            if (error.response?.data?.message) {
                setError(t(error.response.data.message));
            } else {
                setError(t("something_went_wrong"));
            }
        } finally {
            setLoading(false);
        }
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
                    max-w-md
                    mx-auto
                "
                >
                    <PageHeader
                        title={t("forgot_password.title")}
                        subtitle={t("forgot_password.subtitle")}
                    />
                    <motion.form
                        onSubmit={handleSubmit}
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
                            border
                            theme-border
                            flex
                            flex-col
                            gap-5
                        "
                    >
                        {message && (
                            <div
                                className="
                                text-green-600
                                text-center
                            "
                            >
                                {message}
                            </div>
                        )}
                        {error && (
                            <div
                                className="
                                text-red-500
                                text-center
                            "
                            >
                                {error}
                            </div>
                        )}
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder={t("email")}
                            className={inputClass}
                            required
                        />
                        <button
                            disabled={loading}
                            className="
                                theme-button
                                rounded-full
                                py-4
                                font-semibold
                                text-lg
                                transition
                                hover:cursor-pointer
                                hover:scale-105
                                disabled:opacity-50
                            "
                        >
                            {loading
                                ? t("loading")
                                : t("forgot_password.button")}
                        </button>
                        <div
                            className="
                            text-center
                            theme-muted
                        "
                        >
                            <Link
                                to="/login"
                                className="
                                    hover:text-[var(--color-secondary)]
                                "
                            >
                                {t("back_to_login")}
                            </Link>
                        </div>
                    </motion.form>
                </div>
            </main>
        </div>
    );
}
