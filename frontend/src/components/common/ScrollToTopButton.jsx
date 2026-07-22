import { useEffect, useState } from "react";
import { ChevronUp } from "lucide-react";

export default function ScrollToTopButton() {
    const [visible, setVisible] = useState(false);
    useEffect(() => {
        let lastScrollY = window.scrollY;
        const handleScroll = () => {
            const currentScrollY = window.scrollY;
            if (currentScrollY > 150 && currentScrollY > lastScrollY) {
                setVisible(true);
            }
            if (currentScrollY < 100) {
                setVisible(false);
            }
            lastScrollY = currentScrollY;
        };
        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, []);
    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    };
    return (
        <button
            onClick={scrollToTop}
            className={`fixed bottom-6 right-6 z-50
                w-14 h-14 rounded-full
                bg-amber-600 hover:bg-amber-700
                text-white shadow-lg
                flex items-center justify-center
                hover:cursor-pointer
                transition-all duration-300
                ${
                    visible
                        ? "opacity-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 translate-y-5 pointer-events-none"
                }`}
        >
            <ChevronUp size={28} />
        </button>
    );
}