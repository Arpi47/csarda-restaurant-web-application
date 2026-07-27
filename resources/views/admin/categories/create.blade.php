@extends('admin.layouts.app')
@section('title', __('messages.create_category'))
@section('content') <h1>{{ __('messages.create_category') }}</h1>
    <form id="category-form" class="admin-multilingual-form" method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
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
                            value="{{ old('name_' . $langKey) }}" class="@error('name_' . $langKey) is-invalid @enderror"
                            required>
                        @error('name_' . $langKey)
                            <span class="text-danger">
                                ⚠ {{ $message }}
                            </span>
                        @enderror
                    </div>
                </fieldset>
            @endforeach
        </div>
        <button type="submit">
            {{ __('messages.save') }}
        </button>
    </form>
@endsection
