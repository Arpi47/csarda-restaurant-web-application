@extends('layouts.app')
@section('title', __('messages.check_your_email'))
@section('content')
    <div class="container mx-auto max-w-md p-6 bg-white rounded shadow mt-10">
        <h1 class="text-xl font-bold mb-4">{{ __('messages.check_your_email') }}</h1>
        <p>{{ __('messages.registration_email_sent') }}</p>
        <p class="mt-4">{{ __('messages.email_sent_to') }}: <strong>{{ $email }}</strong></p>
        <p class="mt-4">{{ __('messages.check_spam_folder') }}</p>
        <a href="{{ route('login') }}" class="mt-6 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ __('messages.back_to_login') }}
        </a>
    </div>
@endsection
