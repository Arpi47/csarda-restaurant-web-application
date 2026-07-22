import { useEffect } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import client from "../api/client";
import { useLanguage } from "../contexts/LanguageContext";

export default function OAuthCallback() {
    const { t } = useLanguage();
    const [params] = useSearchParams();
    const navigate = useNavigate();
    const { setUser } = useAuth();
    useEffect(() => {
        async function login() {
            const token =
                params.get("token");
            if (!token) {
                navigate(
                    "/login?error=oauth_failed",
                    { replace: true }
                );
                return;
            }
            try {
                localStorage.setItem(
                    "token",
                    token
                );
                const response =
                    await client.get(
                        "/user"
                    );
                setUser(
                    response.data
                );
                navigate(
                    "/",
                    { replace: true }
                );
            }
            catch(error) {
                console.error(
                    "OAuth callback failed:",
                    error
                );
                localStorage.removeItem(
                    "token"
                );
                navigate(
                    "/login?error=oauth_failed",
                    { replace: true }
                );
            }
        }
        login();
    }, [
        params,
        navigate,
        setUser
    ]);
    return (
        <div className="
            page-container
            flex
            items-center
            justify-center
            py-20
        ">
            {t("loading")}
        </div>
    );
}