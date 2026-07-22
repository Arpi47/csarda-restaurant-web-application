import { motion } from "framer-motion";
import { useLanguage } from "../../contexts/LanguageContext";
import { localizedField } from "../../utils/localization";

export default function MenuCard({ item }) {
    const { language } = useLanguage();
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;
    return (
        <motion.article
            variants={{
                hidden:{
                    opacity:0,
                    y:40
                },
                visible:{
                    opacity:1,
                    y:0
                }
            }}
            whileHover={{
                y:-10
            }}
            transition={{
                duration:0.3
            }}
            className="
                bg-[var(--color-surface)]
                rounded-3xl
                overflow-hidden
                shadow-md
                hover:shadow-2xl
                transition-shadow
                duration-300
            "
        >
            {/* IMAGE */}
            <div
                className="
                    relative
                    overflow-hidden
                    h-72
                "
            >
                <img
                    src={`${ASSET_URL}/images/${item.image}`}
                    alt={localizedField(
                        item,
                        "name",
                        language
                    )}
                    onError={(e) => {
                        e.target.src = "/placeholder.jpg";
                    }}
                    className="
                        w-full
                        h-full
                        object-cover
                        transition-transform
                        duration-700
                        hover:scale-110
                    "
                />
                {/* Price badge */}
                <div
                    className="
                        absolute
                        top-4
                        right-4
                        bg-[var(--color-surface)]/90
                        backdrop-blur
                        px-4
                        py-2
                        rounded-full
                        font-bold
                        shadow
                    "
                >
                    {Number(item.price).toLocaleString()}
                    {" "}
                    RSD
                </div>
            </div>
            {/* CONTENT */}
            <div className="p-6">
                {/* CATEGORY */}
                <span
                    className="
                        inline-block
                        mb-3
                        px-4
                        py-1
                        rounded-full
                        text-xs
                        font-medium
                        bg-black/5
                        dark:bg-white/10
                        text-[var(--color-muted)]
                    "
                >
                    {
                        localizedField(
                            item.category,
                            "name",
                            language
                        )
                    }
                </span>
                {/* TITLE */}
                <h3
                    className="
                        text-2xl
                        font-bold
                        mb-3
                    "
                >
                    {
                        localizedField(
                            item,
                            "name",
                            language
                        )
                    }
                </h3>
                {/* DESCRIPTION */}
                <p
                    className="
                        text-[var(--color-muted)]
                        leading-relaxed
                        line-clamp-3
                    "
                >
                    {
                        localizedField(
                            item,
                            "description",
                            language
                        )
                    }
                </p>
            </div>
        </motion.article>
    );
}