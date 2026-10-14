@extends('admin.layouts.app')
@section('title', __('messages.edit_menu_item'))
@section('content')
    <h1>{{ __('messages.edit_menu_item') }}</h1>
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
                <div class="menu-primary-image">
                    <img src="{{ asset('images/' . $menu->image) }}" alt="{{ $menu->name_en }}" width="120"
                        loading="lazy">
                </div>
            @endif
            <div id="image-preview" class="menu-image-preview"></div>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp,.gif"
                class="@error('image') is-invalid @enderror">
            @error('image')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            <small>{{ __('messages.leave_blank_if_no_change') }}</small>
        </div>
        <div class="form-group menu-images-group">
            <label for="images">
                {{ __('messages.menu_additional_images') }}:
            </label>
            <p>{{ __('messages.menu_existing_images') }}</p>
            <div class="menu-image-list" id="menu-image-list">
                @forelse ($menu->images as $menuImage)
                    <div class="menu-image-item" draggable="true" data-image-id="{{ $menuImage->id }}">
                        <img src="{{ asset('images/' . $menuImage->image) }}" alt="{{ $menu->name_en }}" width="120"
                            loading="lazy">
                        <input type="hidden" name="image_order[]" value="{{ $menuImage->id }}">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="{{ $menuImage->id }}"
                                {{ in_array($menuImage->id, old('delete_images', [])) ? 'checked' : '' }}>
                            {{ __('messages.menu_delete_image') }}
                        </label>
                    </div>
                @empty
                    <p>{{ __('messages.menu_no_additional_images') }}</p>
                @endforelse
            </div>
            @error('image_order')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            @error('image_order.*')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            @error('delete_images.*')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            <label for="images">
                {{ __('messages.menu_select_images') }}:
            </label>
            <div id="images-preview" class="menu-image-preview"></div>
            <input type="file" name="images[]" id="images" accept=".jpg,.jpeg,.png,.webp,.gif" multiple
                class="@error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
            @error('images')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            @error('images.*')
                <span class="text-danger">⚠ {{ $message }}</span>
            @enderror
            <small>{{ __('messages.menu_images_help') }}</small>
        </div>
        <button type="submit">
            {{ __('messages.save') }}
        </button>
    </form>
    <script src="{{ asset('js/menu-image-preview.js') }}" defer></script>
    <script src="{{ asset('js/menu-image-sort.js') }}" defer></script>
@endsection
