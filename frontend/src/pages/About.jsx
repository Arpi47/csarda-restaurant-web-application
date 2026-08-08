import PageHeader from "../components/common/PageHeader";
import FeatureCard from "../components/common/FeatureCard";
import { useLanguage } from "../contexts/LanguageContext";
import { motion } from "framer-motion";

export default function About() {
    const { t } = useLanguage();
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;
    return (
        <div className="page-container">
            <main className="py-12">
                <PageHeader
                    title={t("about.title")}
                    subtitle={t("about.subtitle")}
                />
                <section
                    className="
                    px-6
                    pb-20
                "
                >
                    <div
                        className="
                        max-w-7xl
                        mx-auto
                        grid
                        md:grid-cols-2
                        gap-12
                        items-center
                    "
                    >
                        <motion.img
                            initial={{
                                opacity: 0,
                                x: -50,
                            }}
                            whileInView={{
                                opacity: 1,
                                x: 0,
                            }}
                            viewport={{
                                once: true,
                            }}
                            transition={{
                                duration: 0.8,
                            }}
                            src={`${ASSET_URL}/images/about.jpg`}
                            alt="Csárda"
                            className="
                                rounded-2xl
                                shadow-xl
                                w-full
                                h-[400px]
                                object-cover
                            "
                        />
                        <motion.div
                            initial={{
                                opacity: 0,
                                x: 50,
                            }}
                            whileInView={{
                                opacity: 1,
                                x: 0,
                            }}
                            viewport={{
                                once: true,
                            }}
                        >
                            <h2
                                className="
                                theme-text
                                text-4xl
                                font-bold
                                mb-6
                            "
                            >
                                {t("about.storyTitle")}
                            </h2>
                            <p
                                className="
                                theme-muted
                                leading-relaxed
                                mb-4
                            "
                            >
                                {t("about.storyText1")}
                            </p>
                            <p
                                className="
                                theme-muted
                                leading-relaxed
                            "
                            >
                                {t("about.storyText2")}
                            </p>
                        </motion.div>
                    </div>
                </section>
                <section
                    className="
                    px-6
                    py-20
                    bg-[var(--color-overlay)]
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
                            {t("about.valuesTitle")}
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
                                title={t("about.tradition")}
                                text={t("about.traditionText")}
                                icon="🍲"
                            />
                            <FeatureCard
                                title={t("about.ingredients")}
                                text={t("about.ingredientsText")}
                                icon="🥬"
                            />
                            <FeatureCard
                                title={t("about.hospitality")}
                                text={t("about.hospitalityText")}
                                icon="🏡"
                            />
                        </div>
                    </div>
                </section>
            </main>
        </div>
    );
}
