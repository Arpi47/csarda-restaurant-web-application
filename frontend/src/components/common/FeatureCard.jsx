import { motion } from "framer-motion";

export default function FeatureCard({
    title,
    text,
    icon
}) {
    return (
        <motion.div
            initial={{
                opacity:0,
                y:30
            }}
            whileInView={{
                opacity:1,
                y:0
            }}
            viewport={{
                once:true
            }}
            whileHover={{
                y:-8
            }}
            className="
                text-center
                p-8
                rounded-2xl
                shadow-md
                bg-[var(--color-surface)]
            "
        >
            <div className="text-5xl mb-5">
                {icon}
            </div>
            <h3 className="
                text-2xl
                font-bold
                mb-3
            ">
                {title}
            </h3>
            <p className="
                text-[var(--color-muted)]
                leading-relaxed
            ">
                {text}
            </p>
        </motion.div>
    );
}