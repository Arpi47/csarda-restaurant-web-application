import { motion } from "framer-motion";

export default function PageHeader({
    title,
    subtitle
}) {
    return (
        <section
            className="
                text-center
                mb-12
                px-6
            "
        >
            <motion.h1
                initial={{
                    opacity:0,
                    y:40
                }}
                animate={{
                    opacity:1,
                    y:0
                }}
                transition={{
                    duration:0.8
                }}
                className="
                    text-5xl
                    md:text-6xl
                    font-bold
                    mb-5
                "
            >
                {title}
            </motion.h1>
            {
                subtitle && (
                    <motion.p
                        initial={{
                            opacity:0
                        }}
                        animate={{
                            opacity:1
                        }}
                        transition={{
                            delay:0.3
                        }}
                        className="
                            text-[var(--color-muted)]
                            text-lg
                            max-w-2xl
                            mx-auto
                        "
                    >
                        {subtitle}
                    </motion.p>
                )
            }
        </section>
    );
}