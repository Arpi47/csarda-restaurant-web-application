import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import PageHeader from "../components/common/PageHeader";
import { useLanguage } from "../contexts/LanguageContext";
import { 
    getProfile, 
    updateProfile,
    requestDelete,
    cancelDelete,
    disconnectGoogle
} from "../api/profile";

export default function Profile(){
    const { t } = useLanguage();
    const [user,setUser] = useState(null);
    const token = localStorage.getItem("token");
    const [formData,setFormData] = useState({
        first_name:"",
        last_name:"",
        password:"",
        password_confirmation:""
    });
    const [deleteMessage,setDeleteMessage] = useState("");
    const [deleteRequested,setDeleteRequested] = useState(
        false
    );
    const [loading,setLoading] = useState(true);
    const [message,setMessage] = useState("");
    const [messageType,setMessageType] = useState("success");
    useEffect(()=>{
        loadProfile();
        if(window.grecaptcha){
            window.grecaptcha.ready(()=>{});
        }
    },[]);
    async function loadProfile(){
        try{
            const data = await getProfile();
            setUser(data);
            setDeleteRequested(
                data.deletion_requested ?? false
            );
            setFormData({
                first_name:data.first_name || "",
                last_name:data.last_name || "",
                password:"",
                password_confirmation:""
            });
        }
        catch(error){
            console.error(error);
        }
        finally{
            setLoading(false);
        }
    }
    function handleChange(e){
        const {
            name,
            value
        } = e.target;
        setFormData({
            ...formData,
            [name]:value
        });
    }
    async function handleSubmit(e){
        e.preventDefault();
        setMessage("");
        try{
            if(
                !window.grecaptcha
            ){
                setMessageType("error");
                setMessage(
                    t("something_went_wrong")
                );
                return;
            }
            const token =
                await window.grecaptcha.execute(
                    import.meta.env.VITE_RECAPTCHA_SITE_KEY,
                    {
                        action:"profile_update"
                    }
                );
            const data = new FormData();
            Object.keys(formData).forEach(key=>{
                if(formData[key]){
                    data.append(
                        key,
                        formData[key]
                    );
                }
            });
            data.append(
                "g-recaptcha-response",
                token
            );
            const response =
                await updateProfile(data);
            setUser(
                response.user
            );
            setFormData({
                first_name:response.user.first_name,
                last_name:response.user.last_name,
                password:"",
                password_confirmation:""
            });
            setMessageType("success");
            setMessage(
                response.message
            );
        }
        catch(error){
        console.error(error);
        setMessageType("error");
        setMessage(
            error.response?.data?.message ??
            t("something_went_wrong")
        );
        setFormData(prev => ({
            ...prev,
            password:"",
            password_confirmation:""
        }));
    }
    }
    async function handleDeleteRequest(){
        if(!confirm(
            t("profile.delete_confirm")
        )){
            return;
        }
        try{
            const response =
                await requestDelete();
            setDeleteMessage(
                response.message
            );
            setDeleteRequested(true);
        }
        catch(error){
            console.error(error);
        }
    }
    async function handleCancelDelete(){
        try{
            const response = await cancelDelete();
            setDeleteMessage(response.message);
            setDeleteRequested(false);
        }
        catch(error){
            console.error(error);
        }
    }
    async function handleDisconnectGoogle(){
        if(!confirm(
            t("profile.google_disconnect_confirm")
        )){
            return;
        }
        try{
            const response = await disconnectGoogle();
            setUser(prev => ({
                ...prev,
                google_connected: false
            }));
            setMessageType("success");
            setMessage(response.message);
        }
        catch(error){
            console.error(error);
            setMessageType("error");
            setMessage(
                error.response?.data?.message ??
                t("something_went_wrong")
            );
        }
    }
    if(loading){
        return (
            <div className="
                page-container
                flex
                justify-center
                py-20
            ">
                {t("loading")}
            </div>
        );
    }
    return (
        <div className="page-container">
            <main className="
                py-12
                px-6
            ">
                <div className="
                    max-w-3xl
                    mx-auto
                ">
                    <PageHeader
                        title={t("profile.title")}
                        subtitle={t("profile.subtitle")}
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
                            message &&
                            <div
                                className={`
                                    rounded-xl
                                    p-4
                                    text-center
                                    ${
                                        messageType === "success"
                                        ?
                                        "bg-green-100 text-green-700"
                                        :
                                        "bg-red-100 text-red-700"
                                    }
                                `}
                            >
                                {message}
                            </div>
                        }
                        <div className="
                            grid
                            grid-cols-1
                            md:grid-cols-2
                            gap-5
                        ">
                            <input
                                name="first_name"
                                value={formData.first_name}
                                onChange={handleChange}
                                placeholder={t("first_name")}
                                className="
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
                                "
                            />
                            <input
                                name="last_name"
                                value={formData.last_name}
                                onChange={handleChange}
                                placeholder={t("last_name")}
                                className="
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
                                "
                            />
                        </div>
                        {
                            <>
                                <input
                                    type="password"
                                    name="password"
                                    value={formData.password}
                                    onChange={handleChange}
                                    placeholder={
                                        user.can_change_password
                                            ? t("new_password")
                                            : t("profile.set_password")
                                    }
                                    className="
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
                                    "
                                />
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    value={formData.password_confirmation}
                                    onChange={handleChange}
                                    placeholder={t("confirm_password")}
                                    className="
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
                                    "
                                />
                            </>
                        }
                        <p className="
                            text-sm
                            text-[var(--color-muted)]
                            text-center
                            px-2
                            mt-2
                        ">
                            {t("profile.update_info")}
                        </p>
                        <button type="submit"
                            className="
                                theme-button
                                rounded-full
                                py-4
                                mt-4
                                font-semibold
                                text-lg
                                hover:cursor-pointer
                                hover:scale-105
                                transition
                            "
                        >
                            {t("save")}
                        </button>
                        <div className="
                            mt-8
                            pt-8
                            border-t
                            theme-border
                        ">
                            <h2 className="
                                text-3xl
                                font-bold
                                mb-3
                                text-center
                            ">
                                {t("profile.google_account")}
                            </h2>
                            <p className="
                                text-sm
                                text-[var(--color-muted)]
                                text-center
                                mb-6
                            ">
                                {user.google_connected
                                    ? t("profile.google_connected_description")
                                    : t("profile.google_not_connected_description")
                                }
                            </p>
                            {
                                user.google_connected
                                ?
                                <div className="
                                    flex
                                    flex-col
                                    gap-4
                                ">
                                    <div className="
                                        rounded-xl
                                        p-4
                                        text-center
                                        bg-green-100
                                        text-green-700
                                    ">
                                        {t("profile.google_connected")}
                                    </div>
                                    {
                                        user.can_change_password &&
                                        <button
                                            type="button"
                                            onClick={handleDisconnectGoogle}
                                            className="
                                                w-full
                                                rounded-full
                                                py-4
                                                bg-red-600
                                                text-white
                                                font-semibold
                                                text-lg
                                                hover:cursor-pointer
                                                hover:scale-105
                                                transition
                                            "
                                        >
                                            {t("profile.disconnect_google")}
                                        </button>
                                    }
                                </div>
                                :
                                <button
                                    type="button"
                                    onClick={() => {
                                        window.location.href =
                                            `${import.meta.env.VITE_BACKEND_URL}/auth/google/link?token=${encodeURIComponent(token)}`;
                                    }}
                                    className="
                                        w-full
                                        rounded-full
                                        py-4
                                        theme-button
                                        font-semibold
                                        text-lg
                                        hover:cursor-pointer
                                        hover:scale-105
                                        transition
                                    "
                                >
                                    {t("profile.connect_google")}
                                </button>
                            }
                        </div>
                        <div className="
                            mt-8
                            pt-8
                            border-t
                            theme-border
                        ">
                            <h2 className="
                                text-3xl
                                font-bold
                                mb-5
                                text-center
                            ">
                                {t("profile.delete_account")}
                            </h2>
                            {
                                deleteMessage &&
                                <div className="
                                    rounded-xl
                                    bg-green-100
                                    text-green-700
                                    p-4
                                    mb-5
                                    text-center
                                ">
                                    {deleteMessage}
                                </div>
                            }
                            {
                                deleteRequested
                                ?
                                <button
                                    type="button"
                                    onClick={handleCancelDelete}
                                    className="
                                        theme-button
                                        rounded-full
                                        py-4
                                        w-full
                                        font-semibold
                                        text-lg
                                        hover:cursor-pointer
                                        hover:scale-105
                                        transition
                                    "
                                >
                                    {t("profile.cancel_delete")}
                                </button>
                                :
                                <button
                                    type="button"
                                    onClick={handleDeleteRequest}
                                    className="
                                        w-full
                                        rounded-full
                                        py-4
                                        bg-red-600
                                        text-white
                                        font-semibold
                                        text-lg
                                        hover:cursor-pointer
                                        hover:scale-105
                                        transition
                                    "
                                >
                                    {t("profile.request_delete")}
                                </button>
                            }
                        </div>
                    </motion.form>
                </div>
            </main>
        </div>
    );
}