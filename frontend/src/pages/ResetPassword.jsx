import { useState } from "react";
import { motion } from "framer-motion";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { resetPassword } from "../api/auth";
import {
    useParams,
    useNavigate,
    useSearchParams,
    Link
} from "react-router-dom";

export default function ResetPassword(){
    const { t } = useLanguage();
    const { token } = useParams();
    const [searchParams] = useSearchParams();
    const email = searchParams.get("email");
    const navigate = useNavigate();
    const [formData,setFormData] = useState({
        password:"",
        password_confirmation:""
    });
    const [message,setMessage] = useState("");
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
        const updatedForm = {
            ...formData,
            [name]:value
        };
        setFormData(updatedForm);
        if(name === "password"){
            setPasswordValid(
                checkPassword(value)
            );
        }
    }
    const passwordsMatch =
        formData.password === formData.password_confirmation;
    async function handleSubmit(e){
        e.preventDefault();
        setMessage("");
        setError("");
        setLoading(true);
        try{
            const response =
                await resetPassword({
                    token,
                    email,
                    ...formData
                });
            setMessage(
                response.message
            );
            setTimeout(()=>{
                navigate("/login");
            },2000);
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
                    t("password_update_failed")
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
                        title={t("reset_password.title")}
                        subtitle={t("reset_password.subtitle")}
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
                            border
                            theme-border
                            flex
                            flex-col
                            gap-5
                        "
                    >
                        {
                            message &&
                            <div className="
                                text-green-600
                                text-center
                            ">
                                {message}
                            </div>
                        }
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
                            type={
                                showPassword
                                ? "text"
                                : "password"
                            }
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            placeholder={t("new_password")}
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
                        <div className="
                            text-sm
                            theme-muted
                        ">
                            <p>
                                {t("password_requirements")}
                            </p>
                            {
                                formData.password_confirmation &&
                                !passwordsMatch &&
                                <p className="
                                    text-red-500
                                    mt-2
                                ">
                                    {t("passwords_not_match")}
                                </p>
                            }
                        </div>
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
                        <button
                            disabled={
                                loading ||
                                !passwordValid ||
                                !passwordsMatch
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
                                t("reset_password.button")
                            }
                        </button>
                        <div className="
                            text-center
                            theme-muted
                        ">
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