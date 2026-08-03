import { useEffect, useState } from "react";
import { motion, useScroll, useTransform } from "framer-motion";
import { Link } from "react-router-dom";
import { useLanguage } from "../contexts/LanguageContext";
import { getMenu } from "../api/menu";
import MenuCard from "../components/menu/MenuCard";
import FeatureCard from "../components/common/FeatureCard";
import AppDownload from "../components/common/AppDownload";

export default function Home() {
    const { t } = useLanguage();
    const { scrollY } = useScroll();
    const [featuredItems, setFeaturedItems] = useState([]);
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;
    const backgroundY = useTransform(scrollY, [0, 500], [0, 150]);
    useEffect(() => {
        getMenu()
            .then((data) => {
                setFeaturedItems(data.slice(0, 3));
            })
            .catch((error) => {
                console.error(error);
            });
    }, []);
    return (
        <main className="home-page">
            <section
                className="
                    relative
                    min-h-[100dvh]
                    ios-hero-height
                    -mt-[70px]
                    pt-[70px]
                    overflow-hidden
                    flex
                    items-center
                    justify-center
                    z-0
                    landscape-mobile-hero
                "
            >
                <motion.img
                    src={`${ASSET_URL}/images/hero.jpg`}
                    style={{
                        y: backgroundY,
                    }}
                    className="
                        absolute
                        inset-0
                        z-0
                        w-full
                        h-full
                        object-cover
                        scale-110
                        origin-center
                        pointer-events-none
                    "
                    alt="Csarda"
                />
                <div
                    className="
                        absolute
                        inset-0
                        z-10
                        bg-black/50
                    "
                />
                <motion.div
                    initial={{
                        opacity: 0,
                        y: 40,
                    }}
                    animate={{
                        opacity: 1,
                        y: 0,
                    }}
                    transition={{
                        duration: 1,
                    }}
                    className="
                        relative
                        z-20
                        text-center
                        text-white
                        px-6
                        py-10
                        landscape-mobile-hero-content
                    "
                >
                    <h1
                        className="
                            text-5xl
                            md:text-7xl
                            font-bold
                            mb-6
                            landscape-mobile-hero-title
                        "
                    >
                        {t("home.title")}
                    </h1>
                    <p
                        className="
                            text-xl
                            md:text-2xl
                            mb-10
                            max-w-2xl
                            mx-auto
                            landscape-mobile-hero-subtitle
                        "
                    >
                        {t("home.subtitle")}
                    </p>
                    <div
                        className="
                            flex
                            flex-col
                            sm:flex-row
                            items-center
                            justify-center
                            gap-4
                            w-full
                        "
                    >
                        <Link
                            to="/menu"
                            className="
                                w-full
                                sm:w-40
                                max-w-xs
                                px-8
                                py-4
                                rounded-full
                                bg-[var(--hero-button-bg)]
                                text-[var(--hero-button-text)]
                                font-semibold
                                text-center
                                hover:scale-105
                                transition
                            "
                        >
                            {t("home.menuButton")}
                        </Link>

                        <Link
                            to="/reservation"
                            className="
                                w-full
                                sm:w-40
                                max-w-xs
                                px-8
                                py-4
                                rounded-full
                                bg-[var(--hero-button-bg)]
                                text-[var(--hero-button-text)]
                                font-semibold
                                text-center
                                hover:scale-105
                                transition
                            "
                        >
                            {t("home.reservationButton")}
                        </Link>
                    </div>
                </motion.div>
            </section>
            <section
                className="
                    py-20
                    px-6
                    text-center
                "
            >
                <motion.h2
                    initial={{
                        opacity: 0,
                        y: 30,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    transition={{
                        duration: 0.8,
                    }}
                    viewport={{
                        once: true,
                    }}
                    className="
                        text-4xl
                        font-bold
                        mb-6
                    "
                >
                    {t("home.welcome")}
                </motion.h2>
                <p
                    className="
                    max-w-3xl
                    mx-auto
                    text-muted
                    text-lg
                "
                >
                    {t("home.description")}
                </p>
            </section>
            <section
                className="
                    py-20
                    px-6
                    bg-[var(--color-overlay)]
                "
            >
                <motion.h2
                    initial={{
                        opacity: 0,
                        y: 30,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    viewport={{
                        once: true,
                    }}
                    className="
                        text-4xl
                        font-bold
                        text-center
                        mb-12
                    "
                >
                    {t("home.featured")}
                </motion.h2>
                <div
                    className="
                        max-w-7xl
                        mx-auto
                        grid
                        grid-cols-1
                        md:grid-cols-3
                        gap-10
                    "
                >
                    {featuredItems.map((item) => (
                        <MenuCard key={item.id} item={item} />
                    ))}
                </div>
            </section>
            <section
                className="
                    py-20
                    px-6
                "
            >
                <div
                    className="
                        max-w-7xl
                        mx-auto
                    "
                >
                    <motion.h2
                        initial={{
                            opacity: 0,
                            y: 30,
                        }}
                        whileInView={{
                            opacity: 1,
                            y: 0,
                        }}
                        viewport={{
                            once: true,
                        }}
                        className="
                            text-4xl
                            font-bold
                            text-center
                            mb-12
                        "
                    >
                        {t("home.whyTitle")}
                    </motion.h2>
                    <div
                        className="
                            grid
                            grid-cols-1
                            md:grid-cols-3
                            gap-8
                        "
                    >
                        <FeatureCard
                            title={t("home.whyFresh")}
                            text={t("home.whyFreshText")}
                            icon="🥬"
                        />
                        <FeatureCard
                            title={t("home.whyTradition")}
                            text={t("home.whyTraditionText")}
                            icon="🍲"
                        />
                        <FeatureCard
                            title={t("home.whyAtmosphere")}
                            text={t("home.whyAtmosphereText")}
                            icon="🏡"
                        />
                    </div>
                </div>
            </section>
            <section
                className="
                    py-20
                    px-6
                    bg-[var(--color-text)]
                    text-[var(--color-background)]
                    text-center
                "
            >
                <motion.div
                    initial={{
                        opacity: 0,
                        y: 40,
                    }}
                    whileInView={{
                        opacity: 1,
                        y: 0,
                    }}
                    viewport={{
                        once: true,
                    }}
                    transition={{
                        duration: 0.8,
                    }}
                    className="
                        max-w-4xl
                        mx-auto
                    "
                >
                    <h2
                        className="
                            text-4xl
                            md:text-5xl
                            font-bold
                            mb-6
                        "
                    >
                        {t("home.reservationTitle")}
                    </h2>
                    <p
                        className="
                            text-lg
                            md:text-xl
                            text-[var(--color-muted)]
                            mb-10
                        "
                    >
                        {t("home.reservationText")}
                    </p>
                    <Link
                        to="/reservation"
                        className="
                            inline-block
                            px-10
                            py-4
                            rounded-full
                            bg-[var(--color-surface)]
                            text-[var(--color-text)]
                            font-semibold
                            hover:scale-105
                            transition
                        "
                    >
                        {t("home.reservationButton")}
                    </Link>
                </motion.div>
            </section>
            <AppDownload />
        </main>
    );
}
