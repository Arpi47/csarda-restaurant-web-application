export default function Heading({ title, subtitle, center = false }) {
    return (
        <div className={center ? "text-center mb-12" : "mb-12"}>
            <h2 className="text-4xl font-bold mb-4">{title}</h2>
            {subtitle && (
                <p
                    className="
                    text-[var(--color-muted)]
                    max-w-2xl
                "
                >
                    {subtitle}
                </p>
            )}
        </div>
    );
}
