@extends('layouts.app')
@php
    $localeField = match (app()->getLocale()) {
        'en' => 'en',
        'hu' => 'hu',
        'sr' => 'sr_lat',
        'sr_cyrl' => 'sr_cyr',
        default => 'en',
    };
@endphp
@section('title', __('messages.menu'))
@section('left-container')
    <div class="menu-filter-scroll">
        <form method="GET" action="{{ route('menu.index') }}" class="filters">
            <h3>{{ __('messages.filter') }}</h3>
            <div class="form-group">
                <label for="q">{{ __('messages.search') }}</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}">
            </div>
            <div class="form-group">
                <label for="category">{{ __('messages.category') }}</label>
                <select name="category" id="category">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->{'name_' . $localeField} }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="min_price">{{ __('messages.min_price') }}</label>
                <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}">
            </div>
            <div class="form-group">
                <label for="max_price">{{ __('messages.max_price') }}</label>
                <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}">
            </div>
            <button type="submit" class="search-filter">
                {{ __('messages.search') }}
            </button>
        </form>
    </div>
@endsection
@section('left-sidebar')
    <h3>{{ __('messages.promotions') }}</h3>
    <p>🔥 Daily menu</p>
    <p>🍷 Wine discount</p>
@endsection
@section('content')
    <div class="menu-toolbar">
        <button class="mobile-filter-btn" onclick="openFilters()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel"
                viewBox="0 0 16 16">
                <path
                    d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .39.81L10 7.18v6.64a.5.5 0 0 1-.79.41l-2-1.5a.5.5 0 0 1-.21-.41V7.18L1.11 1.81A.5.5 0 0 1 1.5 1.5z" />
            </svg>
        </button>
        <form method="GET" action="{{ route('menu.index') }}">
            @foreach (request()->except('sort') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <select name="sort" onchange="this.form.submit()" class="sort-option">
                <option value="">{{ __('messages.sort') }}</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>
                    🔼💰 {{ __('messages.price_asc') }}
                </option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>
                    🔽💰 {{ __('messages.price_desc') }}
                </option>
                <option value="name" @selected(request('sort') === 'name')>
                    🔤 A–Z
                </option>
            </select>
        </form>
    </div>

    {{-- ===== TALÁLATOK ===== --}}
    @if ($items->isEmpty())
        <p class="no-results">{{ __('messages.no_results') }}</p>
    @else
        <div class="menu-cards">
            @foreach ($items as $item)
                <div class="menu-card">
                    <div class="menu-image">
                        @if ($item->image)
                            <img src="{{ asset('images/' . $item->image) }}" alt="{{ $item->name }}">
                        @else
                            <img src="{{ asset('images/default.png') }}" alt="No image">
                        @endif
                    </div>

                    <div class="menu-details">
                        <h3>{{ $item->name }}</h3>
                        <p class="category">{{ $item->category }}</p>
                        <p class="description">{{ $item->description }}</p>
                        <p class="price">{{ number_format($item->price, 2) }} </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ================= MOBIL FILTER OVERLAY ================= --}}
    <div id="mobile-filters" class="mobile-filters">
        <button class="close-btn" onclick="closeFilters()">✕</button>

        <form method="GET" action="{{ route('menu.index') }}" class="filters">
            <h3 class="mobile-filter-title">{{ __('messages.filter') }}</h3>

            {{-- Keresés --}}
            <div class="form-group">
                <label for="q_mobile">{{ __('messages.search') }}</label>
                <input type="text" id="q_mobile" name="q" value="{{ request('q') }}">
            </div>

            {{-- Kategória --}}
            <div class="form-group">
                <label for="category_mobile">{{ __('messages.category') }}</label>
                <select id="category_mobile" name="category">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->{'name_' . $localeField} }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Ár szűrés --}}
            <div class="form-group">
                <label for="min_price_mobile">{{ __('messages.min_price') }}</label>
                <input type="number" id="min_price_mobile" name="min_price" value="{{ request('min_price') }}">
            </div>

            <div class="form-group">
                <label for="max_price_mobile">{{ __('messages.max_price') }}</label>
                <input type="number" id="max_price_mobile" name="max_price" value="{{ request('max_price') }}">
            </div>

            <div class="form-group">
                <button type="submit">{{ __('messages.search') }}</button>
            </div>
        </form>
    </div>

@endsection

{{-- ================= RIGHT SIDEBAR ================= --}}
@section('right-sidebar')
    <h3>{{ __('messages.events') }}</h3>
    <p>🎶 Live music Friday</p>
    <p>📱 Mobile app available</p>
@endsection

{{-- ================= JS ================= --}}
<script>
    function openFilters() {
        const filters = document.getElementById('mobile-filters');
        filters.style.left = '0';
        filters.classList.add('open');
    }

    function closeFilters() {
        const filters = document.getElementById('mobile-filters');
        filters.style.left = '-120%';
        filters.classList.remove('open');
    }
</script>
