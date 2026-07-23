<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $recaptcha = $request->input('g-recaptcha-response');

        if (! $recaptcha) {
            return response()->json([
                'message' => 'reCAPTCHA is required.',
            ], 422);
        }
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('app.recaptcha_secret_key'),
                'response' => $recaptcha,
                'remoteip' => $request->ip(),
            ]
        );
        $result = $response->json();
        if (
            ! ($result['success'] ?? false) ||
            ($result['score'] ?? 0) < 0.5
        ) {
            return response()->json([
                'message' => 'Invalid reCAPTCHA.',
            ], 422);
        }
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        Mail::to('info@csarda.com')
            ->send(
                new ContactMessageMail($validated)
            );

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
        ]);
    }
}
