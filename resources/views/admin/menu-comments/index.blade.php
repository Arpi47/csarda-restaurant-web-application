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
    @endphp
    <div class="admin-review-page">
        <h1>{{ __('messages.menu_comments') }}</h1>
        <div class="review-menu-list">
            @forelse ($menus as $menu)
                <article class="review-menu-card">
                    <div class="review-menu-info">
                        <h2>{{ $menu->{'name_' . $localeField} }}</h2>
                        <p>
                            {{ __('messages.comment_count') }}:
                            <strong>{{ $menu->comments_count }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('admin.menu-comments.show', $menu) }}" class="admin-add-button">
                        {{ __('messages.view_comments') }}
                    </a>
                </article>
            @empty
                <p>{{ __('messages.no_menu_comments') }}</p>
            @endforelse
        </div>
        {{ $menus->links() }}
    </div>
@endsection
