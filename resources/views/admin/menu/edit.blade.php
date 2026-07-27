@extends('admin.layouts.app')
@section('title', __('messages.edit_menu_item'))
@section('content') <h1>{{ __('messages.edit_menu_item') }}</h1>
    <form id="menu-form" class="admin-multilingual-form" method="POST" action="{{ route('admin.menu.update', $menu) }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php
            $languages = [
                'en' => __('messages.en_language'),
                'hu' => __('messages.hu_language'),
                'sr_lat' => __('messages.sr_lat_language'),
                'sr_cyr' => __('messages.sr_cyr_language'),
            ];
        @endphp
        <div class="language-grid">
            @foreach ($languages as $langKey => $langLabel)
                <fieldset>
                    <legend>{{ $langLabel }}</legend>
                    <div class="form-group">
                        <label for="name_{{ $langKey }}">
                            {{ __('messages.name') }}:
                        </label>
                        <input type="text" name="name_{{ $langKey }}" id="name_{{ $langKey }}"
                            value="{{ old('name_' . $langKey, $menu->{'name_' . $langKey}) }}"
                            class="@error('name_' . $langKey) is-invalid @enderror" required>
                        @error('name_' . $langKey)
                            <span class="text-danger">⚠ {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description_{{ $langKey }}">
                            {{ __('messages.description') }}:
                        </label>
                        <textarea name="description_{{ $langKey }}" id="description_{{ $langKey }}"
                            class="@error('description_' . $langKey) is-invalid @enderror" required>{{ old('description_' . $langKey, $menu->{'description_' . $langKey}) }}</textarea>
                        @error('description_' . $langKey)
                            <span class="text-danger">⚠ {{ $message }}</span>
                        @enderror
                    </div>
                </fieldset>
            @endforeach
        </div>
        <div class="form-group">
            <label for="category_id">
                {{ __('messages.category') }}:
            </label>
            <select name="category_id" id="category_id" class="@error('category_id') is-invalid @enderror" required>
                <option value="">
                    {{ __('messages.select_category') }}
                </option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->{'name_' . app()->getLocale()} }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="price">
                {{ __('messages.price') }}:
            </label>
            <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $menu->price) }}"
                class="@error('price') is-invalid @enderror" required>
            @error('price')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="image">
                {{ __('messages.image') }}:
            </label>
            @if ($menu->image)
                <div>
                    <img src="{{ asset('images/' . $menu->image) }}" alt="{{ $menu->name_en }}" width="80">
                </div>
            @endif
            <input type="file" name="image" id="image" class="@error('image') is-invalid @enderror">
            @error('image')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            <small>
                {{ __('messages.leave_blank_if_no_change') }}
            </small>
        </div>
        <button type="submit">
            {{ __('messages.save') }}
        </button>
    </form>
@endsection
