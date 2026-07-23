import client from "./client";

export async function getReservations() {
    const response = await client.get("/reservations");
    return response.data;
}
export async function deleteReservation(id) {
    const response = await client.delete(`/reservations/${id}`);
    return response.data;
}
