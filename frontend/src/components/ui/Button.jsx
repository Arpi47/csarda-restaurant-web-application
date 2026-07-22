export default function Button({
    children,
    className = "",
}) {
    return (
        <button
            className={`
                px-6
                py-3
                rounded-full
                bg-[var(--color-primary)]
                text-white
                font-medium
                transition
                hover:opacity-90
                ${className}
            `}
        >
            {children}
        </button>
    );
}