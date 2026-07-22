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
                replace: true
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
        const oauthError =
            searchParams.get("error");
        if (!oauthError) {
            return;
        }
        const translatedError =
            t(
                `oauth_errors.${oauthError}`
            );
        if (
            translatedError &&
            translatedError !==
            `oauth_errors.${oauthError}`
        ) {
            setError(
                translatedError
            );
        }
        else {
            setError(
                t("something_went_wrong")
            );
        }
    }, [
        searchParams,
        t
    ]);
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

        const {
            name,
            value
        } = e.target;
        setFormData({
            ...formData,
            [name]: value
        });
    }
    async function handleSubmit(e) {
        e.preventDefault();
        setError("");
        setLoading(true);
        try {
            const response =
                await login(
                    formData
                );
            localStorage.setItem(
                "token",
                response.token
            );
            setUser(
                response.user
            );
            navigate("/");
        }
        catch(error) {
            console.log(error);
            if(
                error.response?.data?.message
            ) {
                setError(
                    error.response.data.message
                );
            }
            else {
                setError(
                    t("something_went_wrong")
                );
            }
        }
        finally {
            setLoading(false);
        }
    }
    return (
        <div className="page-container">
            <main className="
                py-12
                px-6
            ">
                <div className="
                    max-w-md
                    mx-auto
                ">
                    <PageHeader
                        title={t("login.title")}
                        subtitle={t("login.subtitle")}
                    />
                    <motion.form
                        onSubmit={handleSubmit}
                        initial={{
                            opacity: 0,
                            y: 30
                        }}
                        animate={{
                            opacity: 1,
                            y: 0
                        }}
                        transition={{
                            duration: .6
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
                        {
                            error &&
                            <div className="
                                text-red-500
                                text-center
                            ">
                                {error}
                            </div>
                        }
                        <input
                            type="email"
                            name="email"
                            value={
                                formData.email
                            }
                            onChange={
                                handleChange
                            }
                            placeholder={
                                t("email")
                            }
                            className={
                                inputClass
                            }
                            required
                        />
                        <input
                            type={
                                showPassword
                                ? "text"
                                : "password"
                            }
                            name="password"
                            value={
                                formData.password
                            }
                            onChange={
                                handleChange
                            }
                            placeholder={
                                t("password")
                            }
                            className={
                                inputClass
                            }
                            required
                        />
                        <label className="
                            flex
                            items-center
                            gap-3
                            cursor-pointer
                            text-sm
                            text-[var(--color-muted)]
                        ">
                            <input
                                type="checkbox"
                                checked={
                                    showPassword
                                }
                                onChange={() =>
                                    setShowPassword(
                                        !showPassword
                                    )
                                }
                            />
                            <span>
                                {
                                    t(
                                        "login.show_password"
                                    )
                                }
                            </span>
                        </label>
                        <button
                            disabled={
                                loading
                            }
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
                            {
                                loading
                                ? t("loading")
                                : t("login.button")
                            }
                        </button>
                        <div className="
                            flex
                            items-center
                            gap-4
                            my-2
                        ">
                            <div className="
                                flex-1
                                h-px
                                bg-[var(--color-border)]
                            " />
                            <span className="
                                text-sm
                                text-[var(--color-muted)]
                                uppercase
                                tracking-wider
                            ">
                                {
                                    t("or")
                                }
                            </span>
                            <div className="
                                flex-1
                                h-px
                                bg-[var(--color-border)]
                            " />
                        </div>
                        <a
                            href={`${import.meta.env.VITE_API_URL.replace('/api','')}/auth/google`}
                            className="
                                theme-button
                                rounded-full
                                py-3
                                text-center
                            "
                        >
                            Continue with Google
                        </a>
                        <div className="
                            text-center
                            theme-muted
                        ">
                            <Link
                                to="/forgot-password"
                                className="
                                    hover:text-[var(--color-secondary)]
                                "
                            >
                                {t("forgot_password_link")}
                            </Link>
                            <br />
                            <span>
                                {t("no_account")}
                            </span>
                            {" "}
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