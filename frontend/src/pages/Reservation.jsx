import { useEffect, useState } from "react";
import ReservationForm from "../components/reservation/ReservationForm";
import { useAuth } from "../contexts/AuthContext";
import { useNavigate } from "react-router-dom";
import { useLanguage } from "../contexts/LanguageContext";
import AppDownload from "../components/common/AppDownload";

export default function Reservation(){
    const { user, loading } = useAuth();
    const { t } = useLanguage();
    const navigate = useNavigate();
    const [mobile, setMobile] = useState(null);
    useEffect(() => {
        function checkDevice() {
            setMobile(window.matchMedia("(max-width: 1150px)").matches);
        }
        checkDevice();
        window.addEventListener("resize", checkDevice);
        return () => {
            window.removeEventListener("resize", checkDevice);
        };
    }, []);
    if (mobile === null) {
        return null;
    }
    if(mobile){
        return <AppDownload />;
    }
    if (!loading && !user) {
        navigate("/login");
        return null;
    }
    return (
        <div className="
            page-container
            min-h-screen
            flex
            items-center
            justify-center
            py-12
            px-6
        ">
            <div className="
                w-full
                max-w-3xl
            ">
                <ReservationForm />
            </div>
        </div>
    );
}
