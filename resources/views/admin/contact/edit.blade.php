@extends('admin.layouts.app')
@section('title', __('messages.edit_social_link'))
@section('content')
    <h1>{{ __('messages.edit_social_link') }}</h1>
    <form method="POST" action="{{ route('admin.contact.social.update', $contactSetting) }}" class="admin-multilingual-form">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="platform">
                {{ __('messages.social_platform') }}:
            </label>
            <select id="platform" name="platform" required>
                @php
                    $platforms = [
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'tiktok' => 'TikTok',
                        'youtube' => 'YouTube',
                        'x' => 'X (Twitter)',
                        'linkedin' => 'LinkedIn',
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                        'snapchat' => 'Snapchat',
                        'pinterest' => 'Pinterest',
                        'reddit' => 'Reddit',
                        'threads' => 'Threads',
                        'discord' => 'Discord',
                        'twitch' => 'Twitch',
                        'vk' => 'VK',
                        'wechat' => 'WeChat',
                        'messenger' => 'Messenger',
                    ];
                @endphp
                @foreach ($platforms as $value => $label)
                    <option value="{{ $value }}"
                        {{ old('platform', $contactSetting->platform) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('platform')
                <span class="text-danger">
                    ⚠ {{ $message }}
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="url">
                {{ __('messages.social_url') }}:
            </label>
            <input type="url" id="url" name="url" value="{{ old('url', $contactSetting->url) }}" required>
            @error('url')
                <span class="text-danger">
                    ⚠ {{ $message }}
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $contactSetting->is_active) ? 'checked' : '' }}>
                {{ __('messages.social_active') }}
            </label>
        </div>
        <button type="submit">
            {{ __('messages.save') }}
        </button>
    </form>
@endsection
