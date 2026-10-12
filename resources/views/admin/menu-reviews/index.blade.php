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
    @endphp
    <div class="admin-review-page">
        <h1>{{ __('messages.menu_reviews') }}</h1>
        <p>{{ __('messages.menu_reviews_description') }}</p>
        @if (session('success'))
            <p class="review-success" role="status">
                {{ session('success') }}
            </p>
        @endif
        <div class="review-list">
            @forelse ($reviews as $review)
                <article class="review-card">
                    <div class="review-card-header">
                        <div>
                            <h2>
                                {{ $review->menu?->{'name_' . $localeField} ?? __('messages.unknown_menu_item') }}
                            </h2>
                            <p>
                                {{ __('messages.review_author') }}:
                                {{ $review->user
                                    ? trim($review->user->first_name . ' ' . $review->user->last_name)
                                    : __('messages.unknown_user') }}
                            </p>
                            <p>
                                {{ __('messages.review_date') }}:
                                {{ $review->created_at->locale(app()->getLocale())->translatedFormat(
                                    match (app()->getLocale()) {
                                        'hu' => 'Y. m. d. H:i',
                                        'en' => 'M j, Y, H:i',
                                        'sr', 'sr_cyrl' => 'd. M Y. H:i',
                                        default => 'Y-m-d H:i',
                                    },
                                ) }}
                            </p>
                        </div>
                        <div class="review-rating" aria-label="{{ $review->rating }} / 5">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            <span>{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    <div class="review-comment">
                        {{ $review->comment ?: __('messages.no_review_comment') }}
                    </div>
                    <p class="review-status">
                        {{ __('messages.review_status') }}:
                        <strong>
                            {{ $review->is_approved ? __('messages.review_approved') : __('messages.review_pending') }}
                        </strong>
                    </p>
                    <div class="review-actions">
                        @if (!$review->is_approved)
                            <form method="POST" action="{{ route('admin.menu-reviews.approve', $review) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="review-approve-button">
                                    {{ __('messages.approve_review') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.menu-reviews.destroy', $review) }}"
                                class="review-reject-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="review-reject-button">
                                    {{ __('messages.reject_review') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.menu-reviews.reject', $review) }}"
                                class="review-reject-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="review-reject-button">
                                    {{ __('messages.delete_review') }}
                                </button>
                            </form>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.menu-reviews.reply', $review) }}"
                        class="review-reply-form">
                        @csrf
                        @method('PATCH')
                        <label for="admin-reply-{{ $review->id }}">
                            {{ __('messages.admin_reply_label') }}
                        </label>
                        <textarea id="admin-reply-{{ $review->id }}" name="admin_reply" rows="3" minlength="5" maxlength="2000"
                            required>{{ old('admin_reply', $review->admin_reply) }}</textarea>
                        <button type="submit" class="review-approve-button">
                            {{ __('messages.save_admin_reply') }}
                        </button>
                    </form>
                    @if ($review->admin_reply)
                        <p class="review-existing-reply">
                            <strong>{{ __('messages.current_admin_reply') }}:</strong>
                            {{ $review->admin_reply }}
                        </p>
                    @endif
                </article>
            @empty
                <p>{{ __('messages.no_menu_reviews') }}</p>
            @endforelse
        </div>
        {{ $reviews->links() }}
    </div>
    <style>
        .admin-review-page .review-list {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .admin-review-page .review-card {
            padding: 1.25rem;
            border: 1px solid var(--admin-border, #ccc);
            border-radius: 10px;
            background: var(--admin-card-bg, transparent);
        }

        .admin-review-page .review-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-review-page .review-card h2 {
            margin-top: 0;
        }

        .admin-review-page .review-rating {
            font-size: 1.15rem;
            color: #c58a25;
            white-space: nowrap;
        }

        .admin-review-page .review-rating span {
            color: inherit;
            font-size: 0.9rem;
        }

        .admin-review-page .review-comment {
            margin: 1rem 0;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        .admin-review-page .review-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .admin-review-page .review-approve-button,
        .admin-review-page .review-reject-button {
            padding: 0.65rem 1rem;
            border: 0;
            border-radius: 6px;
            color: white;
            cursor: pointer;
        }

        .admin-review-page .review-approve-button {
            background: #287a45;
        }

        .admin-review-page .review-reject-button {
            background: #b83232;
        }

        .admin-review-page .review-success {
            padding: 0.75rem 1rem;
            border: 1px solid #287a45;
            border-radius: 6px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.review-reject-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!confirm(@json(__('messages.confirm_review_rejection')))) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
