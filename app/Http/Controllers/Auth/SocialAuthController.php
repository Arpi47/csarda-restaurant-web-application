<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        return $this->handleCallback('google');
    }

    public function redirectToGoogleLink(Request $request)
    {
        $token = $request->query('token');

        session([
            'google_link_token' => $token,
        ]);

        return Socialite::driver('google')
            ->redirectUrl(
                env('GOOGLE_LINK_REDIRECT_URI')
            )
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function handleGoogleLinkCallback()
    {
        try {

            $token = session('google_link_token');

            if (! $token) {
                $token = request()->query('token');
            }
            if (! $token) {
                return $this->redirectProfileError(
                    'not_authenticated'
                );
            }
            $accessToken =
                \Laravel\Sanctum\PersonalAccessToken::findToken(
                    $token
                );

            if (! $accessToken) {
                return $this->redirectProfileError(
                    'not_authenticated'
                );
            }
            $user = $accessToken->tokenable;

            $socialUser =
                Socialite::driver('google')
                    ->redirectUrl(
                        env('GOOGLE_LINK_REDIRECT_URI')
                    )
                    ->user();

            $email = $socialUser->getEmail();

            if (! $email) {
                return $this->redirectProfileError(
                    'oauth_email_missing'
                );
            }
            if (
                isset($socialUser->user['email_verified']) &&
                ! $socialUser->user['email_verified']
            ) {
                return $this->redirectProfileError(
                    'oauth_email_not_verified'
                );
            }

            $existingSocialAccount =
                SocialAccount::where(
                    'provider',
                    'google'
                )
                    ->where(
                        'provider_id',
                        $socialUser->getId()
                    )
                    ->first();

            if ($existingSocialAccount) {
                if (
                    $existingSocialAccount->user_id ===
                    $user->id
                ) {
                    return $this->redirectProfileSuccess(
                        'google_already_connected'
                    );
                }

                return $this->redirectProfileError(
                    'google_already_connected_to_other_account'
                );
            }

            if (
                strtolower($email) !==
                strtolower($user->email)
            ) {
                return $this->redirectProfileError(
                    'google_email_mismatch'
                );
            }

            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_id' => $socialUser->getId(),
            ]);

            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }

            return $this->redirectProfileSuccess(
                'google_connected'
            );
        } catch (Throwable $e) {

            Log::error(
                'Google account linking failed',
                [
                    'user_id' => Auth::id(),
                    'message' => $e->getMessage(),
                ]
            );

            return $this->redirectProfileError(
                'oauth_failed'
            );
        }
    }

    private function handleCallback(string $provider)
    {
        try {
            $socialUser =
                Socialite::driver($provider)
                    ->user();

            $email = $socialUser->getEmail();

            if (! $email) {

                return $this->redirectError(
                    'oauth_email_missing'
                );
            }

            if (
                $provider === 'google' &&
                isset($socialUser->user['email_verified']) &&
                ! $socialUser->user['email_verified']
            ) {
                return $this->redirectError(
                    'oauth_email_not_verified'
                );
            }

            $socialAccount =
                SocialAccount::where(
                    'provider',
                    $provider
                )
                    ->where(
                        'provider_id',
                        $socialUser->getId()
                    )
                    ->first();

            if ($socialAccount) {

                $user = $socialAccount->user;

                if ($user->is_suspended) {
                    return $this->redirectError(
                        'account_suspended'
                    );
                }
            } else {
                $user =
                    User::where(
                        'email',
                        $email
                    )->first();

                if ($user) {
                    return $this->redirectError(
                        'email_already_registered'
                    );
                }

                $user =
                    User::create([
                        'email' => $email,
                        'first_name' => $this->getFirstName(
                            $socialUser
                        ),
                        'last_name' => $this->getLastName(
                            $socialUser
                        ),
                        'profile_image' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password' => null,
                    ]);

                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            }

            $token =
                $user
                    ->createToken(
                        $provider.'-login'
                    )
                    ->plainTextToken;

            return redirect(

                env('FRONTEND_URL')
                .
                '/oauth/callback?token='
                .
                urlencode($token)

            );
        } catch (Throwable $e) {

            Log::error(
                'OAuth login failed',
                [
                    'provider' => $provider,
                    'message' => $e->getMessage(),
                ]
            );

            return $this->redirectError(
                'oauth_failed'
            );
        }
    }

    private function redirectProfileSuccess(
        string $message
    ) {

        return redirect(

            env('FRONTEND_URL')
            .
            '/profile?oauth='
            .
            urlencode($message)

        );
    }

    private function redirectProfileError(
        string $error
    ) {

        return redirect(

            env('FRONTEND_URL')
            .
            '/profile?oauth_error='
            .
            urlencode($error)

        );
    }

    private function redirectError(
        string $error
    ) {

        return redirect(

            env('FRONTEND_URL')
            .
            '/login?error='
            .
            urlencode($error)

        );
    }

    private function getFirstName(
        $socialUser
    ): string {

        if (
            isset(
                $socialUser->user['given_name']
            )
        ) {
            return
                $socialUser
                    ->user['given_name'];
        }

        $name = $socialUser->getName();

        if (! $name) {
            return '';
        }

        $parts =
            explode(
                ' ',
                $name
            );

        return $parts[0] ?? '';
    }

    private function getLastName(
        $socialUser
    ): string {

        if (
            isset(
                $socialUser->user['family_name']
            )
        ) {
            return
                $socialUser
                    ->user['family_name'];
        }

        $name = $socialUser->getName();

        if (! $name) {
            return '';
        }

        $parts =
            explode(
                ' ',
                $name
            );

        if (
            count($parts) <= 1
        ) {
            return '';
        }

        array_shift(
            $parts
        );

        return
            implode(
                ' ',
                $parts
            );
    }
}
