import { useEffect } from "react";

export default function useDropdown(ref, close) {
    useEffect(() => {
        function handleClick(event) {
            if (
                ref.current &&
                !ref.current.contains(event.target)
            ) {
                close();
            }
        }
        function handleKey(event) {
            if(event.key === "Escape") {
                close();
            }
        }
        document.addEventListener(
            "mousedown",
            handleClick
        );
        document.addEventListener(
            "keydown",
            handleKey
        );
        return () => {
            document.removeEventListener(
                "mousedown",
                handleClick
            );
            document.removeEventListener(
                "keydown",
                handleKey
            );
        };
    }, [ref, close]);
}