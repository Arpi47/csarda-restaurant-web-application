import client from "./client";

export async function getProfile() {
    const response = await client.get("/profile");
    return response.data;
}
export async function updateProfile(data) {
    const response = await client.put("/profile", data, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
    return response.data;
}
export async function requestDelete() {
    const response = await client.post("/profile/delete-request");
    return response.data;
}
export async function cancelDelete() {
    const response = await client.post("/profile/delete-cancel");
    return response.data;
}
export async function disconnectGoogle() {
    const response = await client.post("/profile/google/disconnect");
    return response.data;
}
