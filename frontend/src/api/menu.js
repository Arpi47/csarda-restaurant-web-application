import client from "./client";

export async function getMenu() {
    const response = await client.get("/menu");
    return response.data;
}

export async function getMenuItem(id) {
    const response = await client.get(`/menu/${id}`);
    return response.data;
}

export async function getMenuReviews(id) {
    const response = await client.get(`/menu/${id}/reviews`);
    return response.data;
}

export async function getMyMenuReview(id) {
    const response = await client.get(
        `/menu/${id}/my-review`,
    );

    return response.data;
}

export async function submitMenuReview(id, review) {
    const response = await client.post(
        `/menu/${id}/reviews`,
        review,
    );

    return response.data;
}

export async function updateMenuReview(id, review) {
    const response = await client.put(
        `/menu/${id}/reviews`,
        review,
    );

    return response.data;
}

export async function getMenuComments(id) {
    const response = await client.get(
        `/menu/${id}/comments`,
    );

    return response.data;
}

export async function submitMenuComment(id, comment) {
    const response = await client.post(
        `/menu/${id}/comments`,
        comment,
    );

    return response.data;
}

export async function deleteMenuComment(menuId, commentId) {
    const response = await client.delete(
        `/menu/${menuId}/comments/${commentId}`,
    );

    return response.data;
}

export async function deleteMenuReview(id) {
    const response = await client.delete(`/menu/${id}/reviews`);
    return response.data;
}

export async function updateMenuComment(menuId, commentId, data) {
    const response = await client.put(
        `/menu/${menuId}/comments/${commentId}`,
        data,
    );
    return response.data;
}
