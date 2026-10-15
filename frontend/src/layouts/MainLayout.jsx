import { Outlet } from "react-router-dom";
import Navbar from "../components/layout/Navbar";
import Footer from "../components/layout/Footer";
import ScrollToTopButton from "../components/common/ScrollToTopButton";
import CallButton from "../components/common/CallButton";
import { SiteDataProvider } from "../contexts/SiteDataContext";
import ScrollToTop from "../components/common/ScrollToTop";
import {
    LightboxProvider,
    useLightbox,
} from "../contexts/LightboxContext";

function MainLayoutContent() {
    const { isLightboxOpen } = useLightbox();
    return (
        <div className="min-h-screen flex flex-col">
            <ScrollToTop />
            <Navbar />
            <main className="flex-1">
                <Outlet />
            </main>
            <Footer />
            {!isLightboxOpen && (
                <>
                    <ScrollToTopButton />
                    <CallButton />
                </>
            )}
        </div>
    );
}
export default function MainLayout() {
    return (
        <SiteDataProvider>
            <LightboxProvider>
                <MainLayoutContent />
            </LightboxProvider>
        </SiteDataProvider>
    );
}