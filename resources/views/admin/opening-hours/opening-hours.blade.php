@extends('admin.layouts.app')
@section('title', __('messages.opening_hours'))
@section('content') <h1>{{ __('messages.opening_hours') }}</h1>
    <div class="contact-section">
        <h2>{{ __('messages.restaurant_opening_hours') }}</h2>
        <div class="opening-hours-table-wrapper">
            <table class="opening-hours-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.day') }}</th>
                        <th>{{ __('messages.active') }}</th>
                        <th>{{ __('messages.from') }}</th>
                        <th>{{ __('messages.to') }}</th>
                        <th>{{ __('messages.last_reservation') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($restaurantOpeningHours as $openingHour)
                        <tr>
                            <td>
                                {{ __('messages.day_' . $openingHour->day_of_week) }}
                            </td>
                            <td>
                                <label class="opening-hours-checkbox">
                                    <input type="hidden" name="is_active" value="0"
                                        form="opening-hours-form-{{ $openingHour->id }}">
                                    <input type="checkbox" name="is_active" value="1"
                                        form="opening-hours-form-{{ $openingHour->id }}"
                                        {{ $openingHour->is_active ? 'checked' : '' }}>
                                    {{ __('messages.active') }}
                                </label>
                            </td>
                            <td>
                                <input type="time" name="open_time" form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->open_time ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i') : '' }}">
                            </td>
                            <td>
                                <input type="time" name="close_time" form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->close_time ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i') : '' }}">
                            </td>
                            <td>
                                <input type="time" name="last_reservation_time"
                                    form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->last_reservation_time ? \Carbon\Carbon::parse($openingHour->last_reservation_time)->format('H:i') : '' }}">
                            </td>
                            <td class="opening-hours-actions">
                                <button type="submit" form="opening-hours-form-{{ $openingHour->id }}">
                                    <span class="action-icon">💾</span>
                                    {{ __('messages.save') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @foreach ($restaurantOpeningHours as $openingHour)
            <form id="opening-hours-form-{{ $openingHour->id }}" method="POST"
                action="{{ route('admin.opening-hours.opening-hours.update', $openingHour) }}"
                class="admin-opening-hours-form">
                @csrf
                @method('PUT')
            </form>
        @endforeach
    </div>
    <div class="contact-section">
        <h2>{{ __('messages.kitchen_opening_hours') }}</h2>
        <div class="opening-hours-table-wrapper">
            <table class="opening-hours-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.day') }}</th>
                        <th>{{ __('messages.active') }}</th>
                        <th>{{ __('messages.from') }}</th>
                        <th>{{ __('messages.to') }}</th>
                        <th>{{ __('messages.last_order') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kitchenOpeningHours as $openingHour)
                        <tr>
                            <td>
                                {{ __('messages.day_' . $openingHour->day_of_week) }}
                            </td>
                            <td>
                                <label class="opening-hours-checkbox">
                                    <input type="hidden" name="is_active" value="0"
                                        form="opening-hours-form-{{ $openingHour->id }}">
                                    <input type="checkbox" name="is_active" value="1"
                                        form="opening-hours-form-{{ $openingHour->id }}"
                                        {{ $openingHour->is_active ? 'checked' : '' }}>

                                    {{ __('messages.active') }}
                                </label>
                            </td>
                            <td>
                                <input type="time" name="open_time" form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->open_time ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i') : '' }}">
                            </td>
                            <td>
                                <input type="time" name="close_time" form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->close_time ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i') : '' }}">
                            </td>
                            <td>
                                <input type="time" name="last_reservation_time"
                                    form="opening-hours-form-{{ $openingHour->id }}"
                                    value="{{ $openingHour->last_reservation_time ? \Carbon\Carbon::parse($openingHour->last_reservation_time)->format('H:i') : '' }}">
                            </td>
                            <td class="opening-hours-actions">
                                <button type="submit" form="opening-hours-form-{{ $openingHour->id }}">
                                    <span class="action-icon">💾</span>
                                    {{ __('messages.save') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @foreach ($kitchenOpeningHours as $openingHour)
            <form id="opening-hours-form-{{ $openingHour->id }}" method="POST"
                action="{{ route('admin.opening-hours.opening-hours.update', $openingHour) }}"
                class="admin-opening-hours-form">
                @csrf
                @method('PUT')
            </form>
        @endforeach
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openingHourForms = document.querySelectorAll(
                '.admin-opening-hours-form'
            );
            openingHourForms.forEach(function(form) {
                const checkbox = form.querySelector(
                    'input[type="checkbox"][name="is_active"]'
                );
                const timeInputs = form.querySelectorAll(
                    'input[type="time"]'
                );
                if (!checkbox) {
                    return;
                }

                function updateTimeFields() {
                    const isActive = checkbox.checked;
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
        });
    </script>
@endsection
