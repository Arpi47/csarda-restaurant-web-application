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
            @forelse ($reviews as $review)
                <article class="review-thread">
                    <div class="review-message">
                        <div class="review-message-header">
                            <strong>
                                {{ $review->user
                                    ? trim($review->user->first_name . ' ' . $review->user->last_name)
                                    : __('messages.unknown_user') }}
                            </strong>
                            <time>
                                {{ $review->created_at->locale(app()->getLocale())->translatedFormat($dateFormat) }}
                            </time>
                        </div>
                        <p>
                            {{ __('messages.rating') }}:
                            <strong>{{ $review->rating }} / 5</strong>
                        </p>
                        @if ($review->comment)
                            <div class="review-message-body">{{ $review->comment }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.menu-reviews.destroy', $review) }}"
                            class="review-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="review-delete-button">
                                {{ __('messages.delete_review') }}
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p>{{ __('messages.no_menu_reviews') }}</p>
            @endforelse
        </div>
        {{ $reviews->links() }}
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.review-delete-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!confirm(@json(__('messages.confirm_delete_review')))) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
