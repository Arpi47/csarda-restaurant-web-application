import client from "./client";

export async function login(data) {
    const response = await client.post("/login", data);
    return response.data;
}
export async function logout() {
    try {
        const response = await client.post("/logout");
        return response.data;
    } finally {
        localStorage.removeItem("token");
    }
}
export async function register(data) {
    const response = await client.post("/register", data);
    return response.data;
}
export async function verifyEmail(token) {
    const response = await client.get(`/verify-email/${token}`);
    return response.data;
}
export async function forgotPassword(data) {
    const response = await client.post("/forgot-password", data);
    return response.data;
}
export async function resetPassword(data) {
    const response = await client.post("/reset-password", data);
    return response.data;
}
