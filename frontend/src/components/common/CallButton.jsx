import { Phone } from "lucide-react";

export default function CallButton() {
    return (
        <a
            href="tel:+381XXXXXXXXX"
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