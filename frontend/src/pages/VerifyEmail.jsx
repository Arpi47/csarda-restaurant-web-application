import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { useParams, Link } from "react-router-dom";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { verifyEmail } from "../api/auth";

export default function VerifyEmail() {
    const { t } = useLanguage();
    const { token } = useParams();
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        verify();
    }, []);
    async function verify() {
        try {
            const response = await verifyEmail(token);
            setMessage(response.message);
        } catch (error) {
            console.error(error);
            if (error.response?.data?.message) {
                setError(error.response.data.message);
            } else {
                setError("Verification failed");
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
                        title={t("verify_email.title")}
                        subtitle={t("verify_email.subtitle")}
                    />
                    <motion.div
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
                            text-center
                        "
                    >
                        {loading && <p>{t("loading")}</p>}
                        {message && (
                            <>
                                <div
                                    className="
                                        text-5xl
                                        mb-6
                                    "
                                >
                                    ✅
                                </div>
                                <p
                                    className="
                                        mb-8
                                        text-green-600
                                    "
                                >
                                    {message}
                                </p>
                                <Link
                                    to="/login"
                                    className="
                                            theme-button
                                            rounded-full
                                            py-3
                                            px-8
                                            inline-block
                                            transition
                                            hover:scale-105
                                        "
                                >
                                    {t("login.button")}
                                </Link>
                            </>
                        )}
                        {error && (
                            <>
                                <div
                                    className="
                                        text-5xl
                                        mb-6
                                    "
                                >
                                    ❌
                                </div>
                                <p
                                    className="
                                        text-red-500
                                        mb-8
                                    "
                                >
                                    {error}
                                </p>
                                <Link
                                    to="/register"
                                    className="
                                            theme-button
                                            rounded-full
                                            py-3
                                            px-8
                                            inline-block
                                        "
                                >
                                    {t("register.button")}
                                </Link>
                            </>
                        )}
                    </motion.div>
                </div>
            </main>
        </div>
    );
}
