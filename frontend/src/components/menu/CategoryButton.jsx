import { motion } from "framer-motion";

export default function CategoryButton({ active, children, onClick }) {
    return (
        <motion.button
            whileTap={{
                scale: 0.95,
            }}
            onClick={onClick}
            className={`
                relative
                px-6
                py-3
                rounded-full
                text-sm
                font-medium
                border
                theme-border
                transition-all
                duration-300
                whitespace-nowrap
                cursor-pointer
                ${
                    active
                        ? "theme-button"
                        : "bg-[var(--color-overlay)] text-[var(--color-text)] hover:opacity-80"
                }
                `}
        >
            {active && (
                <motion.div
                    layoutId="activeCategory"
                    className="
                        absolute
                        inset-0
                        bg-[var(--color-text)]
                        rounded-full
                        -z-10
                        "
                />
            )}
            {children}
        </motion.button>
    );
}
