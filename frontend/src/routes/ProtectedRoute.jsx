import { Navigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import { useLanguage } from "../contexts/LanguageContext";

export default function ProtectedRoute({ children }) {
    const { t } = useLanguage();
    const { user, loading } = useAuth();
    if (loading) {
        return (
            <div>
                {t("loading")}
            </div>
        );
    }
    if (!user) {
        return (
            <Navigate 
                to="/login"
                replace
            />
        );
    }
    return children;
}