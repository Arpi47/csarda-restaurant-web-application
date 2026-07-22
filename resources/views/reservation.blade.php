@extends('layouts.app')
@section('title', __('messages.reservation'))
@section('left-sidebar')
<h3>{{ __('messages.promotions') }}</h3>
<p>🔥 Daily menu</p>
<p>🍷 Wine discount</p>
@endsection
@section('content')
<div class="reservation">
    <form id="reservation-form" method="POST" action="{{ url('/foglalas') }}" class="reservation-form">
        <div class="intro">
            <h1>{{ __('messages.reservation') }}</h1>
            <p>{{ __('messages.reservation_description') }}</p>
        </div>
        @csrf
        <div class="form-group">
            <label for="email">{{ __('messages.email') }}:</label>
            <input type="email" id="email" name="email" required value="{{ old('email', auth()->user()->email) }}">
        </div>
        <div class="form-group">
            <label for="first_name">{{ __('messages.first_name') }}:</label>
            <input type="text" id="first_name" name="first_name" required value="{{ old('first_name', auth()->user()->first_name) }}">
        </div>
        <div class="form-group">
            <label for="last_name">{{ __('messages.last_name') }}:</label>
            <input type="text" id="last_name" name="last_name" required value="{{ old('last_name', auth()->user()->last_name) }}">
        </div>
        <div class="form-group">
            <label for="date">{{ __('messages.date') }}:</label>
            <input type="date" id="date" name="date" required min="{{ now()->addDays(3)->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label for="time">{{ __('messages.time') }}:</label>
            <input type="time" id="time" name="time" required>
        </div>
        <div class="form-group">
            <label for="guests">{{ __('messages.guests') }}:</label>
            <input type="number" id="guests" name="guests" min="1" max="70" value="1" required>
        </div>
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <button type="submit" class="reserve-btn">{{ __('messages.reserve') }}</button>
    </form>
</div>
<div id="popup-message" class="popup" style="display:none;">
    <div class="popup-content">
        <span id="popup-text"></span>
        <button class="popup-close-btn" onclick="closePopup()">{{ __('messages.close') }}</button>
    </div>
</div>
@endsection
@section('right-sidebar')
<h3>{{ __('messages.events') }}</h3>
<p>🎶 Live music Friday</p>
<p>📱 Mobile app available</p>
@endsection
@push('styles')
<style>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.popup-content {
    background-color: #fff;
    border-radius: 8px;
    padding: 20px 30px;
    width: 90%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 150px;
    text-align: center;
}

#popup-text {
    white-space: pre-line;
}

.popup-close-btn {
    align-self: center;
    padding: 10px 25px;
    border: none;
    background-color: #e76f51;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
}

.popup-close-btn:hover {
    background-color: #c84b3f;
}
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://www.google.com/recaptcha/api.js?render={{ config('app.recaptcha_site_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#date", {
        minDate: new Date().fp_incr(3),
        dateFormat: "Y-m-d",
        locale: {
            firstDayOfWeek: 1
        },
        disable: [
            function(date) {
                return (date.getDay() === 1);
            }
        ]
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reservation-form');
    const popup = document.getElementById('popup-message');
    const popupText = document.getElementById('popup-text');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('app.recaptcha_site_key') }}', {action: 'reservation'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                const data = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': data.get('_token'), 
                        'Accept': 'application/json' 
                    },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    if(result.success){
                        popupText.innerText = '{{ __("messages.reservation_success") }}';
                        form.reset();
                    } else {
                        let errorMsg = '{{ __("messages.reservation_error") }}';
                        if(result.message) errorMsg += "\n" + result.message;
                        popupText.innerText = errorMsg;
                    }
                    popup.style.display = 'flex';
                })
                .catch(err => {
                    popupText.innerText = '{{ __("messages.reservation_error") }}';
                    popup.style.display = 'flex';
                    console.error(err);
                });
            });
        });
    });
});
function closePopup(){
    document.getElementById('popup-message').style.display = 'none';
}
</script>
@endpush