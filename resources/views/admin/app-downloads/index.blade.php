@extends('admin.layouts.app')
@section('title', __('messages.app_downloads'))
@section('content') <div class="app-downloads-page">
        <h1>{{ __('messages.app_downloads') }}</h1>
        <form method="POST" action="{{ route('admin.app-downloads.update') }}" class="app-downloads-form">
            @csrf
            @method('PUT')
            <div class="app-downloads-links">
                <div class="app-download-link">
                    <label for="google_play">
                        {{ __('messages.google_play_link') }}
                    </label>
                    <input type="url" id="google_play" name="google_play"
                        value="{{ old('google_play', $downloads['google_play']->url ?? '') }}" required>
                </div>
                <div class="app-download-link">
                    <label for="app_store">
                        {{ __('messages.app_store_link') }}
                    </label>
                    <input type="text" id="app_store" name="app_store"
                        value="{{ old('app_store', $downloads['app_store']->url ?? '') }}" required>
                </div>
            </div>
            <button type="submit" class="app-downloads-save">
                {{ __('messages.save') }}
            </button>
        </form>
        <br>
        <hr>
        <br>
        <div class="qr-generators">
            <div class="qr-generator">
                <h2>{{ __('messages.google_play_link') }}</h2>
                <button type="button"
                    onclick="generateQR(
                    'google_play',
                    'google-play-qrcode',
                    'google-play-download'
                )">
                    {{ __('messages.generate_qr') }}
                </button>
                <button type="button" id="google-play-download" class="qr-download-button"
                    onclick="downloadQR(
                    'google-play-qrcode',
                    'google-play-qr.png'
                )"
                    style="display: none;">
                    {{ __('messages.download_qr') }}
                </button>
                <div id="google-play-qrcode" class="qr-code-container"></div>
            </div>
            <div class="qr-generator">
                <h2>{{ __('messages.app_store_link') }}</h2>
                <button type="button"
                    onclick="generateQR(
                    'app_store',
                    'app-store-qrcode',
                    'app-store-download'
                )">
                    {{ __('messages.generate_qr') }}
                </button>
                <button type="button" id="app-store-download" class="qr-download-button"
                    onclick="downloadQR(
                    'app-store-qrcode',
                    'app-store-qr.png'
                )"
                    style="display: none;">
                    {{ __('messages.download_qr') }}
                </button>
                <div id="app-store-qrcode" class="qr-code-container"></div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        function generateQR(inputId, qrContainerId, downloadButtonId) {
            const input = document.getElementById(inputId);
            const qrContainer = document.getElementById(qrContainerId);
            const downloadButton = document.getElementById(downloadButtonId);
            const url = input.value.trim();
            if (!url) {
                return;
            }
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: url,
                width: 220,
                height: 220
            });
            downloadButton.style.display = 'block';
        }

        function downloadQR(qrContainerId, fileName) {
            const qrContainer = document.getElementById(qrContainerId);
            const img = qrContainer.querySelector('img');
            const canvas = qrContainer.querySelector('canvas');
            let qrImage;
            if (img) {
                qrImage = img.src;
            } else if (canvas) {
                qrImage = canvas.toDataURL('image/png');
            }
            if (!qrImage) {
                return;
            }
            const link = document.createElement('a');
            link.href = qrImage;
            link.download = fileName;
            link.click();
        }
    </script>
@endsection
