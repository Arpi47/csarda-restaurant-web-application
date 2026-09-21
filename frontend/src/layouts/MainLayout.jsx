import { Outlet } from "react-router-dom";
import Navbar from "../components/layout/Navbar";
import Footer from "../components/layout/Footer";
import ScrollToTopButton from "../components/common/ScrollToTopButton";
import CallButton from "../components/common/CallButton";
import { SiteDataProvider } from "../contexts/SiteDataContext";

export default function MainLayout() {
    return (
        <SiteDataProvider>
            <div className="min-h-screen flex flex-col">
                <Navbar />
                <main className="flex-1">
                    <Outlet />
                </main>
                <Footer />
                <ScrollToTopButton />
                <CallButton />
            </div>
        </SiteDataProvider>
    );
}
