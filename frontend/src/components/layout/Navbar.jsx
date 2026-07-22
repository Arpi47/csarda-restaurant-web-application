import { useState, useRef, useEffect } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { Link, NavLink } from "react-router-dom";
import LanguageSwitcher from "../common/LanguageSwitcher";
import UserMenu from "../common/UserMenu";
import { useLanguage } from "../../contexts/LanguageContext";
import ThemeSwitcher from "../common/ThemeSwitcher";

export default function Navbar() {
    const [menuOpen, setMenuOpen] = useState(false);
    const menuRef = useRef(null);
    const { t } = useLanguage();
    useEffect(() => {
        function handleKey(e) {
            if (e.key === "Escape") {
                setMenuOpen(false);
            }
        }
        function handleClick(e) {
            if (
                menuRef.current &&
                !menuRef.current.contains(e.target)
            ) {

                setMenuOpen(false);
            }
        }
        document.addEventListener(
            "keydown",
            handleKey
        );
        document.addEventListener(
            "mousedown",
            handleClick
        );
        return () => {
            document.removeEventListener(
                "keydown",
                handleKey
            );
            document.removeEventListener(
                "mousedown",
                handleClick
            );
        }
    }, []);
    const links = [
        {
            name: "nav.home",
            path: "/"
        },
        {
            name: "nav.menu",
            path: "/menu"
        },
        {
            name: "nav.gallery",
            path: "/gallery"
        },
        {
            name: "nav.about",
            path: "/about"
        },
        {
            name: "nav.contact",
            path: "/contact"
        },
        {
            name: "nav.reservation",
            path: "/reservation"
        }
    ];
    return (
        <header className="
            relative
            z-50
            bg-[var(--color-surface)]
            shadow-sm
        ">
            <nav className="
                relative
                max-w-7xl
                mx-auto
                px-6
                py-4
            ">
                <div className="
                    relative
                    flex
                    items-center
                    justify-between
                ">
                    {/* Logo */}
                    <Link
                        to="/"
                        className="
                            text-2xl
                            font-bold
                            whitespace-nowrap
                        "
                    >
                        Csárda
                    </Link>
                    {/* Desktop navigation */}
                    <div className="
                        hidden
                        desktop:flex
                        absolute
                        left-1/2
                        -translate-x-1/2
                        items-center
                    ">
                        <div className="
                            flex
                            items-center
                            gap-6
                            whitespace-nowrap
                        ">
                            {
                                links.map(link => (
                                    <NavLink
                                        key={link.path}
                                        to={link.path}
                                        className={({ isActive }) =>
                                            `
                                            transition-colors
                                            theme-hover
                                            whitespace-nowrap

                                            ${
                                                isActive
                                                ?
                                                "font-bold"
                                                :
                                                ""
                                            }
                                            `
                                        }
                                    >
                                        {t(link.name)}
                                    </NavLink>
                                ))
                            }
                        </div>
                    </div>
                    {/* Right side */}
                    <div className="
                        hidden
                        desktop:flex
                        items-center
                        gap-3
                        whitespace-nowrap
                    ">
                        <ThemeSwitcher />
                        <LanguageSwitcher />
                        <UserMenu />
                    </div>
                    {/* Mobile actions */}
                    <div className="
                        desktop:hidden
                        flex
                        items-center
                        gap-2
                    ">
                        <div className="
                            w-10
                            h-10
                            flex
                            items-center
                            justify-center
                            rounded-full
                        ">
                            <ThemeSwitcher />
                        </div>
                        {/* Hamburger */}
                        <button
                            onMouseDown={(e) => {
                                e.stopPropagation();
                            }}
                            onClick={() => setMenuOpen(!menuOpen)}
                            className="
                                w-10
                                h-10
                                rounded-full
                                flex
                                items-center
                                justify-center
                                cursor-pointer
                                text-[var(--color-text)]
                                text-3xl
                                transition
                                hover:bg-[var(--color-hover-bg)]
                            "
                        >
                            <span
                                className={menuOpen ? "translate-y-0" : "-translate-y-[3px]"}
                            >
                                {menuOpen ? "✕" : "☰"}
                            </span>
                        </button>
                    </div>
                </div>
                {/* Mobile menu */}
                <AnimatePresence>
                    {
                        menuOpen && (
                            <>
                                {/* Top border */}
                                <motion.div
                                    initial={{
                                        opacity: 0
                                    }}
                                    animate={{
                                        opacity: 1
                                    }}
                                    exit={{
                                        opacity: 0
                                    }}
                                    transition={{
                                        duration: 0
                                    }}
                                    className="
                                        absolute
                                        left-0
                                        right-0
                                        top-full
                                        z-[60]
                                        desktop:hidden
                                        border-t
                                    "
                                />
                                <motion.div
                                    ref={menuRef}
                                    initial={{
                                        opacity: 0,
                                        y: -40
                                    }}
                                    animate={{
                                        opacity: 1,
                                        y: 0
                                    }}
                                    exit={{
                                        opacity: 0,
                                        y: -40
                                    }}
                                    transition={{
                                        type: "spring",
                                        stiffness: 350,
                                        damping: 30
                                    }}
                                    className="
                                        absolute
                                        left-0
                                        right-0
                                        top-full
                                        z-50
                                        desktop:hidden
                                        bg-[var(--color-surface)]
                                        shadow-lg
                                        px-6
                                        py-5
                                    "
                                >
                                    <div className="
                                        flex
                                        flex-col
                                        gap-3
                                    ">
                                        {
                                            links.map(link => (
                                                <NavLink
                                                    key={link.path}
                                                    to={link.path}
                                                    onClick={() => setMenuOpen(false)}
                                                    className={({ isActive }) =>
                                                        `
                                                        py-2
                                                        theme-hover
                                                        ${
                                                            isActive
                                                            ?
                                                            "font-bold"
                                                            :
                                                            ""
                                                        }
                                                        `
                                                    }
                                                >
                                                    {t(link.name)}
                                                </NavLink>
                                            ))
                                        }
                                        <hr className="
                                            my-3
                                        " />
                                        <div className="
                                            pt-2
                                            flex
                                            justify-center
                                        ">
                                            <LanguageSwitcher mobile />
                                        </div>
                                    </div>
                                </motion.div>
                            </>
                        )
                    }
                </AnimatePresence>
            </nav>
        </header>
    );
}