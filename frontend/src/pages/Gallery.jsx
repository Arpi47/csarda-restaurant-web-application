import { motion, AnimatePresence } from "framer-motion";
import { useLanguage } from "../contexts/LanguageContext";
import { getGallery } from "../api/gallery";
import { useEffect, useRef, useState } from "react";
import PageHeader from "../components/common/PageHeader";

export default function Gallery() {
    const { t } = useLanguage();
    const thumbnailRefs = useRef([]);
    const [images, setImages] = useState([]);
    const [[selectedIndex, direction], setSelected] = useState([null, 0]);
    const ASSET_URL = import.meta.env.VITE_ASSET_URL;
    const selectedImage =
        selectedIndex !== null
            ? images[selectedIndex]
            : null;
    function nextImage() {
        setSelected([
            (selectedIndex + 1) % images.length,
            1
        ]);
    }
    function previousImage() {
        setSelected([
            (selectedIndex - 1 + images.length) % images.length,
            -1
        ]);
    }
    const imageVariants = {
        enter: (direction) => ({
            x: direction > 0 ? 300 : -300,
            opacity: 0
        }),
        center: {
            x: 0,
            opacity: 1
        },
        exit: (direction) => ({
            x: direction > 0 ? -300 : 300,
            opacity: 0
        })
    };
    useEffect(() => {
        getGallery()
            .then(data => {
                setImages(data);
            })
            .catch(error => {
                console.error(error);
            });
    }, []);
    useEffect(() => {
        function handleKeyDown(e) {
            if (selectedIndex === null) return;
            if (e.key === "Escape") {
                setSelected([
                    null,
                    0
                ]);
            }
            if (e.key === "ArrowRight") {
                nextImage();
            }
            if (e.key === "ArrowLeft") {
                previousImage();
            }
        }
        window.addEventListener(
            "keydown",
            handleKeyDown
        );
        return () =>
            window.removeEventListener(
                "keydown",
                handleKeyDown
            );
    }, [selectedIndex, images]);
    useEffect(() => {
        if(
            selectedIndex !== null &&
            thumbnailRefs.current[selectedIndex]
        ){
            thumbnailRefs.current[selectedIndex]
                .scrollIntoView({
                    behavior:"smooth",
                    inline:"center",
                    block:"nearest"
                });
        }
    }, [selectedIndex]);
    return (
        <div className="page-container">
            <main className="py-12 px-6">
                <div className="max-w-7xl mx-auto">
                    <PageHeader
                        title={t("gallery.title")}
                        subtitle={t("gallery.subtitle")}
                    />
                    <div
                        className="
                            grid
                            grid-cols-1
                            sm:grid-cols-2
                            lg:grid-cols-3
                            gap-8
                        "
                    >
                        {images.map((image, index) => (
                            <motion.div
                                key={image.id}
                                initial={{
                                    opacity: 0,
                                    y: 40
                                }}
                                whileInView={{
                                    opacity: 1,
                                    y: 0
                                }}
                                viewport={{
                                    once: true
                                }}
                                transition={{
                                    delay: index * 0.1
                                }}
                                whileHover={{
                                    scale: 1.04,
                                    y: -5
                                }}
                                onClick={() =>
                                    setSelected([
                                        index,
                                        0
                                    ])
                                }
                                className="
                                    overflow-hidden
                                    rounded-2xl
                                    cursor-pointer
                                    shadow-md
                                "
                            >
                                <img
                                    src={`${ASSET_URL}/images/gallery/${image.image}`}
                                    alt={`Gallery image ${index + 1}`}
                                    className="
                                        w-full
                                        h-80
                                        object-cover
                                        hover:scale-110
                                        transition-transform
                                        duration-500
                                    "
                                />
                            </motion.div>
                        ))}
                    </div>
                </div>
                {selectedImage && (
                    <div
                        onClick={() =>
                            setSelected([
                                null,
                                0
                            ])
                        }
                        className="
                            fixed
                            inset-0
                            bg-black/90
                            backdrop-blur-sm
                            z-50
                            flex
                            items-center
                            justify-center
                            p-6
                        "
                    >
                        <button
                            onClick={(e) => {

                                e.stopPropagation();
                                previousImage();

                            }}
                            className="
                                absolute
                                left-6
                                w-14
                                h-14
                                rounded-full
                                bg-white/15
                                backdrop-blur
                                text-white
                                text-3xl
                                flex
                                items-center
                                justify-center
                                transition
                                hover:bg-white/30
                                cursor-pointer
                            "
                        >
                            ❮
                        </button>
                        {/* Image */}
                        <AnimatePresence
                            mode="wait"
                            initial={false}
                            custom={direction}
                        >
                            <motion.img
                                key={selectedImage.id}
                                onClick={(e) => e.stopPropagation()}
                                src={`${ASSET_URL}/images/gallery/${selectedImage.image}`}
                                alt={`Gallery image ${selectedIndex + 1}`}
                                custom={direction}
                                variants={imageVariants}
                                initial="enter"
                                animate="center"
                                exit="exit"
                                transition={{
                                    duration: 0.35,
                                    ease: "easeInOut"
                                }}
                                drag="x"
                                dragConstraints={{
                                    left: 0,
                                    right: 0
                                }}
                                dragElastic={0.15}
                                onDragEnd={(event, info) => {
                                    if (info.offset.x < -100) {
                                        nextImage();
                                    }
                                    if (info.offset.x > 100) {
                                        previousImage();
                                    }
                                }}
                                className="
                                    max-h-full
                                    max-w-full
                                    rounded-xl
                                    cursor-grab
                                    active:cursor-grabbing
                                "
                                whileDrag={{
                                    scale: 0.96
                                }}
                            />
                        </AnimatePresence>
                        <button
                            onClick={(e) => {
                                e.stopPropagation();
                                nextImage();
                            }}
                            className="
                                absolute
                                right-6
                                w-14
                                h-14
                                rounded-full
                                bg-white/15
                                backdrop-blur
                                text-white
                                text-3xl
                                flex
                                items-center
                                justify-center
                                transition
                                hover:bg-white/30
                                cursor-pointer
                            "
                        >
                            ❯
                        </button>
                        {/* Close */}
                        <button
                            onClick={(e) => {
                                e.stopPropagation();
                                setSelected([
                                    null,
                                    0
                                ])
                            }}
                            className="
                                absolute
                                top-6
                                right-6
                                w-12
                                h-12
                                rounded-full
                                bg-white/15
                                backdrop-blur
                                text-white
                                text-2xl
                                flex
                                items-center
                                justify-center
                                transition
                                hover:bg-red-500
                                hover:rotate-90
                                cursor-pointer
                            "
                        >
                            ✕
                        </button>
                        {/* Counter */}
                        <div
                            className="
                                absolute
                                top-6
                                left-14
                                -translate-x-1/2
                                px-4
                                py-2
                                rounded-full
                                bg-white/15
                                backdrop-blur
                                text-white
                            "
                        >
                            {selectedIndex + 1} / {images.length}
                        </div>
                        {/* Thumbnails */}
                        <div
                            onClick={(e) => e.stopPropagation()}
                            className="
                                group
                                absolute
                                bottom-6
                                left-1/2
                                -translate-x-1/2
                                flex
                                gap-3
                                max-w-[90%]
                                overflow-x-auto
                                px-2
                                py-1
                                rounded-xl
                                bg-black/30
                                backdrop-blur
                                transition-all
                                duration-300
                                ease-out
                                hover:px-4
                                hover:py-3
                            "
                        >
                            {images.map((image, index) => (
                                <button
                                    key={image.id}
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        setSelected([
                                            index,
                                            index > selectedIndex ? 1 : -1
                                        ]);
                                    }}
                                    className={`
                                        flex-shrink-0
                                        rounded-lg
                                        overflow-hidden
                                        border-2
                                        hover:cursor-pointer
                                        transition-all
                                        duration-300
                                        ${
                                            index === selectedIndex
                                            ? "border-white"
                                            : "border-transparent opacity-60 hover:opacity-100"
                                        }
                                        group-hover:w-16
                                        group-hover:h-16
                                        w-10
                                        h-10
                                    `}
                                    ref={el => thumbnailRefs.current[index] = el}
                                >
                                    <img
                                        src={`${ASSET_URL}/images/gallery/${image.image}`}
                                        alt={`Thumbnail ${index + 1}`}
                                        className="
                                            w-full
                                            h-full
                                            object-cover
                                        "
                                    />
                                </button>
                            ))}
                        </div>
                    </div>
                )}
            </main>
        </div>
    );
}