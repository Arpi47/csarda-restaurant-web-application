import { createContext, useContext, useState } from "react";

const LightboxContext = createContext(null);

export function LightboxProvider({ children }) {
    const [isLightboxOpen, setIsLightboxOpen] = useState(false);
    return (
        <LightboxContext.Provider
            value={{ isLightboxOpen, setIsLightboxOpen }}
        >
            {children}
        </LightboxContext.Provider>
    );
}
export function useLightbox() {
    const context = useContext(LightboxContext);
    if (!context) {
        throw new Error(
            "useLightbox must be used within a LightboxProvider",
        );
    }
    return context;
}