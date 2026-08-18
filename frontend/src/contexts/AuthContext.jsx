import { createContext, useContext, useEffect, useState } from "react";
import client from "../api/client";

const AuthContext = createContext();

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        async function loadUser() {
            const token = localStorage.getItem("token");
            if (!token) {
                setLoading(false);
                return;
            }
            try {
                const response = await client.get("/user");
                setUser(response.data);
            } catch (error) {
                localStorage.removeItem("token");
                setUser(null);
            } finally {
                setLoading(false);
            }
        }
        loadUser();
    }, []);
    useEffect(() => {
        const handleLogout = () => {
            setUser(null);
        };
        window.addEventListener("auth:logout", handleLogout);
        return () => {
            window.removeEventListener("auth:logout", handleLogout);
        };
    }, []);
    return (
        <AuthContext.Provider
            value={{
                user,
                setUser,
                loading,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    return useContext(AuthContext);
}
