import axios from "axios";

const API_URL = import.meta.env.VITE_API_URL;

export async function sendContactMessage(data) {
    const response = await axios.post(`${API_URL}/api/contact`, data);
    return response.data;
}
