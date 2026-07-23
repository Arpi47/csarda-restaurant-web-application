import { createContext, useContext, useEffect, useState } from "react";
import translations from "../utils/translations";

const LanguageContext = createContext();

export function LanguageProvider({ children }) {
    const [language, setLanguage] = useState(
        localStorage.getItem("language") || "en",
    );
    useEffect(() => {
        localStorage.setItem("language", language);
    }, [language]);
    function t(path) {
        return path
            .split(".")
            .reduce((obj, key) => obj?.[key], translations[language]);
    }
    return (
        <LanguageContext.Provider
            value={{
                language,
                setLanguage,
                t,
            }}
        >
            {children}
        </LanguageContext.Provider>
    );
}

export function useLanguage() {
    return useContext(LanguageContext);
}
