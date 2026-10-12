import { useEffect, useRef, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import {
    getMenuItem,
    getMenuReviews,
    getMyMenuReview,
    submitMenuReview,
    updateMenuReview,
    deleteMenuReviewComment,
} from "../api/menu";
import { useAuth } from "../contexts/AuthContext";
import { useLanguage } from "../contexts/LanguageContext";
import { localizedField } from "../utils/localization";
import PageHeader from "../components/common/PageHeader";

export default function MenuDetail() {
    const { id } = useParams();
    const { language, t } = useLanguage();
    const { user, loading: authLoading } = useAuth();
    const [item, setItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);
    const [reviews, setReviews] = useState([]);
    const [averageRating, setAverageRating] = useState(0);
    const [reviewsCount, setReviewsCount] = useState(0);
    const [reviewsLoading, setReviewsLoading] = useState(true);
    const [myReview, setMyReview] = useState(null);
    const [myReviewLoading, setMyReviewLoading] = useState(false);
    const [commentDeleting, setCommentDeleting] = useState(false);
    const [reviewSubmitting, setReviewSubmitting] = useState(false);
    const [[selectedIndex, direction], setSelected] = useState([0, 0]);
    const [rating, setRating] = useState(5);
    const [comment, setComment] = useState("");
    const [reviewMessage, setReviewMessage] = useState("");
    const [reviewError, setReviewError] = useState("");
    const thumbnailRefs = useRef([]);
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;

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
                    setReviews(data.reviews ?? []);
                    setAverageRating(
                        Number(data.average_rating ?? 0),
                    );
                    setReviewsCount(
                        Number(data.reviews_count ?? 0),
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
        async function loadMyReview() {
            if (authLoading) {
                return;
            }
            if (!user) {
                setMyReview(null);
                setRating(5);
                setComment("");
                setMyReviewLoading(false);
                return;
            }
            setMyReviewLoading(true);
            try {
                const data = await getMyMenuReview(id);
                if (!cancelled) {
                    const review = data.review ?? null;
                    setMyReview(review);
                    setRating(review?.rating ?? 5);
                    setComment(review?.comment ?? "");
                }
            } catch {
                if (!cancelled) {
                    setMyReview(null);
                    setRating(5);
                    setComment("");
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
            setReviewsCount(Number(data.reviews_count ?? 0));
        } catch {
        }
    }
    async function handleReviewSubmit(event) {
        event.preventDefault();
        const trimmedComment = comment.trim();
        if (
            trimmedComment.length > 0 &&
            trimmedComment.length < 5
        ) {
            setReviewError(t("menu.reviewCommentMinLength"));
            setReviewMessage("");
            return;
        }
        setReviewSubmitting(true);
        setReviewMessage("");
        setReviewError("");
        try {
            const reviewData = {
                rating,
                comment: trimmedComment || null,
            };
            const response = myReview
                ? await updateMenuReview(id, reviewData)
                : await submitMenuReview(id, reviewData);
            setMyReview(response.review);
            setRating(response.review.rating);
            setComment(response.review.comment ?? "");
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
    async function handleDeleteComment() {
        if (!myReview?.comment || commentDeleting || reviewSubmitting) {
            return;
        }
        const confirmed = window.confirm(
            t("menu.confirmDeleteReviewComment"),
        );
        if (!confirmed) {
            return;
        }
        setCommentDeleting(true);
        setReviewMessage("");
        setReviewError("");
        try {
            const response = await deleteMenuReviewComment(id);
            setMyReview((previous) =>
                previous
                    ? {
                          ...previous,
                          ...(response.review ?? {}),
                          comment: null,
                          admin_reply: null,
                      }
                    : previous,
            );
            setComment("");
            setReviewMessage(t("menu.reviewCommentDeleted"));
            await refreshReviews();
        } catch (error) {
            if (error.response?.status === 401) {
                setReviewError(t("menu.loginRequired"));
            } else {
                setReviewError(t("menu.reviewCommentDeleteError"));
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
            thumbnailRefs.current[
                selectedIndex - 1
            ].scrollIntoView({
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
                                alt={`${localizedField(item, "name", language)}${images.length > 1 ? ` (${selectedIndex + 1}/${images.length})` : ""}`}
                                onError={(event) => {
                                    event.currentTarget.onerror = null;
                                    event.currentTarget.src =
                                        "/placeholder.jpg";
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
                            {localizedField(
                                item.category,
                                "name",
                                language,
                            )}
                        </span>
                    )}
                    <p className="text-[var(--color-muted)] leading-relaxed whitespace-pre-line mb-8">
                        {localizedField(
                            item,
                            "description",
                            language,
                        )}
                    </p>
                    <p className="text-2xl font-bold">
                        {Number(item.price).toLocaleString()} RSD
                    </p>
                </div>
            </motion.article>
            <section className="mt-16 border-t border-[var(--color-border)] pt-10">
                <div className="mb-8">
                    <h2 className="text-2xl font-bold mb-3">
                        {t("menu.reviews")}
                    </h2>
                    {!reviewsLoading && (
                        <div className="flex flex-wrap items-center gap-3 text-[var(--color-muted)]">
                            <span className="text-xl font-semibold text-[var(--color-text)]">
                                ★ {averageRating.toFixed(1)} / 5
                            </span>

                            <span>({reviewsCount})</span>
                        </div>
                    )}
                </div>
                <div className="mb-10 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 sm:p-6">
                    <h3 className="text-xl font-semibold mb-4">
                        {t("menu.writeReview")}
                    </h3>

                    {authLoading || myReviewLoading ? (
                        <p className="text-[var(--color-muted)]">
                            {t("menu.loadingDetails")}
                        </p>
                    ) : !user ? (
                        <p className="text-[var(--color-muted)]">
                            {t("menu.loginToReview")}
                        </p>
                    ) : (
                        <form
                            onSubmit={handleReviewSubmit}
                            className="space-y-5"
                        >
                            <div>
                                <p className="font-medium mb-2">
                                    {t("menu.rating")}
                                </p>
                                <div className="flex gap-2">
                                    {[1, 2, 3, 4, 5].map((value) => (
                                        <button
                                            key={value}
                                            type="button"
                                            onClick={() => setRating(value)}
                                            aria-label={`${value} / 5`}
                                            aria-pressed={rating === value}
                                            className="text-3xl text-amber-500 transition-transform hover:scale-110"
                                        >
                                            {value <= rating ? "★" : "☆"}
                                        </button>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <label
                                    htmlFor="review-comment"
                                    className="block font-medium mb-2"
                                >
                                    {t("menu.commentPlaceholder")}
                                </label>
                                <textarea
                                    id="review-comment"
                                    value={comment}
                                    onChange={(event) =>
                                        setComment(event.target.value)
                                    }
                                    maxLength={2000}
                                    rows={4}
                                    placeholder={t("menu.commentPlaceholder")}
                                    className="theme-input resize-none"
                                />
                                {reviewMessage && (
                                    <p
                                        className="text-sm text-green-600"
                                        role="status"
                                    >
                                        {reviewMessage}
                                    </p>
                                )}
                                {reviewError && (
                                    <p
                                        className="text-sm text-red-600"
                                        role="alert"
                                    >
                                        {reviewError}
                                    </p>
                                )}
                                <div className="flex justify-end gap-3 mt-3">
                                    {myReview?.comment && (
                                        <button
                                            type="button"
                                            onClick={handleDeleteComment}
                                            disabled={
                                                commentDeleting ||
                                                reviewSubmitting
                                            }
                                            className="w-40 rounded-xl border border-red-600 bg-red-600 px-3 py-2 font-semibold cursor-pointer text-white transition-colors hover:border-red-700 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            {commentDeleting
                                                ? t("menu.loadingDetails")
                                                : t("menu.deleteReviewComment")}
                                        </button>
                                    )}
                                    <button
                                        type="submit"
                                        disabled={
                                            reviewSubmitting || commentDeleting
                                        }
                                        className="w-40 theme-button rounded-xl px-3 py-2 font-semibold cursor-pointer transition-colors duration-200 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {reviewSubmitting
                                            ? t("menu.loadingDetails")
                                            : t("menu.submitReview")}
                                    </button>
                                </div>
                            </div>
                        </form>
                    )}
                </div>
                {reviewsLoading ? (
                    <p className="text-[var(--color-muted)]">
                        {t("menu.loadingDetails")}
                    </p>
                ) : reviews.length === 0 ? (
                    <p className="text-[var(--color-muted)]">
                        {t("menu.noReviews")}
                    </p>
                ) : (
                    <div className="flex flex-col items-center gap-5">
                        {reviews.map((review) => (
                            <article
                                key={review.id}
                                className="w-full max-w-3xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5"
                            >
                            <div className="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div>
                                    <p className="font-semibold">
                                        {review.user?.full_name || t("menu.unknownUser")}
                                    </p>
                                    {review.created_at && (
                                        <p className="text-sm text-[var(--color-muted)] mt-1">
                                            {new Date(review.created_at).toLocaleDateString(
                                                {
                                                    en: "en-US",
                                                    hu: "hu-HU",
                                                    sr_lat: "sr-Latn-RS",
                                                    sr_cyr: "sr-Cyrl-RS",
                                                }[language] || "en-US",
                                            )}
                                        </p>
                                    )}
                                </div>
                                <div
                                    className="text-amber-500"
                                    aria-label={`${review.rating} / 5`}
                                >
                                    <span aria-hidden="true">
                                        {"★".repeat(review.rating)}
                                        {"☆".repeat(5 - review.rating)}
                                    </span>
                                    <span className="ml-2 text-sm text-[var(--color-muted)]">
                                        {review.rating}/5
                                    </span>
                                </div>
                            </div>
                            {review.comment && (
                                <p className="text-[var(--color-muted)] leading-relaxed whitespace-pre-line">
                                    {review.comment}
                                </p>
                            )}
                            {review.admin_reply && (
                                <div className="mt-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-hover-bg)] p-4">
                                    <p className="font-semibold mb-2">Csárda</p>
                                    <p className="text-[var(--color-muted)] leading-relaxed whitespace-pre-line">
                                        {review.admin_reply}
                                    </p>
                                </div>
                            )}
                            </article>
                        ))}
                    </div>
                )}
            </section>
        </section>
    );
}
