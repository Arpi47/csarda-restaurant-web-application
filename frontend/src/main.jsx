import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App.jsx";
import "./index.css";
import { BrowserRouter } from "react-router-dom";
import { LanguageProvider } from "./contexts/LanguageContext";
import { ThemeProvider } from "./contexts/ThemeContext";
import RecaptchaProvider from "./providers/RecaptchaProvider";

ReactDOM.createRoot(document.getElementById("root")).render(
    <React.StrictMode>
        <RecaptchaProvider>
            <BrowserRouter>
                <ThemeProvider>
                    <LanguageProvider>
                        <App />
                    </LanguageProvider>
                </ThemeProvider>
            </BrowserRouter>
        </RecaptchaProvider>
    </React.StrictMode>,
);
