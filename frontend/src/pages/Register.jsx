import { useState } from "react";
import { motion } from "framer-motion";
import { Link, useNavigate } from "react-router-dom";
import { useGoogleReCaptcha } from "react-google-recaptcha-v3";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { register } from "../api/auth";

export default function Register(){
    const { t, language } = useLanguage();
    const { executeRecaptcha } = useGoogleReCaptcha();
    const navigate = useNavigate();
    const [formData,setFormData] = useState({
        first_name:"",
        last_name:"",
        email:"",
        password:"",
        password_confirmation:""
    });
    const [error,setError] = useState("");
    const [loading,setLoading] = useState(false);
    const [passwordValid,setPasswordValid] = useState(false);
    const [showPassword,setShowPassword] = useState(false);
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
    function checkPassword(password){
        return (
            password.length >= 8 &&
            /[A-Z]/.test(password) &&
            /[a-z]/.test(password) &&
            /\d/.test(password) &&
            /[\W_]/.test(password)
        );
    }
    function handleChange(e){
        const {name,value}=e.target;
        setFormData({
            ...formData,
            [name]:value
        });
        if(name==="password"){
            setPasswordValid(
                checkPassword(value)
            );
        }
    }
    async function handleSubmit(e){
        e.preventDefault();
        setError("");
        setLoading(true);
        try{
            if(!executeRecaptcha){
                setError(
                    "Captcha is not ready"
                );
                return;
            }
            const token =
                await executeRecaptcha(
                    "register"
                );
            await register({
                ...formData,
                language,
                recaptcha_token: token
            });
            navigate(
                `/check-email?email=${encodeURIComponent(
                    formData.email
                )}`
            );
        }
        catch(error){
            console.error(error);
            if(error.response?.data?.message){
                setError(
                    error.response.data.message
                );
            }
            else if(error.response?.data?.errors){
                setError(
                    Object.values(
                        error.response.data.errors
                    )
                    .flat()
                    .join(" ")
                );
            }
            else{
                setError(
                    t("registration_failed")
                );
            }
        }
        finally{
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
                        title={t("register.title")}
                        subtitle={t("register.subtitle")}
                    />
                    <motion.form
                        onSubmit={handleSubmit}
                        initial={{
                            opacity:0,
                            y:30
                        }}
                        animate={{
                            opacity:1,
                            y:0
                        }}
                        transition={{
                            duration:.6
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
                            name="first_name"
                            value={formData.first_name}
                            onChange={handleChange}
                            placeholder={t("first_name")}
                            className={inputClass}
                            required
                        />
                        <input
                            name="last_name"
                            value={formData.last_name}
                            onChange={handleChange}
                            placeholder={t("last_name")}
                            className={inputClass}
                            required
                        />
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
                            type={
                                showPassword
                                ? "text"
                                : "password"
                            }
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            placeholder={t("password")}
                            className={inputClass}
                            required
                        />
                        <input
                            type={
                                showPassword
                                ? "text"
                                : "password"
                            }
                            name="password_confirmation"
                            value={formData.password_confirmation}
                            onChange={handleChange}
                            placeholder={t("confirm_password")}
                            className={inputClass}
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
                                checked={showPassword}
                                onChange={() =>
                                    setShowPassword(
                                        !showPassword
                                    )
                                }
                            />
                            <span>
                                {t("login.show_password")}
                            </span>
                        </label>
                        <div className="
                            text-sm
                            theme-muted
                        ">
                            <p>
                                {t("password_requirements")}
                            </p>
                        </div>
                        <button
                            disabled={
                                loading ||
                                !passwordValid
                            }
                            className="
                                theme-button
                                rounded-full
                                py-4
                                font-semibold
                                text-lg
                                transition
                                hover:enabled:cursor-pointer
                                hover:enabled:scale-105
                                disabled:opacity-50
                                disabled:cursor-not-allowed
                            "
                        >
                            {
                                loading
                                ?
                                t("loading")
                                :
                                t("register.button")
                            }
                        </button>
                        <div className="
                            text-center
                            theme-muted
                        ">
                            {t("already_account")}
                            {" "}
                            <Link
                                to="/login"
                                className="
                                    hover:text-[var(--color-secondary)]
                                "
                            >
                                {t("login_here")}
                            </Link>
                        </div>
                    </motion.form>
                </div>
            </main>
        </div>
    );
}