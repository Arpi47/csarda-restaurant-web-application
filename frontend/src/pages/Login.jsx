import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Link, useNavigate, useSearchParams } from "react-router-dom";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { useAuth } from "../contexts/AuthContext";
import { login } from "../api/auth";

export default function Login() {
    const { t } = useLanguage();
    const { setUser } = useAuth();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    useEffect(() => {
        const isMobile = window.matchMedia("(max-width: 1150px)").matches;
        if (isMobile) {
            navigate("/reservation", {
                replace: true,
            });
        }
    }, [navigate]);
    const [formData, setFormData] = useState({
        email: "",
        password: "",
    });
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(false);
    useEffect(() => {
        const oauthError = searchParams.get("error");
        if (!oauthError) {
            return;
        }
        const translatedError = t(`oauth_errors.${oauthError}`);
        if (
            translatedError &&
            translatedError !== `oauth_errors.${oauthError}`
        ) {
            setError(translatedError);
        } else {
            setError(t("something_went_wrong"));
        }
    }, [searchParams, t]);
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
        placeholder:text-[var(--color-muted)]
    `;
    function handleChange(e) {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    }
    async function handleSubmit(e) {
        e.preventDefault();
        setError("");
        setLoading(true);
        try {
            const response = await login(formData);
            localStorage.setItem("token", response.token);
            setUser(response.user);
            navigate("/");
        } catch (error) {
            console.log(error);
            if (error.response?.data?.message) {
                setError(error.response.data.message);
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
                        title={t("login.title")}
                        subtitle={t("login.subtitle")}
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
                            md:p-10
                            flex
                            flex-col
                            gap-5
                            border
                            theme-border
                        "
                    >
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
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            placeholder={t("email")}
                            className={inputClass}
                            required
                        />
                        <input
                            type={showPassword ? "text" : "password"}
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            placeholder={t("password")}
                            className={inputClass}
                            required
                        />
                        <label
                            className="
                            flex
                            items-center
                            gap-3
                            cursor-pointer
                            text-sm
                            text-[var(--color-muted)]
                        "
                        >
                            <input
                                type="checkbox"
                                checked={showPassword}
                                onChange={() => setShowPassword(!showPassword)}
                            />
                            <span>{t("login.show_password")}</span>
                        </label>
                        <button
                            disabled={loading}
                            className="
                                theme-button
                                rounded-full
                                py-4
                                font-semibold
                                text-lg
                                hover:cursor-pointer
                                hover:scale-105
                                transition
                                disabled:opacity-50
                            "
                        >
                            {loading ? t("loading") : t("login.button")}
                        </button>
                        <div
                            className="
                            flex
                            items-center
                            gap-4
                            my-2
                        "
                        >
                            <div
                                className="
                                flex-1
                                h-px
                                bg-[var(--color-border)]
                            "
                            />
                            <span
                                className="
                                text-sm
                                text-[var(--color-muted)]
                                uppercase
                                tracking-wider
                            "
                            >
                                {t("or")}
                            </span>
                            <div
                                className="
                                flex-1
                                h-px
                                bg-[var(--color-border)]
                            "
                            />
                        </div>
                        <a
                            href={`${import.meta.env.VITE_API_URL.replace("/api", "")}/auth/google`}
                            className="
                                rounded-full
                                py-3
                                flex
                                items-center
                                justify-center
                                gap-3
                                bg-[var(--color-surface)]
                                text-[var(--color-text)]
                                border
                                border-[var(--color-border)]
                                shadow-sm
                                hover:border-[var(--color-secondary)]
                                hover:shadow-md
                                hover:scale-105
                                transition
                            "
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                className="w-5 h-5"
                                aria-hidden="true"
                            >
                                <path
                                    fill="#4285F4"
                                    d="M21.35 12.27c0-.79-.07-1.55-.22-2.27H12v4.3h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.42z"
                                />
                                <path
                                    fill="#34A853"
                                    d="M12 21.75c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.53A9.75 9.75 0 0 0 12 21.75z"
                                />
                                <path
                                    fill="#FBBC05"
                                    d="M6.54 13.83A5.86 5.86 0 0 1 6.23 12c0-.64.11-1.26.31-1.83V7.64H3.3A9.75 9.75 0 0 0 2.25 12c0 1.57.38 3.05 1.05 4.36l3.24-2.53z"
                                />
                                <path
                                    fill="#EA4335"
                                    d="M12 6.14c1.43 0 2.71.49 3.72 1.46l2.79-2.79C16.83 3.25 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.39l3.24 2.53C7.31 7.86 9.46 6.14 12 6.14z"
                                />
                            </svg>

                            <span className="font-medium">
                                {t("login.continue_with_google")}
                            </span>
                        </a>
                        <div
                            className="
                            text-center
                            theme-muted
                        "
                        >
                            <Link
                                to="/forgot-password"
                                className="
                                    hover:text-[var(--color-secondary)]
                                "
                            >
                                {t("forgot_password_link")}
                            </Link>
                            <br />
                            <span>{t("no_account")}</span>{" "}
                            <Link
                                to="/register"
                                className="
                                    hover:text-[var(--color-secondary)]
                                "
                            >
                                {t("register_here")}
                            </Link>
                        </div>
                    </motion.form>
                </div>
            </main>
        </div>
    );
}
