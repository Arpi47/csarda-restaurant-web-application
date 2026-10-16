import { useEffect, useRef, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import {
    getMenuItem,
    getMenuReviews,
    getMyMenuReview,
    submitMenuReview,
    updateMenuReview,
    deleteMenuReview,
    getMenuComments,
    submitMenuComment,
    updateMenuComment,
    deleteMenuComment,
} from "../api/menu";
import { useAuth } from "../contexts/AuthContext";
import { useLanguage } from "../contexts/LanguageContext";
import { localizedField } from "../utils/localization";
import PageHeader from "../components/common/PageHeader";
import { Pencil, Trash2, Send, CornerDownRight, Ban } from "lucide-react";
import { useLightbox } from "../contexts/LightboxContext";
function CommentThread({
    comment,
    user,
    t,
    dateLocale,
    depth = 0,
    editingCommentId,
    editingCommentBody,
    setEditingCommentBody,
    startEditingComment,
    cancelEditingComment,
    handleUpdateComment,
    handleDeleteComment,
    replyingToId,
    setReplyingToId,
    replyBodies,
    setReplyBodies,
    handleReplySubmit,
    commentDeleting,
    commentUpdating,
    replySubmitting,
}) {
    const isEditing = editingCommentId === comment.id;
    const isReplying = replyingToId === comment.id;
    const isOwnComment =
        user &&
        !comment.is_admin &&
        Number(comment.user_id) === Number(user.id);
    return (
        <div className="flex min-w-0 flex-col">
            <article
                className={`min-w-0 rounded-2xl border border-[var(--color-border)] p-4 sm:p-5 ${
                    comment.is_admin
                        ? "bg-[var(--color-hover-bg)]"
                        : "bg-[var(--color-surface)]"
                }`}
            >
                <div className="mb-3 flex flex-wrap items-start justify-between gap-3">
                    <div className="min-w-0">
                        <p className="font-semibold">
                            {comment.is_admin
                                ? "Csárda"
                                : comment.user?.full_name ||
                                t("menu.unknownUser")}
                        </p>
                        {comment.created_at && (
                            <div className="mt-1 flex flex-wrap gap-x-2 text-sm text-[var(--color-muted)]">
                                <time dateTime={comment.created_at}>
                                    {new Date(comment.created_at).toLocaleDateString(
                                        dateLocale,
                                    )}
                                </time>
                                {comment.updated_at &&
                                    new Date(comment.updated_at).getTime() >
                                        new Date(comment.created_at).getTime() && (
                                        <span>
                                            ({t("menu.edited")}:{" "}
                                            <time dateTime={comment.updated_at}>
                                                {new Date(
                                                    comment.updated_at,
                                                ).toLocaleDateString(dateLocale)}
                                            </time>
                                            )
                                        </span>
                                    )}
                            </div>
                        )}
                    </div>
                    {isOwnComment && (
                        <div className="flex shrink-0 gap-2">
                            <button
                                type="button"
                                onClick={() => startEditingComment(comment)}
                                disabled={
                                    commentDeleting ||
                                    commentUpdating ||
                                    replySubmitting ||
                                    (editingCommentId !== null &&
                                        editingCommentId !== comment.id)
                                }
                                aria-label={t("menu.editComment")}
                                title={t("menu.editComment")}
                                className="flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Pencil size={18} aria-hidden="true" />
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDeleteComment(comment.id)}
                                disabled={commentDeleting || commentUpdating}
                                aria-label={t("menu.deleteReviewComment")}
                                title={t("menu.deleteReviewComment")}
                                className="flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-red-600 bg-red-600 text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Trash2 size={18} aria-hidden="true" />
                            </button>
                        </div>
                    )}
                </div>
                {isEditing ? (
                    <div className="mt-3 space-y-3">
                        <textarea
                            value={editingCommentBody}
                            onChange={(event) =>
                                setEditingCommentBody(event.target.value)
                            }
                            maxLength={2000}
                            rows={4}
                            className="theme-input w-full resize-none"
                            aria-label={t("menu.editComment")}
                        />
                        <div className="flex flex-wrap justify-end gap-3">
                            <button
                                type="button"
                                onClick={cancelEditingComment}
                                disabled={commentUpdating}
                                className="inline-flex h-10 w-28 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] px-4 py-2 font-semibold text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {t("menu.cancelEdit")}
                            </button>
                            <button
                                type="button"
                                onClick={() => handleUpdateComment(comment.id)}
                                disabled={commentUpdating}
                                className="inline-flex h-10 w-28 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] px-4 py-2 font-semibold text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {commentUpdating
                                    ? t("menu.loadingDetails")
                                    : t("menu.saveEdit")}
                            </button>
                        </div>
                    </div>
                ) : (
                    <p className="whitespace-pre-line break-words leading-relaxed text-[var(--color-muted)]">
                        {comment.body}
                    </p>
                )}
                {user && !isEditing && (
                    <div className="mt-4">
                        <button
                            type="button"
                            onClick={() =>
                                setReplyingToId(
                                    isReplying ? null : comment.id,
                                )
                            }
                            disabled={
                                commentDeleting ||
                                commentUpdating ||
                                replySubmitting
                            }
                            className="flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] px-3 text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {isReplying ? (
                                <Ban size={16} aria-hidden="true" />
                            ) : (
                                <>
                                    <CornerDownRight
                                        size={16}
                                        aria-hidden="true"
                                    />
                                    {t("menu.replyToComment")}
                                </>
                            )}
                        </button>
                    </div>
                )}
                {isReplying && user && (
                    <form
                        onSubmit={(event) =>
                            handleReplySubmit(event, comment.id)
                        }
                        className="mt-3 space-y-3"
                    >
                        <textarea
                            value={replyBodies[comment.id] ?? ""}
                            onChange={(event) =>
                                setReplyBodies((previous) => ({
                                    ...previous,
                                    [comment.id]: event.target.value,
                                }))
                            }
                            maxLength={2000}
                            rows={3}
                            required
                            placeholder={t("menu.replyPlaceholder")}
                            className="theme-input w-full resize-none"
                        />
                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={
                                    replySubmitting ||
                                    (replyBodies[comment.id] ?? "").trim().length < 5
                                }
                                aria-label={t("menu.submitReply")}
                                title={t("menu.submitReply")}
                                className="flex h-10 w-20 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {replySubmitting ? (
                                    <span aria-hidden="true">…</span>
                                ) : (
                                    <Send size={20} aria-hidden="true" />
                                )}
                            </button>
                        </div>
                    </form>
                )}
            </article>
            {comment.replies?.length > 0 && (
                <div
                    className={`relative mt-4 ml-3 flex min-w-0 flex-col gap-4 border-l-2 border-[var(--color-border)] pl-3 sm:ml-5 sm:pl-5 ${
                        depth > 0 ? "before:absolute before:-left-2 before:top-0 before:h-4 before:w-2 before:border-t-2 before:border-[var(--color-border)]" : ""
                    }`}
                >
                    {comment.replies.map((reply) => (
                        <div key={reply.id} className="relative min-w-0">
                            <span
                                aria-hidden="true"
                                className="absolute -left-3 top-5 w-3 border-t-2 border-[var(--color-border)] sm:-left-5 sm:w-5"
                            />
                            <CommentThread
                                comment={reply}
                                user={user}
                                t={t}
                                dateLocale={dateLocale}
                                depth={depth + 1}
                                editingCommentId={editingCommentId}
                                editingCommentBody={editingCommentBody}
                                setEditingCommentBody={setEditingCommentBody}
                                startEditingComment={startEditingComment}
                                cancelEditingComment={cancelEditingComment}
                                handleUpdateComment={handleUpdateComment}
                                handleDeleteComment={handleDeleteComment}
                                replyingToId={replyingToId}
                                setReplyingToId={setReplyingToId}
                                replyBodies={replyBodies}
                                setReplyBodies={setReplyBodies}
                                handleReplySubmit={handleReplySubmit}
                                commentDeleting={commentDeleting}
                                commentUpdating={commentUpdating}
                                replySubmitting={replySubmitting}
                            />
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
export default function MenuDetail() {
    const { id } = useParams();
    const { language, t } = useLanguage();
    const { user, loading: authLoading } = useAuth();
    const [item, setItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);
    const [rating, setRating] = useState(0);
    const [reviewComment, setReviewComment] = useState("");
    const [averageRating, setAverageRating] = useState(0);
    const [reviewsCount, setReviewsCount] = useState(0);
    const [reviews, setReviews] = useState([]);
    const [reviewsLoading, setReviewsLoading] = useState(true);
    const [myReview, setMyReview] = useState(null);
    const [myReviewLoading, setMyReviewLoading] = useState(false);
    const [reviewSubmitting, setReviewSubmitting] = useState(false);
    const [reviewDeleting, setReviewDeleting] = useState(false);
    const [reviewMessage, setReviewMessage] = useState("");
    const [reviewError, setReviewError] = useState("");
    const [comment, setComment] = useState("");
    const [comments, setComments] = useState([]);
    const [commentsLoading, setCommentsLoading] = useState(true);
    const [commentSubmitting, setCommentSubmitting] = useState(false);
    const [commentDeleting, setCommentDeleting] = useState(false);
    const [commentUpdating, setCommentUpdating] = useState(false);
    const [commentMessage, setCommentMessage] = useState("");
    const [commentError, setCommentError] = useState("");
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editingCommentBody, setEditingCommentBody] = useState("");
    const [replyingToId, setReplyingToId] = useState(null);
    const [replyBodies, setReplyBodies] = useState({});
    const [replySubmitting, setReplySubmitting] = useState(false);
    const [[selectedIndex, direction], setSelected] = useState([0, 0]);
    const [isLightboxOpen, setIsLightboxOpen] = useState(false);
    const thumbnailRefs = useRef([]);
    const mainGalleryRef = useRef(null);
    const lightboxRef = useRef(null);
    const lightboxThumbnailRefs = useRef([]);
    const { setIsLightboxOpen: setGlobalLightboxOpen } = useLightbox();
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;
    const dateLocale = {
        en: "en-US",
        hu: "hu-HU",
        sr_lat: "sr-Latn-RS",
        sr_cyr: "sr-Cyrl-RS",
    }[language] || "en-US";
    useEffect(() => {
        let cancelled = false;
        async function loadMenuItem() {
            setLoading(true);
            setError(false);
            setItem(null);
            setSelected([0, 0]);
            setIsLightboxOpen(false);
            try {
                const data = await getMenuItem(id);
                if (!cancelled) {
                    setItem(data);
                }
            } catch {
                if (!cancelled) {
                    setError(true);
                }
            } finally {
                if (!cancelled) {
                    setLoading(false);
                }
            }
        }
        loadMenuItem();
        return () => {
            cancelled = true;
        };
    }, [id]);
    useEffect(() => {
        let cancelled = false;
        async function loadReviews() {
            setReviewsLoading(true);
            try {
                const data = await getMenuReviews(id);
                if (!cancelled) {
                    setReviews(data.reviews ?? []);
                    setAverageRating(Number(data.average_rating ?? 0));
                    setReviewsCount(
                        Number(data.ratings_count ?? data.reviews_count ?? 0),
                    );
                }
            } catch {
                if (!cancelled) {
                    setReviews([]);
                    setAverageRating(0);
                    setReviewsCount(0);
                }
            } finally {
                if (!cancelled) {
                    setReviewsLoading(false);
                }
            }
        }
        loadReviews();
        return () => {
            cancelled = true;
        };
    }, [id]);
    useEffect(() => {
        let cancelled = false;
        async function loadComments() {
            setCommentsLoading(true);
            try {
                const data = await getMenuComments(id);
                if (!cancelled) {
                    setComments(data.comments ?? []);
                }
            } catch {
                if (!cancelled) {
                    setComments([]);
                }
            } finally {
                if (!cancelled) {
                    setCommentsLoading(false);
                }
            }
        }
        loadComments();
        return () => {
            cancelled = true;
        };
    }, [id]);
    useEffect(() => {
        let cancelled = false;
        async function loadMyReview() {
            if (authLoading) {
                return;
            }
            if (!user) {
                setMyReview(null);
                setRating(0);
                setReviewComment("");
                setMyReviewLoading(false);
                return;
            }
            setMyReviewLoading(true);
            try {
                const data = await getMyMenuReview(id);

                if (!cancelled) {
                    const review = data.review ?? null;
                    setMyReview(review);
                    setRating(review?.rating ?? 0);
                    setReviewComment(review?.comment ?? "");
                }
            } catch {
                if (!cancelled) {
                    setMyReview(null);
                    setRating(0);
                    setReviewComment("");
                }
            } finally {
                if (!cancelled) {
                    setMyReviewLoading(false);
                }
            }
        }
        loadMyReview();
        return () => {
            cancelled = true;
        };
    }, [id, user, authLoading]);
    async function refreshReviews() {
        try {
            const data = await getMenuReviews(id);
            setReviews(data.reviews ?? []);
            setAverageRating(Number(data.average_rating ?? 0));
            setReviewsCount(
                Number(data.ratings_count ?? data.reviews_count ?? 0),
            );
        } catch {
            setReviewError(t("menu.reviewSubmitError"));
        }
    }
    async function refreshComments() {
        try {
            const data = await getMenuComments(id);
            setComments(data.comments ?? []);
        } catch {
            setCommentError(t("menu.commentLoadError"));
        }
    }
    function handleRatingSelect(value) {
        if (reviewSubmitting || reviewDeleting || !user) {
            return;
        }
        setRating(value);
        setReviewMessage("");
        setReviewError("");
    }
    async function handleReviewSubmit(event) {
        event.preventDefault();
        if (!user || reviewSubmitting || reviewDeleting) {
            return;
        }
        if (!rating) {
            setReviewError(t("menu.ratingRequired"));
            setReviewMessage("");
            return;
        }
        const payload = {
            rating,
            comment: reviewComment.trim() || null,
        };
        setReviewSubmitting(true);
        setReviewMessage("");
        setReviewError("");
        try {
            const response = myReview
                ? await updateMenuReview(id, payload)
                : await submitMenuReview(id, payload);
            setMyReview(response.review);
            setRating(response.review.rating);
            setReviewComment(response.review.comment ?? "");
            setReviewMessage(t("menu.reviewSubmitted"));
            await refreshReviews();
        } catch (error) {
            const validationErrors = error.response?.data?.errors;
            if (validationErrors) {
                setReviewError(
                    Object.values(validationErrors).flat().join(" "),
                );
            } else if (error.response?.status === 401) {
                setReviewError(t("menu.loginRequired"));
            } else {
                setReviewError(t("menu.reviewSubmitError"));
            }
        } finally {
            setReviewSubmitting(false);
        }
    }
    async function handleDeleteRating() {
        if (!myReview || reviewDeleting || reviewSubmitting) {
            return;
        }
        if (!window.confirm(t("menu.confirmDeleteRating"))) {
            return;
        }
        setReviewDeleting(true);
        setReviewMessage("");
        setReviewError("");
        try {
            await deleteMenuReview(id);
            setMyReview(null);
            setRating(0);
            setReviewComment("");
            setReviewMessage(t("menu.ratingDeleted"));
            await refreshReviews();
        } catch (error) {
            if (error.response?.status === 401) {
                setReviewError(t("menu.loginRequired"));
            } else {
                setReviewError(t("menu.ratingDeleteError"));
            }
        } finally {
            setReviewDeleting(false);
        }
    }
    async function handleCommentSubmit(event) {
        event.preventDefault();
        const body = comment.trim();
        if (body.length < 5) {
            setCommentError(t("menu.reviewCommentMinLength"));
            setCommentMessage("");
            return;
        }
        setCommentSubmitting(true);
        setCommentMessage("");
        setCommentError("");
        try {
            await submitMenuComment(id, { body });
            setComment("");
            setCommentMessage(t("menu.commentSubmitted"));
            await refreshComments();
        } catch (error) {
            const validationErrors = error.response?.data?.errors;
            if (validationErrors) {
                setCommentError(
                    Object.values(validationErrors).flat().join(" "),
                );
            } else if (error.response?.status === 401) {
                setCommentError(t("menu.loginRequired"));
            } else {
                setCommentError(t("menu.commentSubmitError"));
            }
        } finally {
            setCommentSubmitting(false);
        }
    }
    async function handleReplySubmit(event, parentId) {
        event.preventDefault();
        const body = (replyBodies[parentId] ?? "").trim();
        if (body.length < 5) {
            setCommentError(t("menu.reviewCommentMinLength"));
            setCommentMessage("");
            return;
        }
        setReplySubmitting(true);
        setCommentMessage("");
        setCommentError("");
        try {
            await submitMenuComment(id, {
                body,
                parent_id: parentId,
            });
            setReplyBodies((previous) => ({
                ...previous,
                [parentId]: "",
            }));
            setReplyingToId(null);
            setCommentMessage(t("menu.commentSubmitted"));
            await refreshComments();
        } catch (error) {
            const validationErrors = error.response?.data?.errors;
            if (validationErrors) {
                setCommentError(
                    Object.values(validationErrors).flat().join(" "),
                );
            } else if (error.response?.status === 401) {
                setCommentError(t("menu.loginRequired"));
            } else {
                setCommentError(t("menu.commentSubmitError"));
            }
        } finally {
            setReplySubmitting(false);
        }
    }
    function startEditingComment(commentItem) {
        if (commentUpdating || commentDeleting || replySubmitting) {
            return;
        }
        setEditingCommentId(commentItem.id);
        setEditingCommentBody(commentItem.body);
        setReplyingToId(null);
        setCommentMessage("");
        setCommentError("");
    }
    function cancelEditingComment() {
        if (commentUpdating) {
            return;
        }
        setEditingCommentId(null);
        setEditingCommentBody("");
        setCommentMessage("");
        setCommentError("");
    }
    async function handleUpdateComment(commentId) {
        if (commentUpdating) {
            return;
        }
        const body = editingCommentBody.trim();
        if (body.length < 5) {
            setCommentError(t("menu.reviewCommentMinLength"));
            setCommentMessage("");
            return;
        }
        setCommentUpdating(true);
        setCommentMessage("");
        setCommentError("");
        try {
            await updateMenuComment(id, commentId, { body });
            setEditingCommentId(null);
            setEditingCommentBody("");
            setCommentMessage(t("menu.commentUpdated"));
            await refreshComments();
        } catch (error) {
            if (error.response?.status === 401) {
                setCommentError(t("menu.loginRequired"));
            } else if (error.response?.status === 422) {
                setCommentError(t("menu.commentEditNotAllowed"));
            } else {
                const validationErrors = error.response?.data?.errors;
                setCommentError(
                    validationErrors
                        ? Object.values(validationErrors).flat().join(" ")
                        : t("menu.commentUpdateError"),
                );
            }
        } finally {
            setCommentUpdating(false);
        }
    }
    async function handleDeleteComment(commentId) {
        if (commentDeleting || commentUpdating) {
            return;
        }
        if (!window.confirm(t("menu.confirmDeleteReviewComment"))) {
            return;
        }
        setCommentDeleting(true);
        setCommentMessage("");
        setCommentError("");
        try {
            await deleteMenuComment(id, commentId);

            if (editingCommentId === commentId) {
                setEditingCommentId(null);
                setEditingCommentBody("");
            }
            if (replyingToId === commentId) {
                setReplyingToId(null);
            }
            setCommentMessage(t("menu.reviewCommentDeleted"));
            await refreshComments();
        } catch (error) {
            if (error.response?.status === 401) {
                setCommentError(t("menu.loginRequired"));
            } else {
                setCommentError(t("menu.reviewCommentDeleteError"));
            }
        } finally {
            setCommentDeleting(false);
        }
    }
    const images = item
        ? [
              { id: `main-${item.id}`, image: item.image },
              ...(item.images ?? []),
          ]
        : [];
    const selectedImage = images[selectedIndex];
    function nextImage() {
        if (images.length < 2) {
            return;
        }
        setSelected(([currentIndex]) => [
            (currentIndex + 1) % images.length,
            1,
        ]);
    }
    function previousImage() {
        if (images.length < 2) {
            return;
        }
        setSelected(([currentIndex]) => [
            (currentIndex - 1 + images.length) % images.length,
            -1,
        ]);
    }
    useEffect(() => {
        function handleWheel(event) {
            event.preventDefault();
            if (images.length < 2) {
                return;
            }
            if (event.deltaY > 0) {
                nextImage();
            } else if (event.deltaY < 0) {
                previousImage();
            }
        }
        const elements = [
            mainGalleryRef.current,
            lightboxRef.current,
        ].filter(Boolean);
        elements.forEach((element) => {
            element.addEventListener("wheel", handleWheel, {
                passive: false,
            });
        });
        return () => {
            elements.forEach((element) => {
                element.removeEventListener("wheel", handleWheel);
            });
        };
    }, [images.length, isLightboxOpen]);
    const imageVariants = {
        enter: (dir) => ({
            x: dir > 0 ? 300 : -300,
            opacity: 0,
        }),
        center: {
            x: 0,
            opacity: 1,
        },
        exit: (dir) => ({
            x: dir > 0 ? -300 : 300,
            opacity: 0,
        }),
    };
    useEffect(() => {
        if (!item || images.length === 0) {
            return;
        }
        function handleKeyDown(event) {
            if (event.key === "Escape" && isLightboxOpen) {
                setIsLightboxOpen(false);
                return;
            }
            if (!isLightboxOpen || images.length < 2) {
                return;
            }
            if (event.key === "ArrowRight") {
                nextImage();
            } else if (event.key === "ArrowLeft") {
                previousImage();
            }
        }
        window.addEventListener("keydown", handleKeyDown);
        return () => {
            window.removeEventListener("keydown", handleKeyDown);
        };
    }, [item, images.length, isLightboxOpen]);
    useEffect(() => {
        const refs = isLightboxOpen
            ? lightboxThumbnailRefs.current
            : thumbnailRefs.current;
        const thumbnailIndex = isLightboxOpen
            ? selectedIndex
            : selectedIndex - 1;
        if (thumbnailIndex >= 0 && refs[thumbnailIndex]) {
            refs[thumbnailIndex].scrollIntoView({
                behavior: "smooth",
                inline: "center",
                block: "nearest",
            });
        }
    }, [selectedIndex, isLightboxOpen]);
    useEffect(() => {
        if (!isLightboxOpen) {
            return;
        }
        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";
        return () => {
            document.body.style.overflow = previousOverflow;
        };
    }, [isLightboxOpen]);
    useEffect(() => {
        setGlobalLightboxOpen(isLightboxOpen);
        return () => {
            setGlobalLightboxOpen(false);
        };
    }, [isLightboxOpen, setGlobalLightboxOpen]);
    if (loading) {
        return (
            <section className="flex min-h-[50vh] items-center justify-center px-6">
                <p className="text-[var(--color-muted)]">
                    {t("menu.loadingDetails")}
                </p>
            </section>
        );
    }
    if (error || !item) {
        return (
            <section className="flex min-h-[50vh] flex-col items-center justify-center gap-4 px-6 text-center">
                <p className="text-[var(--color-muted)]">
                    {error
                        ? t("menu.detailsError")
                        : t("menu.notFound")}
                </p>
                <Link
                    to="/menu"
                    className="font-semibold"
                >
                    {t("menu.backToMenu")}
                </Link>
            </section>
        );
    }
    return (
        <section className="mx-auto max-w-7xl px-6 py-12">
            <PageHeader
                title={localizedField(item, "name", language)}
                subtitle={t("menu.detailsSubtitle")}
            />
            <motion.article
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.1 }}
                className="grid grid-cols-1 items-start gap-8 md:grid-cols-2 lg:gap-12"
            >
                <div className="min-w-0">
                    <div
                        ref={mainGalleryRef}
                        onClick={() => setIsLightboxOpen(true)}
                        onKeyDown={(event) => {
                            if (
                                event.target === event.currentTarget &&
                                (event.key === "Enter" || event.key === " ")
                            ) {
                                event.preventDefault();
                                setIsLightboxOpen(true);
                            }
                        }}
                        role="button"
                        tabIndex={0}
                        aria-label="Open image in full screen"
                        className="relative cursor-zoom-in overflow-hidden rounded-3xl bg-black/5 shadow-lg"
                    >
                        <AnimatePresence
                            mode="wait"
                            initial={false}
                            custom={direction}
                        >
                            <motion.img
                                key={selectedImage?.id}
                                src={`${ASSET_URL}/images/${selectedImage?.image}`}
                                alt={`${localizedField(item, "name", language)}${
                                    images.length > 1
                                        ? ` (${selectedIndex + 1}/${images.length})`
                                        : ""
                                }`}
                                onError={(event) => {
                                    event.currentTarget.onerror = null;
                                    event.currentTarget.src = "/placeholder.jpg";
                                }}
                                custom={direction}
                                variants={imageVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{
                                    duration: 0.1,
                                    ease: "easeInOut",
                                }}
                                drag={images.length > 1 ? "x" : false}
                                dragConstraints={{ left: 0, right: 0 }}
                                dragElastic={0.15}
                                onDragEnd={(event, info) => {
                                    if (info.offset.x < -80) {
                                        nextImage();
                                    } else if (info.offset.x > 80) {
                                        previousImage();
                                    }
                                }}
                                whileDrag={{ scale: 0.97 }}
                                className={`aspect-[4/3] w-full object-cover ${
                                    images.length > 1
                                        ? "cursor-grab active:cursor-grabbing"
                                        : ""
                                }`}
                            />
                        </AnimatePresence>
                        {images.length > 1 && (
                            <>
                                <button
                                    type="button"
                                    onClick={(event) => {
                                        event.stopPropagation();
                                        previousImage();
                                    }}
                                    aria-label="Previous image"
                                    className="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white transition-colors hover:bg-black/75 cursor-pointer"
                                >
                                    ❮
                                </button>
                                <button
                                    type="button"
                                    onClick={(event) => {
                                        event.stopPropagation();
                                        nextImage();
                                    }}
                                    aria-label="Next image"
                                    className="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white transition-colors hover:bg-black/75 cursor-pointer"
                                >
                                    ❯
                                </button>
                                <span className="pointer-events-none absolute bottom-3 right-3 rounded-full bg-black/60 px-3 py-1 text-sm text-white">
                                    {selectedIndex + 1} / {images.length}
                                </span>
                            </>
                        )}
                    </div>
                    {images.length > 1 && (
                        <div className="mt-5">
                            <h2 className="mb-3 text-lg font-semibold max-[421px]:text-center">
                                {t("menu.gallery")}
                            </h2>
                            <div className="flex gap-3 overflow-x-auto pb-3">
                                {images.map((image, index) => {
                                    const isSelected =
                                        selectedIndex === index;
                                    return (
                                        <button
                                            key={image.id}
                                            type="button"
                                            ref={(element) => {
                                                thumbnailRefs.current[index] = element;
                                            }}
                                            onClick={() =>
                                                setSelected([
                                                    index,
                                                    index > selectedIndex ? 1 : -1,
                                                ])
                                            }
                                            aria-label={`${t("menu.gallery")} ${index + 1}`}
                                            aria-pressed={isSelected}
                                            className={`h-20 w-20 flex-shrink-0 cursor-pointer overflow-hidden rounded-xl border-2 transition-opacity sm:h-24 sm:w-24 ${
                                                isSelected
                                                    ? "border-[var(--color-primary)] opacity-100"
                                                    : "border-transparent opacity-65 hover:opacity-100"
                                            }`}
                                        >
                                            <img
                                                src={`${ASSET_URL}/images/${image.image}`}
                                                alt=""
                                                onError={(event) => {
                                                    event.currentTarget.onerror = null;
                                                    event.currentTarget.src = "/placeholder.jpg";
                                                }}
                                                className="h-full w-full object-cover"
                                            />
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>
                <div className="min-w-0">
                    <div className="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 sm:p-8 lg:p-9">
                        {item.category && (
                            <div className="mb-5 flex items-center justify-center gap-3">
                                <span className="h-px w-8 bg-[var(--color-primary)]" />
                                <span className="text-sm font-semibold uppercase tracking-[0.12em] text-[var(--color-muted)]">
                                    {localizedField(
                                        item.category,
                                        "name",
                                        language,
                                    )}
                                </span>
                                <span className="h-px w-8 bg-[var(--color-primary)]" />
                            </div>
                        )}
                        <h2 className="text-center text-2xl font-bold leading-tight tracking-tight lg:text-3xl">
                            {localizedField(item, "name", language)}
                        </h2>
                        <div className="mt-7 border-t border-[var(--color-border)] pt-6">
                            <div className="mb-5 flex items-center gap-3">
                                <span className="h-6 w-1 rounded-full bg-[var(--color-primary)]" />
                                <h3 className="text-xl font-semibold">
                                    {t("menu.description")}
                                </h3>
                            </div>
                            <p className="whitespace-pre-line leading-8 text-[var(--color-muted)]">
                                {localizedField(
                                    item,
                                    "description",
                                    language,
                                )}
                            </p>
                        </div>
                        <div className="mt-7 rounded-2xl border border-[var(--color-border)] bg-[var(--color-hover-bg)] p-5">
                            <p className="mb-2 text-sm font-medium text-[var(--color-muted)]">
                                {t("menu.price")}
                            </p>
                            <p className="text-3xl font-bold tracking-tight lg:text-4xl">
                                {Number(item.price).toLocaleString()} RSD
                            </p>
                        </div>
                        <div className="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[var(--color-border)] p-5">
                            <p className="text-2xl font-bold">
                                {reviewsLoading
                                    ? "—"
                                    : `★ ${averageRating.toFixed(1)} / 5`}
                            </p>
                            <p className="text-right text-2xl font-semibold">
                                ({reviewsLoading ? "—" : reviewsCount})
                            </p>
                        </div>
                    </div>
                </div>
                {isLightboxOpen && selectedImage && (
                    <div
                        ref={lightboxRef}
                        onClick={() => setIsLightboxOpen(false)}
                        role="dialog"
                        aria-modal="true"
                        aria-label="Image viewer"
                        className="fixed inset-0 z-50 flex cursor-zoom-out items-center justify-center bg-black/90 p-4 backdrop-blur-sm sm:p-6"
                    >
                        {images.length > 1 && (
                            <button
                                type="button"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    previousImage();
                                }}
                                aria-label="Previous image"
                                className="absolute left-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-2xl text-white transition hover:bg-white/30 cursor-pointer sm:left-6 sm:h-14 sm:w-14 sm:text-3xl"
                            >
                                ❮
                            </button>
                        )}
                        <AnimatePresence
                            mode="wait"
                            initial={false}
                            custom={direction}
                        >
                            <motion.img
                                key={selectedImage.id}
                                src={`${ASSET_URL}/images/${selectedImage.image}`}
                                alt={`${localizedField(item, "name", language)} (${selectedIndex + 1}/${images.length})`}
                                onClick={(event) => event.stopPropagation()}
                                onError={(event) => {
                                    event.currentTarget.onerror = null;
                                    event.currentTarget.src = "/placeholder.jpg";
                                }}
                                custom={direction}
                                variants={imageVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{
                                    duration: 0.1,
                                    ease: "easeInOut",
                                }}
                                drag={images.length > 1 ? "x" : false}
                                dragConstraints={{ left: 0, right: 0 }}
                                dragElastic={0.15}
                                onDragEnd={(event, info) => {
                                    if (info.offset.x < -100) {
                                        nextImage();
                                    } else if (info.offset.x > 100) {
                                        previousImage();
                                    }
                                }}
                                whileDrag={{ scale: 0.96 }}
                                className="max-h-[calc(100vh-9rem)] max-w-full cursor-grab rounded-xl object-contain active:cursor-grabbing"
                            />
                        </AnimatePresence>
                        {images.length > 1 && (
                            <button
                                type="button"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    nextImage();
                                }}
                                aria-label="Next image"
                                className="absolute right-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-2xl text-white transition hover:bg-white/30 cursor-pointer sm:right-6 sm:h-14 sm:w-14 sm:text-3xl"
                            >
                                ❯
                            </button>
                        )}
                        <button
                            type="button"
                            onClick={(event) => {
                                event.stopPropagation();
                                setIsLightboxOpen(false);
                            }}
                            aria-label="Close enlarged image"
                            className="absolute right-6 top-6 flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white/15 text-2xl text-white backdrop-blur transition hover:rotate-90 hover:bg-red-500"
                        >
                            ✕
                        </button>
                        <div className="pointer-events-none absolute left-4 top-4 rounded-full bg-white/15 px-4 py-2 text-sm text-white backdrop-blur sm:left-6 sm:top-6">
                            {selectedIndex + 1} / {images.length}
                        </div>
                        {images.length > 1 && (
                            <div
                                onClick={(event) => event.stopPropagation()}
                                className="group absolute bottom-6 left-1/2 flex max-w-[90%] -translate-x-1/2 gap-3 overflow-x-auto rounded-xl bg-black/30 px-2 py-1 backdrop-blur transition-all duration-300 ease-out hover:px-4 hover:py-3"
                            >
                                {images.map((image, index) => (
                                    <button
                                        key={image.id}
                                        type="button"
                                        ref={(element) => {
                                            lightboxThumbnailRefs.current[index] = element;
                                        }}
                                        onClick={() =>
                                            setSelected([
                                                index,
                                                index > selectedIndex ? 1 : -1,
                                            ])
                                        }
                                        aria-label={`Image ${index + 1}`}
                                        aria-pressed={index === selectedIndex}
                                        className={`h-10 w-10 flex-shrink-0 cursor-pointer overflow-hidden rounded-lg border-2 transition-all duration-300 group-hover:h-16 group-hover:w-16 ${
                                            index === selectedIndex
                                                ? "border-white"
                                                : "border-transparent opacity-60 hover:opacity-100"
                                        }`}
                                    >
                                        <img
                                            src={`${ASSET_URL}/images/${image.image}`}
                                            alt=""
                                            onError={(event) => {
                                                event.currentTarget.onerror = null;
                                                event.currentTarget.src = "/placeholder.jpg";
                                            }}
                                            className="h-full w-full object-cover"
                                        />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </motion.article>
            {!authLoading && !user && (
                <section className="mt-12 border-t border-[var(--color-border)] pt-10">
                    <div className="mx-auto flex min-h-40 w-full max-w-3xl items-center justify-center rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center sm:p-6">
                        <p className="text-[var(--color-muted)]">
                            <Link
                                to="/login"
                                className="font-bold text-[var(--color-text)] transition-colors hover:text-[var(--color-secondary)]"
                            >
                                {t("menu.loginLink")}
                            </Link>{" "}
                            {t("menu.loginToReview")}
                        </p>
                    </div>
                </section>
            )}
            <section className="mt-12 border-t border-[var(--color-border)] pt-10">
                <h2 className="mb-6 text-center text-2xl font-bold">
                    {t("menu.reviews")}
                </h2>
                {(authLoading || user) && (
                    <div className="relative mx-auto mt-8 mb-8 w-full max-w-3xl">
                        {authLoading || (user && myReviewLoading) ? (
                            <div className="w-full rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center sm:p-6">
                                <p className="text-[var(--color-muted)]">
                                    {t("menu.loadingDetails")}
                                </p>
                            </div>
                        ) : user ? (
                            <div className="w-full rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6">
                                <h3 className="mb-4 text-center text-xl font-semibold">
                                    {t("menu.rating")}
                                </h3>
                                <form
                                    onSubmit={handleReviewSubmit}
                                    className="space-y-4"
                                >
                                    <div
                                        className="flex justify-center gap-2"
                                        role="group"
                                        aria-label={t("menu.rating")}
                                    >
                                        {[1, 2, 3, 4, 5].map((value) => (
                                            <button
                                                key={value}
                                                type="button"
                                                onClick={() => handleRatingSelect(value)}
                                                disabled={
                                                    reviewSubmitting ||
                                                    reviewDeleting
                                                }
                                                aria-label={`${value} / 5`}
                                                aria-pressed={rating === value}
                                                className="cursor-pointer text-3xl text-amber-500 transition-transform hover:scale-110 disabled:cursor-wait disabled:opacity-60"
                                            >
                                                {value <= rating ? "★" : "☆"}
                                            </button>
                                        ))}
                                    </div>
                                    <label
                                        htmlFor="menu-review-comment"
                                        className="block text-sm font-medium"
                                    >
                                        {t("menu.reviewTextLabel")}
                                    </label>
                                    <textarea
                                        id="menu-review-comment"
                                        value={reviewComment}
                                        onChange={(event) =>
                                            setReviewComment(event.target.value)
                                        }
                                        maxLength={2000}
                                        rows={4}
                                        placeholder={t("menu.writeReview")}
                                        className="theme-input w-full resize-none"
                                    />
                                    <p className="text-sm text-[var(--color-muted)]">
                                        {t("menu.reviewTextOptional")}
                                    </p>
                                    <div
                                        className={`flex flex-wrap items-center gap-3 ${
                                            myReview
                                                ? "justify-between"
                                                : "justify-end"
                                        }`}
                                    >
                                        {myReview && (
                                            <button
                                                type="button"
                                                onClick={handleDeleteRating}
                                                disabled={
                                                    reviewDeleting ||
                                                    reviewSubmitting
                                                }
                                                className="flex h-10 cursor-pointer items-center justify-center gap-2 rounded-xl border border-red-600 bg-red-600 px-4 text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <Trash2
                                                    size={18}
                                                    aria-hidden="true"
                                                />
                                                {t("menu.deleteRating")}
                                            </button>
                                        )}
                                        <button
                                            type="submit"
                                            disabled={
                                                reviewSubmitting ||
                                                reviewDeleting ||
                                                rating === 0
                                            }
                                            aria-label={t("menu.submitReview")}
                                            title={t("menu.submitReview")}
                                            className="flex h-10 w-20 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {reviewSubmitting ? (
                                                <span aria-hidden="true">…</span>
                                            ) : (
                                                <Send
                                                    size={22}
                                                    aria-hidden="true"
                                                />
                                            )}
                                        </button>
                                    </div>
                                </form>
                                {reviewMessage && (
                                    <p
                                        className="mt-4 text-sm text-green-600"
                                        role="status"
                                    >
                                        {reviewMessage}
                                    </p>
                                )}
                                {reviewError && (
                                    <p
                                        className="mt-4 text-sm text-red-600"
                                        role="alert"
                                    >
                                        {reviewError}
                                    </p>
                                )}
                            </div>
                        ) : null}
                    </div>
                )}
                {reviewsLoading ? (
                    <p className="text-center text-[var(--color-muted)]">
                        {t("menu.loadingDetails")}
                    </p>
                ) : reviews.filter((review) => review.comment?.trim()).length === 0 ? (
                    <p className="text-center text-[var(--color-muted)]">
                        {t("menu.noReviews")}
                    </p>
                ) : (
                    <div className="mx-auto flex w-full max-w-3xl flex-col gap-4">
                        {reviews
                            .filter((review) => review.comment?.trim())
                            .map((review) => (
                            <article
                                key={review.id}
                                className="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5"
                            >
                                <div className="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p className="font-semibold">
                                            {review.user?.full_name ||
                                                review.user?.name ||
                                                t("menu.unknownUser")}
                                        </p>
                                        {review.created_at && (
                                            <p className="mt-1 text-sm text-[var(--color-muted)]">
                                                {new Date(
                                                    review.created_at,
                                                ).toLocaleDateString(dateLocale)}
                                            </p>
                                        )}
                                    </div>
                                    <p
                                        className="text-xl text-amber-500"
                                        aria-label={`${review.rating} / 5`}
                                    >
                                        {"★".repeat(review.rating)}
                                        <span className="text-[var(--color-muted)]">
                                            {"☆".repeat(5 - review.rating)}
                                        </span>
                                    </p>
                                </div>
                                {review.comment && (
                                    <p className="mt-4 whitespace-pre-line break-words leading-relaxed text-[var(--color-muted)]">
                                        {review.comment}
                                    </p>
                                )}
                            </article>
                        ))}
                    </div>
                )}
            </section>
            <section className="mt-16 border-t border-[var(--color-border)] pt-10">
                <h2 className="mb-6 text-center text-2xl font-bold">
                    {t("menu.comments")}
                </h2>
                {user ? (
                    <div className="mx-auto mb-8 w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6">
                        <h3 className="mb-4 text-center text-xl font-semibold">
                            {t("menu.writeComment")}
                        </h3>
                        <form
                            onSubmit={handleCommentSubmit}
                            className="space-y-4"
                        >
                            <textarea
                                id="menu-comment"
                                value={comment}
                                onChange={(event) =>
                                    setComment(event.target.value)
                                }
                                maxLength={2000}
                                rows={4}
                                required
                                placeholder={t("menu.commentPlaceholder")}
                                className="theme-input w-full resize-none"
                            />
                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={
                                        commentSubmitting ||
                                        !comment.trim()
                                    }
                                    aria-label={t("menu.submitComment")}
                                    title={t("menu.submitComment")}
                                    className="flex h-10 w-20 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    {commentSubmitting ? (
                                        <span aria-hidden="true">…</span>
                                    ) : (
                                        <Send
                                            size={22}
                                            aria-hidden="true"
                                        />
                                    )}
                                </button>
                            </div>
                        </form>
                        {commentMessage && (
                            <p
                                className="mt-4 text-sm text-green-600"
                                role="status"
                            >
                                {commentMessage}
                            </p>
                        )}
                        {commentError && (
                            <p
                                className="mt-4 text-sm text-red-600"
                                role="alert"
                            >
                                {commentError}
                            </p>
                        )}
                    </div>
                ) : null}
                <div className="mx-auto flex w-full max-w-3xl flex-col gap-6">
                    {commentsLoading ? (
                        <p className="text-center text-[var(--color-muted)]">
                            {t("menu.loadingDetails")}
                        </p>
                    ) : comments.length === 0 ? (
                        <p className="text-center text-[var(--color-muted)]">
                            {t("menu.noComments")}
                        </p>
                    ) : (
                        comments.map((commentItem) => (
                            <CommentThread
                                key={commentItem.id}
                                comment={commentItem}
                                user={user}
                                t={t}
                                dateLocale={dateLocale}
                                editingCommentId={editingCommentId}
                                editingCommentBody={editingCommentBody}
                                setEditingCommentBody={setEditingCommentBody}
                                startEditingComment={startEditingComment}
                                cancelEditingComment={cancelEditingComment}
                                handleUpdateComment={handleUpdateComment}
                                handleDeleteComment={handleDeleteComment}
                                replyingToId={replyingToId}
                                setReplyingToId={setReplyingToId}
                                replyBodies={replyBodies}
                                setReplyBodies={setReplyBodies}
                                handleReplySubmit={handleReplySubmit}
                                commentDeleting={commentDeleting}
                                commentUpdating={commentUpdating}
                                replySubmitting={replySubmitting}
                            />
                        ))
                    )}
                </div>
            </section>
        </section>
    );
}
