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
        <div class="review-menu-list">
            @forelse ($menus as $menu)
                <article class="review-menu-card">
                    <div class="review-menu-info">
                        <h2>{{ $menu->{'name_' . $localeField} }}</h2>
                        <p>
                            {{ __('messages.review_count') }}:
                            <strong>{{ $menu->reviews_count }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('admin.menu-reviews.show', $menu) }}" class="admin-add-button">
                        {{ __('messages.view_reviews') }}
                    </a>
                </article>
            @empty
                <p>{{ __('messages.no_menu_reviews') }}</p>
            @endforelse
        </div>
        {{ $menus->links() }}
    </div>
@endsection
