@extends('admin.layouts.app')
@section('title', __('messages.create_menu_item'))
@section('content')
<h1>{{ __('messages.create_menu_item') }}</h1>
<form id="menu-form" method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data">
    @csrf
    @php
        $languages = [
            'en' => __('messages.en_language'),
            'sr_lat' => __('messages.sr_lat_language'),
            'sr_cyr' => __('messages.sr_cyr_language'),
            'hu' => __('messages.hu_language')
        ];
    @endphp
    @foreach($languages as $langKey => $langLabel)
        <fieldset>
            <legend>{{ $langLabel }}</legend>
            <div class="form-group">
                <label for="name_{{ $langKey }}">{{ __('messages.name') }}:</label>
                <input type="text"
                       name="name_{{ $langKey }}"
                       id="name_{{ $langKey }}"
                       value="{{ old('name_'.$langKey) }}"
                       class="@error('name_'.$langKey) is-invalid @enderror"
                       required>
                @error('name_'.$langKey)
                    <span class="text-danger">⚠ {{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="description_{{ $langKey }}">{{ __('messages.description') }}:</label>
                <textarea name="description_{{ $langKey }}"
                          id="description_{{ $langKey }}"
                          class="@error('description_'.$langKey) is-invalid @enderror"
                          required>{{ old('description_'.$langKey) }}</textarea>
                @error('description_'.$langKey)
                    <span class="text-danger">⚠ {{ $message }}</span>
                @enderror
            </div>
        </fieldset>
    @endforeach
    <div class="form-group">
        <label for="category_id">{{ __('messages.category') }}:</label>
        <select name="category_id" id="category_id" 
                class="@error('category_id') is-invalid @enderror" required>
            <option value="">{{ __('messages.select_category') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->{'name_'.app()->getLocale()} }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <span class="text-danger">⚠ {{ $message }}</span>
        @enderror
    </div>
    <div class="form-group">
        <label for="price">{{ __('messages.price') }}:</label>
        <input type="number"
               name="price"
               id="price"
               step="0.01"
               value="{{ old('price') }}"
               class="@error('price') is-invalid @enderror"
               required>
        @error('price')
            <span class="text-danger">⚠ {{ $message }}</span>
        @enderror
    </div>
    <div class="form-group">
        <label for="image">{{ __('messages.image') }}:</label>
        <input type="file"
               name="image"
               id="image"
               class="@error('image') is-invalid @enderror"
               required>
        @error('image')
            <span class="text-danger">⚠ {{ $message }}</span>
        @enderror
    </div>
    <button type="submit">{{ __('messages.save') }}</button>
</form>
@endsection