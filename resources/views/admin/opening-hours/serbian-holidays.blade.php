@extends('admin.layouts.app')
@section('title', __('messages.serbian_holidays'))
@section('content') <h1>{{ __('messages.serbian_holidays') }}</h1>
    <div class="contact-section">
        <h2>{{ __('messages.serbian_holidays_management') }}</h2>
        <div class="opening-hours-table-wrapper">
            <table class="opening-hours-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.holiday') }}</th>
                        <th>{{ __('messages.restaurant_active') }}</th>
                        <th>{{ __('messages.restaurant_opening_hours') }}</th>
                        <th>{{ __('messages.restaurant_last_reservation') }}</th>
                        <th>{{ __('messages.kitchen_active') }}</th>
                        <th>{{ __('messages.kitchen_opening_hours') }}</th>
                        <th>{{ __('messages.kitchen_last_order') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holidays as $holiday)
                        <tr>
                            <td>
                                {{ $holiday->date->format('Y-m-d') }}
                            </td>
                            <td>
                                {{ $holiday->name }}
                            </td>
                            <td>
                                <label class="opening-hours-checkbox">
                                    <input type="hidden" name="restaurant_is_active" value="0"
                                        form="serbian-holiday-form-{{ $holiday->id }}">
                                    <input type="checkbox" name="restaurant_is_active" value="1"
                                        form="serbian-holiday-form-{{ $holiday->id }}"
                                        {{ $holiday->restaurant_is_active ? 'checked' : '' }}>
                                    {{ __('messages.active') }}
                                </label>
                            </td>
                            <td>
                                <div class="holiday-time-fields">
                                    <label>
                                        <input type="time" name="restaurant_open_time"
                                            form="serbian-holiday-form-{{ $holiday->id }}"
                                            value="{{ $holiday->restaurant_open_time ? \Carbon\Carbon::parse($holiday->restaurant_open_time)->format('H:i') : '' }}">
                                    </label>
                                    <label>
                                        <input type="time" name="restaurant_close_time"
                                            form="serbian-holiday-form-{{ $holiday->id }}"
                                            value="{{ $holiday->restaurant_close_time ? \Carbon\Carbon::parse($holiday->restaurant_close_time)->format('H:i') : '' }}">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="time" name="restaurant_last_reservation_time"
                                    form="serbian-holiday-form-{{ $holiday->id }}"
                                    value="{{ $holiday->restaurant_last_reservation_time
                                        ? \Carbon\Carbon::parse($holiday->restaurant_last_reservation_time)->format('H:i')
                                        : '' }}">
                            </td>
                            <td>
                                <label class="opening-hours-checkbox">
                                    <input type="hidden" name="kitchen_is_active" value="0"
                                        form="serbian-holiday-form-{{ $holiday->id }}">
                                    <input type="checkbox" name="kitchen_is_active" value="1"
                                        form="serbian-holiday-form-{{ $holiday->id }}"
                                        {{ $holiday->kitchen_is_active ? 'checked' : '' }}>
                                    {{ __('messages.active') }}
                                </label>
                            </td>
                            <td>
                                <div class="holiday-time-fields">
                                    <label>
                                        <input type="time" name="kitchen_open_time"
                                            form="serbian-holiday-form-{{ $holiday->id }}"
                                            value="{{ $holiday->kitchen_open_time ? \Carbon\Carbon::parse($holiday->kitchen_open_time)->format('H:i') : '' }}">
                                    </label>
                                    <label>
                                        <input type="time" name="kitchen_close_time"
                                            form="serbian-holiday-form-{{ $holiday->id }}"
                                            value="{{ $holiday->kitchen_close_time ? \Carbon\Carbon::parse($holiday->kitchen_close_time)->format('H:i') : '' }}">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="time" name="kitchen_last_order_time"
                                    form="serbian-holiday-form-{{ $holiday->id }}"
                                    value="{{ $holiday->kitchen_last_order_time
                                        ? \Carbon\Carbon::parse($holiday->kitchen_last_order_time)->format('H:i')
                                        : '' }}">
                            </td>
                            <td class="opening-hours-actions">
                                <button type="submit" form="serbian-holiday-form-{{ $holiday->id }}">
                                    <span class="action-icon">💾</span>
                                    {{ __('messages.save') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                {{ __('messages.no_serbian_holidays') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @foreach ($holidays as $holiday)
            <form id="serbian-holiday-form-{{ $holiday->id }}" method="POST"
                action="{{ route('admin.opening-hours.serbian-holidays.update', $holiday) }}"
                class="admin-serbian-holiday-form">
                @csrf
                @method('PUT')
            </form>
        @endforeach
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const holidayForms = document.querySelectorAll(
                '.admin-serbian-holiday-form'
            );
            holidayForms.forEach(function(form) {
                const restaurantCheckbox = document.querySelector(
                    'input[type="checkbox"][name="restaurant_is_active"][form="' + form.id + '"]'
                );
                const kitchenCheckbox = document.querySelector(
                    'input[type="checkbox"][name="kitchen_is_active"][form="' + form.id + '"]'
                );
                const restaurantTimeInputs = document.querySelectorAll(
                    'input[type="time"][form="' + form.id + '"][name^="restaurant_"]'
                );
                const kitchenTimeInputs = document.querySelectorAll(
                    'input[type="time"][form="' + form.id + '"][name^="kitchen_"]'
                );

                function updateRestaurantTimeFields() {
                    if (!restaurantCheckbox) {
                        return;
                    }
                    const isActive = restaurantCheckbox.checked;
                    restaurantTimeInputs.forEach(function(input) {
                        input.disabled = !isActive;
                    });
                }

                function updateKitchenTimeFields() {
                    if (!kitchenCheckbox) {
                        return;
                    }
                    const isActive = kitchenCheckbox.checked;
                    kitchenTimeInputs.forEach(function(input) {
                        input.disabled = !isActive;
                    });
                }
                if (restaurantCheckbox) {
                    restaurantCheckbox.addEventListener(
                        'change',
                        updateRestaurantTimeFields
                    );
                    updateRestaurantTimeFields();
                }
                if (kitchenCheckbox) {
                    kitchenCheckbox.addEventListener(
                        'change',
                        updateKitchenTimeFields
                    );
                    updateKitchenTimeFields();
                }
            });
        });
    </script>
@endsection
