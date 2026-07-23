import axios from "axios";

const API_URL = import.meta.env.VITE_API_URL;
const BACKEND_URL = import.meta.env.VITE_BACKEND_URL;
const client = axios.create({
    baseURL: API_URL,
    headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
    withCredentials: true,
});
client.defaults.withXSRFToken = true;
client.interceptors.request.use(
    async (config) => {
        const language = localStorage.getItem("language") || "en";
        const laravelLocale =
            {
                sr_cyr: "sr_cyrl",
            }[language] ?? language;
        config.headers["Accept-Language"] = laravelLocale;
        const token = localStorage.getItem("token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        if (
            ["post", "put", "patch", "delete"].includes(
                config.method?.toLowerCase(),
            )
        ) {
            await axios.get(`${BACKEND_URL}/sanctum/csrf-cookie`, {
                withCredentials: true,
            });
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

export default client;
