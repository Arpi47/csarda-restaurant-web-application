import { useEffect, useState } from "react";
import { getMenu } from "../api/menu";
import MenuCard from "../components/menu/MenuCard";
import { getCategories } from "../api/categories";
import { useLanguage } from "../contexts/LanguageContext";
import { localizedField } from "../utils/localization";
import { motion, AnimatePresence } from "framer-motion";
import CategoryButton from "../components/menu/CategoryButton";
import PageHeader from "../components/common/PageHeader";

export default function Menu() {
    const { language, t } = useLanguage();
    const [items, setItems] = useState([]);
    const [categories, setCategories] = useState([]);
    const [activeCategory, setActiveCategory] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    useEffect(() => {
        Promise.all([
            getMenu(),
            getCategories()
        ])
        .then(([menuData, categoryData]) => {
            setItems(menuData);
            setCategories(categoryData);
        })
        .catch(error => {
            console.error(error);
            setError(true);
        })
        .finally(() => {
            setLoading(false);
        });
    }, []);
    const filteredItems = activeCategory
    ? items.filter(
        item =>
        Number(item.category_id) === activeCategory
    )
    : items;
    if (loading) {
        return (
            <div className="
                py-20
                text-center
                text-xl
            ">
                {t("menu.loading")}
            </div>
        );
    }
    if (error) {
        return (
            <div className="
                py-20
                text-center
                text-red-500
                text-xl
            ">
                {t("menu.error")}
            </div>
        );
    }
    return (
        <div className="page-container">
            <main className="py-12">
                <div className="max-w-7xl mx-auto px-6">
                    <PageHeader
                        title={t("menu.title")}
                        subtitle={t("menu.subtitle")}
                    />
                    <div className="
                        flex
                        gap-3
                        mb-2
                        overflow-x-auto
                        justify-start
                        md:justify-center
                        pb-2
                        scrollbar-hide
                    ">
                        <div
                            className="
                                flex
                                gap-3
                                mb-12
                                overflow-x-auto
                                justify-start
                                md:justify-center
                                pb-2
                            "
                            >
                            <CategoryButton
                                active={activeCategory === null}
                                onClick={() =>
                                    setActiveCategory(null)
                                }
                            >
                                {t("menu.all")}
                            </CategoryButton>
                            {categories.map(category => (
                            <CategoryButton
                                key={category.id}
                                active={
                                    activeCategory === category.id
                                }
                                onClick={() =>
                                    setActiveCategory(category.id)
                                }
                            >
                                {
                                    localizedField(
                                        category,
                                        "name",
                                        language
                                    )
                                }
                            </CategoryButton>
                            ))}
                        </div>
                    </div>
                    {filteredItems.length === 0 && (
                        <div className="
                            text-center
                            text-gray-500
                            text-lg
                            py-10
                        ">
                            {t("menu.empty")}
                        </div>
                    )}
                    {filteredItems.length > 0 && (
                    <AnimatePresence mode="wait">
                        <motion.div
                            key={activeCategory ?? "all"}
                            initial={{
                                opacity:0,
                                y:20
                            }}
                            animate={{
                                opacity:1,
                                y:0
                            }}
                            exit={{
                                opacity:0,
                                y:-20
                            }}
                            className="
                            grid
                            grid-cols-1
                            sm:grid-cols-2
                            xl:grid-cols-3
                            gap-10
                            "
                            >
                                {filteredItems.map(item => (
                                    <MenuCard
                                        key={item.id}
                                        item={item}
                                    />
                                ))}
                        </motion.div>
                    </AnimatePresence>
                    )}
                </div>
            </main>
        </div>
    );
}