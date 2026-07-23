import client from "./client";

export async function getGallery() {
    const response = await client.get("/gallery");
    return response.data;
}
