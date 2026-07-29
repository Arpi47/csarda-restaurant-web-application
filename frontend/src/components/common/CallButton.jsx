import { useEffect, useState } from "react";
import { Phone } from "lucide-react";

export default function CallButton() {
    const [phone, setPhone] = useState("");
    useEffect(() => {
        fetch(`${import.meta.env.VITE_API_URL}/contact`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch contact data.");
                }
                return response.json();
            })
            .then((data) => {
                setPhone(data.information?.phone || "");
            })
            .catch((error) => {
                console.error("Failed to load phone number:", error);
            });
    }, []);
    const phoneLink = phone.replace(/[^\d+]/g, "");
    return (
        <a
            href={phoneLink ? `tel:${phoneLink}` : "#"}
            aria-label="Call Csárda"
            className="
                fixed
                bottom-6
                left-6
                z-50
                w-14
                h-14
                rounded-full
                bg-[var(--color-secondary)]
                hover:bg-[var(--color-button-hover)]
                text-[var(--color-button-text)]
                text-white
                shadow-lg
                flex
                items-center
                justify-center
                hover:cursor-pointer
                transition-all
                duration-300
                desktop:hidden
            "
        >
            <Phone size={26} />
        </a>
    );
}
