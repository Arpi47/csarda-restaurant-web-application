import { useRef, useState } from "react";
import { useAuth } from "../../contexts/AuthContext";
import { logout } from "../../api/auth";
import { useLanguage } from "../../contexts/LanguageContext";
import useDropdown from "../../hooks/useDropdown";
import { Link, useNavigate } from "react-router-dom";

export default function UserMenu() {
    const navigate = useNavigate();
    const [open, setOpen] = useState(false);
    const { user, setUser } = useAuth();
    const { t } = useLanguage();
    const ref = useRef(null);
    useDropdown(ref, () => setOpen(false));
    async function handleLogout() {
        try {
            await logout();
            setUser(null);
            navigate("/login");
        } catch (error) {
            console.log(error);
        }
    }
    return (
        <div
            ref={ref}
            className="
                relative
                w-auto
            "
        >
            <button
                onClick={() => setOpen(!open)}
                className="
                    flex
                    items-center
                    justify-between
                    gap-2
                    px-3
                    py-2
                    rounded-lg
                    theme-hover-bg
                    cursor-pointer
                    min-w-[120px]
                    transition
                "
            >
                👤
                <span className="ml-2">
                    {user ? user.first_name : t("nav.account")}
                </span>
                ▾
            </button>
            {open && (
                <div
                    className="
                            absolute
                            right-0
                            mt-2
                            bg-[var(--color-surface)]
                            shadow-lg
                            rounded-lg
                            w-44
                            z-50
                            overflow-hidden
                        "
                >
                    {user ? (
                        <>
                            <Link
                                to="/profile"
                                className="
                                    block
                                    px-4
                                    py-2
                                    theme-hover-bg
                                    cursor-pointer
                                    transition
                                "
                            >
                                {t("nav.profile")}
                            </Link>
                            <Link
                                to="/reservations"
                                className="
                                    block
                                    px-4
                                    py-2
                                    theme-hover-bg
                                    cursor-pointer
                                    transition
                                "
                            >
                                {t("nav.reservations")}
                            </Link>
                            <button
                                onClick={handleLogout}
                                className="
                                    block
                                    w-full
                                    text-left
                                    px-4
                                    py-2
                                    theme-hover-bg
                                    cursor-pointer
                                    transition
                                "
                            >
                                {t("nav.logout")}
                            </button>
                        </>
                    ) : (
                        <>
                            <a
                                href="/login"
                                className="
                                    block
                                    px-4
                                    py-2
                                    theme-hover-bg
                                    cursor-pointer
                                    transition
                                "
                            >
                                {t("nav.login")}
                            </a>
                            <a
                                href="/register"
                                className="
                                    block
                                    px-4
                                    py-2
                                    theme-hover-bg
                                    cursor-pointer
                                    transition
                                "
                            >
                                {t("nav.register")}
                            </a>
                        </>
                    )}
                </div>
            )}
        </div>
    );
}
