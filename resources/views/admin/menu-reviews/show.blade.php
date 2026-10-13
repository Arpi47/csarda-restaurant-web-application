@extends('admin.layouts.app')
@section('title', __('messages.menu_reviews'))
@section('content')
    @php
        $localeField = match (app()->getLocale()) {
            'en' => 'en',
            'hu' => 'hu',
            'sr' => 'sr_lat',
            'sr_cyrl' => 'sr_cyr',
            default => 'en',
        };
        $dateFormat = match (app()->getLocale()) {
            'hu' => 'Y. m. d. H:i',
            'en' => 'M j, Y, H:i',
            'sr', 'sr_cyrl' => 'd. M Y. H:i',
            default => 'Y-m-d H:i',
        };
    @endphp
    <div class="admin-review-page">
        <h1>{{ $menu->{'name_' . $localeField} }}</h1>
        @if (session('success'))
            <p class="review-success" role="status">
                {{ session('success') }}
            </p>
        @endif
        @if ($errors->any())
            <div class="review-error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="review-list">
            @forelse ($comments as $comment)
                <article class="review-thread">
                    <div class="review-message review-root-message">
                        <div class="review-message-header">
                            <strong>
                                {{ $comment->user
                                    ? trim($comment->user->first_name . ' ' . $comment->user->last_name)
                                    : __('messages.unknown_user') }}
                            </strong>
                            <time>
                                {{ $comment->created_at->locale(app()->getLocale())->translatedFormat($dateFormat) }}
                            </time>
                        </div>
                        <div class="review-message-body">{{ $comment->body }}</div>
                        <form method="POST" action="{{ route('admin.menu-reviews.destroy', $comment) }}"
                            class="review-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="review-delete-button">
                                {{ __('messages.delete_review') }}
                            </button>
                        </form>
                    </div>
                    @foreach ($comment->replies as $reply)
                        <div class="review-message {{ $reply->is_admin ? 'review-admin-message' : 'review-user-message' }}">
                            <div class="review-message-header">
                                <strong>
                                    @if ($reply->is_admin)
                                        Csárda
                                        @if ($reply->user)
                                            — {{ trim($reply->user->first_name . ' ' . $reply->user->last_name) }}
                                        @endif
                                    @else
                                        {{ $reply->user ? trim($reply->user->first_name . ' ' . $reply->user->last_name) : __('messages.unknown_user') }}
                                    @endif
                                </strong>
                                <time>
                                    {{ $reply->created_at->locale(app()->getLocale())->translatedFormat($dateFormat) }}
                                </time>
                            </div>
                            <div class="review-message-body">{{ $reply->body }}</div>
                            <form method="POST" action="{{ route('admin.menu-reviews.destroy', $reply) }}"
                                class="review-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="review-delete-button">
                                    {{ __('messages.delete_review') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('admin.menu-reviews.reply', $comment) }}"
                        class="review-reply-form">
                        @csrf
                        <label for="reply-{{ $comment->id }}">
                            {{ __('messages.admin_reply_label') }}
                        </label>
                        <textarea id="reply-{{ $comment->id }}" name="body" rows="3" minlength="5" maxlength="2000" required>{{ old('body') }}</textarea>
                        <button type="submit" class="admin-add-button">
                            {{ __('messages.save_admin_reply') }}
                        </button>
                    </form>
                </article>
            @empty
                <p>{{ __('messages.no_menu_reviews') }}</p>
            @endforelse
        </div>
        {{ $comments->links() }}
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.review-delete-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!confirm(@json(__('messages.confirm_review_rejection')))) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
