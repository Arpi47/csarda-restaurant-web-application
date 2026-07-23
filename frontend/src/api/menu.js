import client from "./client";

export async function getMenu() {
    const response = await client.get("/menu");
    return response.data;
}
