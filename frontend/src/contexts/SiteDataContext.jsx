import { createContext, useContext, useEffect, useState } from "react";

const SiteDataContext = createContext();

export function SiteDataProvider({ children }) {
    const [contactInformation, setContactInformation] = useState(null);
    const [socialLinks, setSocialLinks] = useState([]);
    const [restaurantOpeningHours, setRestaurantOpeningHours] = useState([]);
    const [kitchenOpeningHours, setKitchenOpeningHours] = useState([]);
    const [kitchenSpecialHours, setKitchenSpecialHours] = useState([]);
    const [serbianHolidays, setSerbianHolidays] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    useEffect(() => {
        async function loadSiteData() {
            try {
                const [contactResponse, openingHoursResponse] =
                    await Promise.all([
                        fetch(`${import.meta.env.VITE_API_URL}/contact`),
                        fetch(`${import.meta.env.VITE_API_URL}/opening-hours`),
                    ]);
                if (!contactResponse.ok || !openingHoursResponse.ok) {
                    throw new Error(
                        "Failed to fetch contact or opening hours data.",
                    );
                }
                const contactData = await contactResponse.json();
                const openingHoursData =
                    await openingHoursResponse.json();
                setContactInformation(
                    contactData.information || null,
                );
                setSocialLinks(
                    contactData.socialLinks || [],
                );
                setRestaurantOpeningHours(
                    openingHoursData.restaurant_weekly || [],
                );
                setKitchenOpeningHours(
                    openingHoursData.kitchen_weekly || [],
                );
                setKitchenSpecialHours(
                    openingHoursData.kitchen_special || [],
                );
                setSerbianHolidays(
                    openingHoursData.serbian_holidays || [],
                );
            } catch (error) {
                console.error(
                    "Failed to load site data:",
                    error,
                );
                setError(error);
            } finally {
                setLoading(false);
            }
        }
        loadSiteData();
    }, []);
    return (
        <SiteDataContext.Provider
            value={{
                contactInformation,
                socialLinks,
                restaurantOpeningHours,
                kitchenOpeningHours,
                kitchenSpecialHours,
                serbianHolidays,
                loading,
                error,
            }}
        >
            {children}
        </SiteDataContext.Provider>
    );
}

export function useSiteData() {
    return useContext(SiteDataContext);
}