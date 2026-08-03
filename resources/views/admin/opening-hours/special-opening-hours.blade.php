@extends('admin.layouts.app')
@section('title', __('messages.special_opening_hours'))
@section('content') <h1>{{ __('messages.special_opening_hours') }}</h1>
    <div class="contact-section">
        <form method="POST" action="{{ route('admin.opening-hours.special-opening-hours.store') }}"
            class="admin-multilingual-form admin-special-opening-hours-form">
            @csrf
            <div class="form-group">
                <label for="special_type">
                    {{ __('messages.type') }}:
                </label>
                <select id="special_type" name="type" required>
                    <option value="restaurant" {{ old('type', 'restaurant') === 'restaurant' ? 'selected' : '' }}>
                        {{ __('messages.restaurant') }}
                    </option>
                    <option value="kitchen" {{ old('type') === 'kitchen' ? 'selected' : '' }}>
                        {{ __('messages.kitchen') }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="special_date">
                    {{ __('messages.date') }}:
                </label>
                <input type="date" id="special_date" name="date" value="{{ old('date') }}" required>
            </div>
            <div class="form-group special-opening-hours-active">
                <label>
                    <input type="hidden" name="is_active" value="0">
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
                <div class="form-group">
                    <label for="special_last_reservation_time">
                        <span id="special-last-time-label">
                            {{ old('type', 'restaurant') === 'kitchen' ? __('messages.last_order') : __('messages.last_reservation') }}
                        </span>:
                    </label>
                    <input type="time" id="special_last_reservation_time" name="last_reservation_time"
                        value="{{ old('last_reservation_time') }}">
                </div>
            </div>
            <br>
            <button type="submit">
                + {{ __('messages.add_new') }}
            </button>

            <br>
        </form>
        @if ($specialOpeningHours->isNotEmpty())
            <div class="opening-hours-table-wrapper">
                <table class="opening-hours-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.active') }}</th>
                            <th>{{ __('messages.from') }}</th>
                            <th>{{ __('messages.to') }}</th>
                            <th>
                                {{ __('messages.last_reservation') }}
                                /
                                {{ __('messages.last_order') }}
                            </th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($specialOpeningHours as $specialOpeningHour)
                            <tr>
                                <td>
                                    <select name="type" form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                        class="special-type-select"
                                        data-label-id="special-last-time-label-{{ $specialOpeningHour->id }}" required>
                                        <option value="restaurant"
                                            {{ $specialOpeningHour->type === 'restaurant' ? 'selected' : '' }}>
                                            {{ __('messages.restaurant') }}
                                        </option>
                                        <option value="kitchen"
                                            {{ $specialOpeningHour->type === 'kitchen' ? 'selected' : '' }}>
                                            {{ __('messages.kitchen') }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" class="opening-hours-date" name="date"
                                        form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                        value="{{ $specialOpeningHour->date->format('Y-m-d') }}" required>
                                </td>
                                <td>
                                    <label class="opening-hours-checkbox">
                                        <input type="hidden" name="is_active" value="0"
                                            form="special-opening-hours-form-{{ $specialOpeningHour->id }}">
                                        <input type="checkbox" name="is_active" value="1"
                                            form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                            {{ $specialOpeningHour->is_active ? 'checked' : '' }}>
                                    </label>
                                </td>
                                <td>
                                    <input type="time" name="open_time"
                                        form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                        value="{{ $specialOpeningHour->open_time ? \Carbon\Carbon::parse($specialOpeningHour->open_time)->format('H:i') : '' }}">
                                </td>
                                <td>
                                    <input type="time" name="close_time"
                                        form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                        value="{{ $specialOpeningHour->close_time ? \Carbon\Carbon::parse($specialOpeningHour->close_time)->format('H:i') : '' }}">
                                </td>
                                <td>
                                    <input type="time" name="last_reservation_time"
                                        form="special-opening-hours-form-{{ $specialOpeningHour->id }}"
                                        value="{{ $specialOpeningHour->last_reservation_time
                                            ? \Carbon\Carbon::parse($specialOpeningHour->last_reservation_time)->format('H:i')
                                            : '' }}">
                                </td>
                                <td class="opening-hours-actions">
                                    <div class="opening-hours-action-buttons">
                                        <button type="submit"
                                            form="special-opening-hours-form-{{ $specialOpeningHour->id }}">
                                            <span class="action-icon">💾</span>
                                            {{ __('messages.save') }}
                                        </button>
                                        <button type="submit" class="delete-button"
                                            form="delete-special-opening-hours-form-{{ $specialOpeningHour->id }}">
                                            <span class="action-icon">🗑️</span>
                                            {{ __('messages.delete') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @foreach ($specialOpeningHours as $specialOpeningHour)
                <form id="special-opening-hours-form-{{ $specialOpeningHour->id }}" method="POST"
                    action="{{ route('admin.opening-hours.special-opening-hours.update', $specialOpeningHour) }}"
                    class="admin-special-opening-hours-form">
                    @csrf
                    @method('PUT')
                </form>
                <form id="delete-special-opening-hours-form-{{ $specialOpeningHour->id }}" method="POST"
                    action="{{ route('admin.opening-hours.special-opening-hours.destroy', $specialOpeningHour) }}"
                    class="delete-form">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const specialOpeningHoursForms = document.querySelectorAll(
                '.admin-special-opening-hours-form'
            );
            specialOpeningHoursForms.forEach(function(form) {
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
            const specialTypeSelect = document.getElementById(
                'special_type'
            );
            const specialLastTimeLabel = document.getElementById(
                'special-last-time-label'
            );
            if (
                specialTypeSelect &&
                specialLastTimeLabel
            ) {
                function updateSpecialLastTimeLabel() {
                    specialLastTimeLabel.textContent =
                        specialTypeSelect.value === 'kitchen' ?
                        @json(__('messages.last_order')) :
                        @json(__('messages.last_reservation'));
                }
                specialTypeSelect.addEventListener(
                    'change',
                    updateSpecialLastTimeLabel
                );
                updateSpecialLastTimeLabel();
            }
            const specialTypeSelects = document.querySelectorAll(
                '.special-type-select'
            );
            specialTypeSelects.forEach(function(select) {
                const labelId = select.dataset.labelId;
                const label = document.getElementById(
                    labelId
                );
                if (!label) {
                    return;
                }

                function updateExistingSpecialLastTimeLabel() {
                    label.textContent =
                        select.value === 'kitchen' ?
                        @json(__('messages.last_order')) :
                        @json(__('messages.last_reservation'));
                }
                select.addEventListener(
                    'change',
                    updateExistingSpecialLastTimeLabel
                );
                updateExistingSpecialLastTimeLabel();
            });
        });
    </script>
@endsection
