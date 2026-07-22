import { Link, useSearchParams } from "react-router-dom";
import { motion } from "framer-motion";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";

export default function CheckEmail(){
    const { t } = useLanguage();
    const [searchParams] = useSearchParams();
    const email = searchParams.get("email");
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
                        title={t("check_email.title")}
                        subtitle={t("check_email.subtitle")}
                    />
                    <motion.div
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
                            text-center
                        "
                    >
                        <div className="
                            text-5xl
                            mb-6
                        ">
                            ✉️
                        </div>
                        <p className="
                            mb-6
                            theme-muted
                        ">
                            {t("check_email.message")}
                        </p>
                        {
                            email &&
                            <p className="
                                font-semibold
                                mb-8
                            ">
                                {email}
                            </p>
                        }
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
                    </motion.div>
                </div>
            </main>
        </div>
    );
}