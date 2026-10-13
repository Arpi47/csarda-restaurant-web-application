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
import { Pencil, Trash2, Send } from "lucide-react";

export default function MenuDetail() {
    const { id } = useParams();
    const { language, t } = useLanguage();
    const { user, loading: authLoading } = useAuth();
    const [item, setItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);
    const [rating, setRating] = useState(0);
    const [averageRating, setAverageRating] = useState(0);
    const [reviewsCount, setReviewsCount] = useState(0);
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
    const [[selectedIndex, direction], setSelected] = useState([0, 0]);
    const thumbnailRefs = useRef([]);
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
                    setAverageRating(
                        Number(data.average_rating ?? 0),
                    );
                    setReviewsCount(
                        Number(
                            data.ratings_count ??
                            data.reviews_count ??
                            0,
                        ),
                    );
                }
            } catch {
                if (!cancelled) {
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
                }
            } catch {
                if (!cancelled) {
                    setMyReview(null);
                    setRating(0);
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
            setAverageRating(
                Number(data.average_rating ?? 0),
            );
            setReviewsCount(
                Number(
                    data.ratings_count ??
                    data.reviews_count ??
                    0,
                ),
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
    async function handleRatingSelect(value) {
        if (
            reviewSubmitting ||
            reviewDeleting ||
            !user
        ) {
            return;
        }
        const previousRating = myReview?.rating ?? 0;
        setRating(value);
        setReviewSubmitting(true);
        setReviewMessage("");
        setReviewError("");
        try {
            const response = myReview
                ? await updateMenuReview(id, { rating: value })
                : await submitMenuReview(id, { rating: value });
            setMyReview(response.review);
            setRating(response.review.rating);
            setReviewMessage(t("menu.reviewSubmitted"));
            await refreshReviews();
        } catch (error) {
            setRating(previousRating);
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
    function startEditingComment(commentItem) {
        if (commentUpdating || commentDeleting) {
            return;
        }
        setEditingCommentId(commentItem.id);
        setEditingCommentBody(commentItem.body);
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
        setSelected([
            (selectedIndex + 1) % images.length,
            1,
        ]);
    }
    function previousImage() {
        if (images.length < 2) {
            return;
        }
        setSelected([
            (selectedIndex - 1 + images.length) % images.length,
            -1,
        ]);
    }
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
        if (!item || images.length < 2) {
            return;
        }
        function handleKeyDown(event) {
            if (event.key === "ArrowRight") {
                setSelected(([current]) => [
                    (current + 1) % images.length,
                    1,
                ]);
            }
            if (event.key === "ArrowLeft") {
                setSelected(([current]) => [
                    (current - 1 + images.length) % images.length,
                    -1,
                ]);
            }
        }
        window.addEventListener("keydown", handleKeyDown);
        return () => {
            window.removeEventListener("keydown", handleKeyDown);
        };
    }, [item, images.length]);
    useEffect(() => {
        if (
            selectedIndex > 0 &&
            thumbnailRefs.current[selectedIndex - 1]
        ) {
            thumbnailRefs.current[selectedIndex - 1].scrollIntoView({
                behavior: "smooth",
                inline: "center",
                block: "nearest",
            });
        }
    }, [selectedIndex]);
    if (loading) {
        return (
            <section className="min-h-[50vh] flex items-center justify-center px-6">
                <p className="text-[var(--color-muted)]">
                    {t("menu.loadingDetails")}
                </p>
            </section>
        );
    }
    if (error || !item) {
        return (
            <section className="min-h-[50vh] flex flex-col items-center justify-center gap-4 px-6 text-center">
                <p className="text-[var(--color-muted)]">
                    {error
                        ? t("menu.detailsError")
                        : t("menu.notFound")}
                </p>
                <Link
                    to="/menu"
                    className="font-semibold underline underline-offset-4"
                >
                    {t("menu.backToMenu")}
                </Link>
            </section>
        );
    }
    return (
        <section className="max-w-7xl mx-auto px-6 py-12">
            <PageHeader
                title={localizedField(item, "name", language)}
                subtitle={t("menu.detailsSubtitle")}
            />
            <motion.article
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.35 }}
                className="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start"
            >
                <div className="min-w-0">
                    <div className="relative overflow-hidden rounded-3xl shadow-lg bg-black/5">
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
                                    duration: 0.3,
                                    ease: "easeInOut",
                                }}
                                drag={images.length > 1 ? "x" : false}
                                dragConstraints={{
                                    left: 0,
                                    right: 0,
                                }}
                                dragElastic={0.15}
                                onDragEnd={(event, info) => {
                                    if (info.offset.x < -80) {
                                        nextImage();
                                    } else if (info.offset.x > 80) {
                                        previousImage();
                                    }
                                }}
                                whileDrag={{ scale: 0.97 }}
                                className={`w-full aspect-[4/3] object-cover ${
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
                                    onClick={previousImage}
                                    aria-label="Previous image"
                                    className="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white text-xl flex items-center justify-center hover:bg-black/75 transition-colors"
                                >
                                    ❮
                                </button>
                                <button
                                    type="button"
                                    onClick={nextImage}
                                    aria-label="Next image"
                                    className="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white text-xl flex items-center justify-center hover:bg-black/75 transition-colors"
                                >
                                    ❯
                                </button>
                                <span className="absolute bottom-3 right-3 rounded-full bg-black/60 text-white text-sm px-3 py-1">
                                    {selectedIndex + 1} / {images.length}
                                </span>
                            </>
                        )}
                    </div>
                    {images.length > 1 && (
                        <div className="mt-5">
                            <h2 className="text-lg font-semibold mb-3">
                                {t("menu.gallery")}
                            </h2>
                            <div className="flex gap-3 overflow-x-auto pb-3">
                                {images.slice(1).map((image, index) => {
                                    const imageIndex = index + 1;
                                    const isSelected =
                                        selectedIndex === imageIndex;
                                    return (
                                        <button
                                            key={image.id}
                                            type="button"
                                            ref={(element) => {
                                                thumbnailRefs.current[index] =
                                                    element;
                                            }}
                                            onClick={() =>
                                                setSelected([
                                                    imageIndex,
                                                    imageIndex > selectedIndex
                                                        ? 1
                                                        : -1,
                                                ])
                                            }
                                            aria-label={`${t("menu.gallery")} ${index + 1}`}
                                            aria-pressed={isSelected}
                                            className={`flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 overflow-hidden rounded-xl border-2 transition-opacity ${
                                                isSelected
                                                    ? "border-[var(--color-primary)] opacity-100"
                                                    : "border-transparent opacity-65 hover:opacity-100"
                                            }`}
                                        >
                                            <img
                                                src={`${ASSET_URL}/images/${image.image}`}
                                                alt=""
                                                onError={(event) => {
                                                    event.currentTarget.onerror =
                                                        null;
                                                    event.currentTarget.src =
                                                        "/placeholder.jpg";
                                                }}
                                                className="w-full h-full object-cover"
                                            />
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>
                <div className="py-2">
                    <h2 className="text-2xl md:text-3xl font-bold mb-5">
                        {t("menu.description")}
                    </h2>
                    {item.category && (
                        <span className="inline-block mb-4 px-4 py-1 rounded-full text-sm font-medium bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-muted)]">
                            {localizedField(item.category, "name", language)}
                        </span>
                    )}
                    <p className="text-[var(--color-muted)] leading-relaxed whitespace-pre-line mb-8">
                        {localizedField(item, "description", language)}
                    </p>
                    <p className="text-2xl font-bold">
                        {Number(item.price).toLocaleString()} RSD
                    </p>
                </div>
            </motion.article>
            <section className="mt-16 border-t border-[var(--color-border)] pt-10">
                <div className="mb-8 text-center">
                    <h2 className="text-2xl font-bold mb-3">
                        {t("menu.reviews")}
                    </h2>
                    {!reviewsLoading && (
                        <div className="flex items-center justify-center gap-3 text-[var(--color-muted)]">
                            <span className="text-xl font-semibold text-[var(--color-text)]">
                                ★ {averageRating.toFixed(1)} / 5
                            </span>
                            <span>({reviewsCount})</span>
                        </div>
                    )}
                </div>
                <div
                    className={`relative mx-auto mb-8 w-full max-w-3xl ${
                        !user
                            ? "flex min-h-40 items-center justify-center rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6"
                            : ""
                    }`}
                >
                    {authLoading || (user && myReviewLoading) ? (
                        <div className="mx-auto mb-10 w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center sm:p-6">
                            <p className="text-[var(--color-muted)]">
                                {t("menu.loadingDetails")}
                            </p>
                        </div>
                    ) : !user ? (
                        <div className="w-full text-center">
                            <p className="text-[var(--color-muted)]">
                                <Link
                                    to="/login"
                                    className="
                                        font-semibold
                                        text-[var(--color-text)]
                                        hover:text-[var(--color-secondary)]
                                    "
                                >
                                    {t("menu.loginLink")}
                                </Link>{" "}
                                {t("menu.loginToReview")}
                            </p>
                        </div>
                    ) : (
                        <>
                            <div className="relative mx-auto mb-8 w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6">
                                <h3 className="text-xl font-semibold mb-4">
                                    {t("menu.rating")}
                                </h3>
                                <div
                                    className="flex justify-center gap-2"
                                    role="group"
                                    aria-label={t("menu.rating")}
                                >
                                    {[1, 2, 3, 4, 5].map((value) => (
                                        <button
                                            key={value}
                                            type="button"
                                            onClick={() =>
                                                handleRatingSelect(value)
                                            }
                                            disabled={
                                                reviewSubmitting || reviewDeleting
                                            }
                                            aria-label={`${value} / 5`}
                                            aria-pressed={rating === value}
                                            className="text-3xl text-amber-500 transition-transform hover:scale-110 disabled:cursor-wait disabled:opacity-60 cursor-pointer"
                                        >
                                            {value <= rating ? "★" : "☆"}
                                        </button>
                                    ))}
                                </div>
                                {myReview && (
                                    <button
                                        type="button"
                                        onClick={handleDeleteRating}
                                        disabled={
                                            reviewDeleting || reviewSubmitting
                                        }
                                        className="absolute right-5 top-5 sm:right-6 sm:top-6 flex h-10 w-10 items-center justify-center rounded-xl border border-red-600 bg-red-600 text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
                                    >
                                        <Trash2 size={18} aria-hidden="true" />
                                    </button>
                                )}
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
                            <div className="mx-auto w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6">
                                <h3 className="text-xl font-semibold mb-4">
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
                                                commentSubmitting || !comment.trim()
                                            }
                                            aria-label={t("menu.submitComment")}
                                            title={t("menu.submitComment")}
                                            className="flex h-10 w-20 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
                                        >
                                            {commentSubmitting ? (
                                                <span
                                                    className="text-sm"
                                                    aria-hidden="true"
                                                >
                                                    …
                                                </span>
                                            ) : (
                                                <Send
                                                    size={22}
                                                    aria-hidden="true"
                                                />
                                            )}
                                        </button>
                                    </div>
                                    {commentMessage && (
                                        <p
                                            className="text-sm text-green-600"
                                            role="status"
                                        >
                                            {commentMessage}
                                        </p>
                                    )}
                                    {commentError && (
                                        <p
                                            className="text-sm text-red-600"
                                            role="alert"
                                        >
                                            {commentError}
                                        </p>
                                    )}
                                </form>
                            </div>
                        </>
                    )}
                </div>
                <h2 className="text-2xl text-center font-bold mb-3">
                    {t("menu.comments")}
                </h2>
                <div className="flex flex-col items-center gap-5">
                    {commentsLoading ? (
                        <p className="text-[var(--color-muted)]">
                            {t("menu.loadingDetails")}
                        </p>
                    ) : comments.length === 0 ? (
                        <p className="text-center text-[var(--color-muted)]">
                            {t("menu.noComments")}
                        </p>
                    ) : (
                        comments.map((commentItem) => (
                            <article
                                key={commentItem.id}
                                className="w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5"
                            >
                                <div className="mb-3 flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p className="font-semibold">
                                            {commentItem.user?.full_name ||
                                                t("menu.unknownUser")}
                                        </p>
                                        {commentItem.created_at && (
                                            <p className="mt-1 text-sm text-[var(--color-muted)]">
                                                {new Date(
                                                    commentItem.created_at,
                                                ).toLocaleDateString(dateLocale)}
                                            </p>
                                        )}
                                    </div>
                                    {user &&
                                        !commentItem.is_admin &&
                                        commentItem.user_id === user.id && (
                                            <div className="flex gap-2">
                                                {!commentItem.has_admin_reply && (
                                                    <button
                                                        type="button"
                                                        onClick={() => startEditingComment(commentItem)}
                                                        disabled={
                                                            commentItem.replies?.some((reply) => reply.is_admin) ||
                                                            commentDeleting ||
                                                            commentUpdating ||
                                                            (editingCommentId !== null &&
                                                                editingCommentId !== commentItem.id)
                                                        }
                                                        aria-label={t("menu.editComment")}
                                                        title={
                                                            commentItem.replies?.some((reply) => reply.is_admin)
                                                                ? t("menu.commentEditNotAllowed")
                                                                : t("menu.editComment")
                                                        }
                                                        className="flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-button-bg)] text-white transition-colors hover:bg-[var(--color-button-hover)] disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
                                                    >
                                                        <Pencil size={18} aria-hidden="true" />
                                                    </button>
                                                )}
                                                <button
                                                    type="button"
                                                    onClick={() => handleDeleteComment(commentItem.id)}
                                                    disabled={commentDeleting || commentUpdating}
                                                    aria-label={t("menu.deleteReviewComment")}
                                                    title={t("menu.deleteReviewComment")}
                                                    className="flex h-10 w-10 items-center justify-center rounded-xl border border-red-600 bg-red-600 text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
                                                >
                                                    <Trash2 size={18} aria-hidden="true" />
                                                </button>
                                            </div>
                                        )}
                                </div>
                                {editingCommentId === commentItem.id ? (
                                    <div className="mt-3 space-y-3">
                                        <textarea
                                            value={editingCommentBody}
                                            onChange={(event) =>
                                                setEditingCommentBody(
                                                    event.target.value,
                                                )
                                            }
                                            maxLength={2000}
                                            rows={4}
                                            className="theme-input w-full resize-none"
                                            aria-label={t("menu.editComment")}
                                        />
                                        <div className="flex flex-wrap justify-center min-[421px]:justify-end gap-3">
                                            <button
                                                type="button"
                                                onClick={cancelEditingComment}
                                                disabled={commentUpdating}
                                                className="theme-button w-25 rounded-xl px-3 py-2 font-semibold disabled:opacity-50 cursor-pointer"
                                            >
                                                {t("menu.cancelEdit")}
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    handleUpdateComment(
                                                        commentItem.id,
                                                    )
                                                }
                                                disabled={commentUpdating}
                                                className="theme-button w-25 rounded-xl px-3 py-2 font-semibold disabled:opacity-50 cursor-pointer"
                                            >
                                                {commentUpdating
                                                    ? t("menu.loadingDetails")
                                                    : t("menu.saveEdit")}
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    <p className="whitespace-pre-line leading-relaxed text-[var(--color-muted)]">
                                        {commentItem.body}
                                    </p>
                                )}
                                {commentItem.replies?.length > 0 && (
                                    <div className="mt-5 flex flex-col gap-3 border-l-2 border-[var(--color-border)] pl-4">
                                        {commentItem.replies.map((reply) => (
                                            <div
                                                key={reply.id}
                                                className="rounded-xl border border-[var(--color-border)] bg-[var(--color-hover-bg)] p-4"
                                            >
                                                <div className="mb-2 flex flex-wrap items-start justify-between gap-3">
                                                    <div>
                                                        <p className="font-semibold">
                                                            {reply.is_admin
                                                                ? "Csárda"
                                                                : reply.user
                                                                      ?.full_name ||
                                                                  t(
                                                                      "menu.unknownUser",
                                                                  )}
                                                        </p>
                                                        {reply.created_at && (
                                                            <p className="mt-1 text-sm text-[var(--color-muted)]">
                                                                {new Date(
                                                                    reply.created_at,
                                                                ).toLocaleDateString(
                                                                    dateLocale,
                                                                )}
                                                            </p>
                                                        )}
                                                    </div>
                                                    {user &&
                                                        !reply.is_admin &&
                                                        reply.user_id ===
                                                            user.id && (
                                                            <div className="flex flex-wrap gap-2">
                                                                {!commentItem.has_admin_reply && (
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            startEditingComment(
                                                                                reply,
                                                                            )
                                                                        }
                                                                        disabled={
                                                                            commentDeleting ||
                                                                            commentUpdating ||
                                                                            (
                                                                                editingCommentId !==
                                                                                    null &&
                                                                                editingCommentId !==
                                                                                    reply.id
                                                                            )
                                                                        }
                                                                        className="w-40 rounded-xl border border-[var(--color-border)] px-3 py-2 font-semibold transition-colors hover:bg-[var(--color-surface)] disabled:cursor-not-allowed disabled:opacity-50"
                                                                    >
                                                                        {t(
                                                                            "menu.editComment",
                                                                        )}
                                                                    </button>
                                                                )}
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        handleDeleteComment(
                                                                            reply.id,
                                                                        )
                                                                    }
                                                                    disabled={
                                                                        commentDeleting ||
                                                                        commentUpdating
                                                                    }
                                                                    className="w-40 rounded-xl border border-red-600 bg-red-600 px-3 py-2 font-semibold text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                                >
                                                                    {commentDeleting
                                                                        ? t(
                                                                              "menu.loadingDetails",
                                                                          )
                                                                        : t(
                                                                              "menu.deleteReviewComment",
                                                                          )}
                                                                </button>
                                                            </div>
                                                        )}
                                                </div>
                                                {editingCommentId ===
                                                reply.id ? (
                                                    <div className="mt-3 space-y-3">
                                                        <textarea
                                                            value={
                                                                editingCommentBody
                                                            }
                                                            onChange={(event) =>
                                                                setEditingCommentBody(
                                                                    event.target
                                                                        .value,
                                                                )
                                                            }
                                                            maxLength={2000}
                                                            rows={4}
                                                            className="theme-input w-full resize-none"
                                                            aria-label={t(
                                                                "menu.editComment",
                                                            )}
                                                        />
                                                        <div className="flex flex-wrap justify-end gap-3">
                                                            <button
                                                                type="button"
                                                                onClick={cancelEditingComment}
                                                                disabled={commentUpdating}
                                                                className="theme-button w-40 rounded-xl px-3 py-2 font-semibold disabled:opacity-50"
                                                            >
                                                                {t("menu.cancelEdit")}
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    handleUpdateComment(
                                                                        reply.id,
                                                                    )
                                                                }
                                                                disabled={commentUpdating}
                                                                className="theme-button w-40 rounded-xl px-3 py-2 font-semibold disabled:opacity-50"
                                                            >
                                                                {commentUpdating
                                                                    ? t("menu.loadingDetails")
                                                                    : t("menu.saveEdit")}
                                                            </button>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <p className="whitespace-pre-line leading-relaxed text-[var(--color-muted)]">
                                                        {reply.body}
                                                    </p>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </article>
                        ))
                    )}
                </div>
            </section>
        </section>
    );
}
