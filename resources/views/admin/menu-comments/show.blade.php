@extends('admin.layouts.app')
@section('title', __('messages.menu_comments'))
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
                    @include('admin.menu-comments.partials.comment', [
                        'comment' => $comment,
                        'dateFormat' => $dateFormat,
                    ])
                </article>
            @empty
                <p>{{ __('messages.no_menu_comments') }}</p>
            @endforelse
        </div>
        {{ $comments->links() }}
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.review-delete-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!confirm(@json(__('messages.confirm_delete_comment')))) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
