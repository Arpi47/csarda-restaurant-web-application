import { Routes, Route } from "react-router-dom";
import MainLayout from "../layouts/MainLayout";
import Home from "../pages/Home";
import Menu from "../pages/Menu";
import Gallery from "../pages/Gallery";
import About from "../pages/About";
import Contact from "../pages/Contact";
import Reservation from "../pages/Reservation";
import Login from "../pages/Login";
import ProtectedRoute from "./ProtectedRoute";
import GuestRoute from "./GuestRoute";
import Profile from "../pages/Profile";
import UserReservations from "../pages/UserReservations";
import Register from "../pages/Register";
import VerifyEmail from "../pages/VerifyEmail";
import ForgotPassword from "../pages/ForgotPassword";
import ResetPassword from "../pages/ResetPassword";
import CheckEmail from "../pages/CheckEmail";
import VerificationSuccess from "../pages/VerificationSuccess";
import OAuthCallback from "../pages/OAuthCallback";

export default function AppRoutes() {
    return (
        <Routes>
            <Route path="/oauth/callback" element={<OAuthCallback />} />
            <Route element={<MainLayout />}>
                <Route path="/" element={<Home />} />
                <Route path="/menu" element={<Menu />} />
                <Route path="/gallery" element={<Gallery />} />
                <Route path="/about" element={<About />} />
                <Route path="/contact" element={<Contact />} />
                <Route path="/reservation" element={<Reservation />} />
                <Route path="/verify-email/:token" element={<VerifyEmail />} />
                <Route
                    path="/reset-password/:token"
                    element={<ResetPassword />}
                />
                <Route path="/check-email" element={<CheckEmail />} />
                <Route
                    path="/verification-success"
                    element={<VerificationSuccess />}
                />
                <Route
                    path="/login"
                    element={
                        <GuestRoute>
                            <Login />
                        </GuestRoute>
                    }
                />
                <Route
                    path="/register"
                    element={
                        <GuestRoute>
                            <Register />
                        </GuestRoute>
                    }
                />
                <Route
                    path="/forgot-password"
                    element={
                        <GuestRoute>
                            <ForgotPassword />
                        </GuestRoute>
                    }
                />
                <Route
                    path="/profile"
                    element={
                        <ProtectedRoute>
                            <Profile />
                        </ProtectedRoute>
                    }
                />
                <Route
                    path="/reservations"
                    element={
                        <ProtectedRoute>
                            <UserReservations />
                        </ProtectedRoute>
                    }
                />
            </Route>
        </Routes>
    );
}
