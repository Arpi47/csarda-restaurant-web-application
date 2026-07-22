import { Link } from "react-router-dom";
import { useLanguage } from "../contexts/LanguageContext";

export default function VerificationSuccess(){
    const { t } = useLanguage();
    return (
        <div className="page-container">
            <main className="
                min-h-screen
                flex
                items-center
                justify-center
                py-12
                px-6
            ">
                <div className="
                    theme-card
                    rounded-3xl
                    shadow-xl
                    p-10
                    text-center
                    max-w-md
                ">
                    <div className="
                        text-green-500
                        text-5xl
                        mb-5
                    ">
                        ✓
                    </div>
                    <h1 className="
                        text-2xl
                        font-bold
                        mb-4
                    ">
                        {t("verification_success")}
                    </h1>
                    <Link
                        to="/login"
                        className="
                            theme-button
                            inline-block
                            rounded-full
                            px-8
                            py-3
                            mt-5
                        "
                    >
                        {t("login.button")}
                    </Link>
                </div>
            </main>
        </div>
    );
}