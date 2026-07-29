@extends('admin.layouts.app')

@section('title', __('messages.opening_hours'))

@section('content') <h1>{{ __('messages.opening_hours') }}</h1>
    {{-- Weekly opening hours --}}
    <div class="contact-section">
        <h2>{{ __('messages.weekly_opening_hours') }}</h2>

        @foreach ($openingHours as $openingHour)
            <form method="POST" action="{{ route('admin.opening-hours.update', $openingHour) }}"
                class="admin-multilingual-form admin-opening-hours-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ $openingHour->is_active ? 'checked' : '' }}>

                        {{ __('messages.day_' . $openingHour->day_of_week) }}
                        -
                        {{ __('messages.active') }}
                    </label>
                </div>

                <div class="opening-hours-time-fields" {{ !$openingHour->is_active ? 'hidden' : '' }}>
                    <div class="form-group">
                        <label for="open_time_{{ $openingHour->id }}">
                            {{ __('messages.from') }}:
                        </label>

                        <input type="time" id="open_time_{{ $openingHour->id }}" name="open_time"
                            value="{{ $openingHour->open_time ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i') : '' }}"
                            {{ !$openingHour->is_active ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="close_time_{{ $openingHour->id }}">
                            {{ __('messages.to') }}:
                        </label>

                        <input type="time" id="close_time_{{ $openingHour->id }}" name="close_time"
                            value="{{ $openingHour->close_time ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i') : '' }}"
                            {{ !$openingHour->is_active ? 'disabled' : '' }}>
                    </div>
                </div>

                <button type="submit">
                    {{ __('messages.save') }}
                </button>
            </form>
        @endforeach
    </div>

    {{-- Special opening hours --}}
    <div class="contact-section">
        <h2>{{ __('messages.special_opening_hours') }}</h2>

        {{-- Add new special opening hour --}}
        <form method="POST" action="{{ route('admin.special-opening-hours.store') }}"
            class="admin-multilingual-form admin-special-opening-hours-form">
            @csrf

            <div class="form-group">
                <label for="special_date">
                    {{ __('messages.date') }}:
                </label>

                <input type="date" id="special_date" name="date" value="{{ old('date') }}" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked>

                    {{ __('messages.active') }}
                </label>
            </div>

            <div class="special-opening-hours-time-fields">
                <div class="form-group">
                    <label for="special_open_time">
                        {{ __('messages.from') }}:
                    </label>

                    <input type="time" id="special_open_time" name="open_time" value="{{ old('open_time') }}">
                </div>

                <div class="form-group">
                    <label for="special_close_time">
                        {{ __('messages.to') }}:
                    </label>

                    <input type="time" id="special_close_time" name="close_time" value="{{ old('close_time') }}">
                </div>
            </div>

            <button type="submit">
                + {{ __('messages.add_new') }}
            </button>
        </form>

        {{-- Existing special opening hours --}}
        @if ($specialOpeningHours->isNotEmpty())
            <div class="special-opening-hours-list">
                @foreach ($specialOpeningHours as $specialOpeningHour)
                    <div class="special-opening-hour-item">

                        <form method="POST"
                            action="{{ route('admin.special-opening-hours.update', $specialOpeningHour) }}"
                            class="admin-multilingual-form admin-special-opening-hours-form">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>
                                    {{ __('messages.date') }}:
                                </label>

                                <input type="date" name="date"
                                    value="{{ $specialOpeningHour->date->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ $specialOpeningHour->is_active ? 'checked' : '' }}>

                                    {{ __('messages.active') }}
                                </label>
                            </div>

                            <div class="special-opening-hours-time-fields"
                                {{ !$specialOpeningHour->is_active ? 'hidden' : '' }}>
                                <div class="form-group">
                                    <label>
                                        {{ __('messages.from') }}:
                                    </label>

                                    <input type="time" name="open_time"
                                        value="{{ $specialOpeningHour->open_time ? \Carbon\Carbon::parse($specialOpeningHour->open_time)->format('H:i') : '' }}"
                                        {{ !$specialOpeningHour->is_active ? 'disabled' : '' }}>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {{ __('messages.to') }}:
                                    </label>

                                    <input type="time" name="close_time"
                                        value="{{ $specialOpeningHour->close_time ? \Carbon\Carbon::parse($specialOpeningHour->close_time)->format('H:i') : '' }}"
                                        {{ !$specialOpeningHour->is_active ? 'disabled' : '' }}>
                                </div>
                            </div>

                            <button type="submit">
                                {{ __('messages.save') }}
                            </button>
                        </form>

                        <form method="POST"
                            action="{{ route('admin.special-opening-hours.destroy', $specialOpeningHour) }}"
                            class="delete-form">
                            @csrf
                            @method('DELETE')

                            <button type="submit">
                                <span class="action-icon">🗑️</span> {{ __('messages.delete') }}
                            </button>
                        </form>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
             * Weekly and special opening hours
             */
            const openingHourForms = document.querySelectorAll(
                '.admin-opening-hours-form, .admin-special-opening-hours-form'
            );

            openingHourForms.forEach(function(form) {
                const checkbox = form.querySelector(
                    'input[type="checkbox"][name="is_active"]'
                );

                const timeFields = form.querySelector(
                    '.opening-hours-time-fields, .special-opening-hours-time-fields'
                );

                const timeInputs = form.querySelectorAll(
                    'input[type="time"]'
                );

                if (!checkbox || !timeFields) {
                    return;
                }

                function updateTimeFields() {
                    const isActive = checkbox.checked;

                    timeFields.hidden = !isActive;

                    timeInputs.forEach(function(input) {
                        input.disabled = !isActive;
                    });
                }

                checkbox.addEventListener(
                    'change',
                    updateTimeFields
                );

                updateTimeFields();
            });


            /*
             * Delete confirmation
             */
            const deleteForms = document.querySelectorAll(
                '.delete-form'
            );

            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    const confirmed = confirm(
                        @json(__('messages.confirm_delete'))
                    );

                    if (!confirmed) {
                        event.preventDefault();
                    }
                });
            });

        });
    </script>
@endsection
