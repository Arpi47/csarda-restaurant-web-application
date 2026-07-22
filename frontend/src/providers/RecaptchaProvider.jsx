import { GoogleReCaptchaProvider } from "react-google-recaptcha-v3";

export default function RecaptchaProvider({ children }) {
    return (
        <GoogleReCaptchaProvider
            reCaptchaKey={
                import.meta.env.VITE_RECAPTCHA_SITE_KEY
            }
        >
            {children}
        </GoogleReCaptchaProvider>
    );
}