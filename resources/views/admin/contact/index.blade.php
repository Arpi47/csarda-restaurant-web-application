@extends('admin.layouts.app')
@section('title', __('messages.contact_management'))
@section('content')
    <h1>{{ __('messages.contact_management') }}</h1>
    <div class="contact-section">
        <h2>{{ __('messages.contact_information') }}</h2>
        <form method="POST" action="{{ route('admin.contact.information.update') }}" class="admin-multilingual-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="phone">
                    {{ __('messages.phone') }}:
                </label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $contactInformation->phone) }}"
                    class="@error('phone') is-invalid @enderror" required>
                @error('phone')
                    <span class="text-danger">
                        ⚠ {{ $message }}
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">
                    {{ __('messages.email') }}:
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $contactInformation->email) }}"
                    class="@error('email') is-invalid @enderror" required>
                @error('email')
                    <span class="text-danger">
                        ⚠ {{ $message }}
                    </span>
                @enderror
            </div>
            <button type="submit">
                {{ __('messages.save') }}
            </button>
        </form>
    </div>
    <div
        style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        ">
        <h2>{{ __('messages.social_media') }}</h2>
    </div>
    <table border="1" cellpadding="5" cellspacing="0"
        style="
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        ">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>☰</th>
                <th>ID</th>
                <th>{{ __('messages.platform') }}</th>
                <th>{{ __('messages.social_media_url') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="social-sortable">
            @foreach ($socialLinks as $socialLink)
                <tr draggable="true" data-id="{{ $socialLink->id }}">
                    <td class="drag-handle"
                        style="
                            text-align: center;
                            cursor: grab;
                        ">
                        ☰
                    </td>
                    <td>{{ $socialLink->id }}</td>
                    <td>{{ ucfirst($socialLink->platform) }}</td>
                    <td>{{ $socialLink->url }}</td>
                    <td>
                        @if ($socialLink->is_active)
                            {{ __('messages.active') }}
                        @else
                            {{ __('messages.inactive') }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div
                            style="
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                gap: 8px;
                                flex-wrap: wrap;
                            ">
                            <a href="{{ route('admin.contact.social.edit', $socialLink) }}"
                                style="
                                    background-color: #2a9d8f;
                                    width: 40px;
                                    height: 40px;
                                    padding: 0;
                                    border: 1px solid rgba(0, 0, 0, 0.2);
                                    border-radius: 5px;
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    box-sizing: border-box;
                                    text-decoration: none;
                                "
                                title="{{ __('messages.edit') }}">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('admin.contact.social.destroy', $socialLink) }}"
                                class="delete-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="
                                        background-color: #e76f51;
                                        width: 40px;
                                        height: 40px;
                                        padding: 0;
                                        border: 1px solid rgba(0, 0, 0, 0.2);
                                        border-radius: 5px;
                                        cursor: pointer;
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-sizing: border-box;
                                    "
                                    title="{{ __('messages.delete') }}">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="contact-section">
        <h2>{{ __('messages.add_social_media') }}</h2>
        <form method="POST" action="{{ route('admin.contact.social.store') }}" class="admin-multilingual-form">
            @csrf
            <div class="form-group">
                <label for="platform">
                    {{ __('messages.platform') }}:
                </label>
                <select id="platform" name="platform" required>
                    <option value="">
                        {{ __('messages.select_platform') }}
                    </option>
                    @php
                        $platforms = [
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'tiktok' => 'TikTok',
                            'youtube' => 'YouTube',
                            'x' => 'X (Twitter)',
                            'linkedin' => 'LinkedIn',
                            'whatsapp' => 'WhatsApp',
                            'telegram' => 'Telegram',
                            'snapchat' => 'Snapchat',
                            'pinterest' => 'Pinterest',
                            'reddit' => 'Reddit',
                            'threads' => 'Threads',
                            'discord' => 'Discord',
                            'twitch' => 'Twitch',
                            'vk' => 'VK',
                            'wechat' => 'WeChat',
                            'messenger' => 'Messenger',
                        ];
                    @endphp
                    @foreach ($platforms as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('platform')
                    <span class="text-danger">
                        ⚠ {{ $message }}
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="url">
                    {{ __('messages.social_media_url') }}:
                </label>
                <input type="url" id="url" name="url" value="{{ old('url') }}" placeholder="https://..."
                    required>
                @error('url')
                    <span class="text-danger">
                        ⚠ {{ $message }}
                    </span>
                @enderror
            </div>
            <button type="submit">
                + {{ __('messages.add_new') }}
            </button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('social-sortable');
            let draggedRow = null;
            tbody.addEventListener('dragstart', function(event) {
                draggedRow = event.target.closest('tr');
                if (!draggedRow) {
                    return;
                }
                draggedRow.classList.add('dragging');
            });
            tbody.addEventListener('dragend', function() {
                if (draggedRow) {
                    draggedRow.classList.remove('dragging');
                }
                draggedRow = null;
            });
            tbody.addEventListener('dragover', function(event) {
                event.preventDefault();
                const targetRow = event.target.closest('tr');
                if (
                    !targetRow ||
                    targetRow === draggedRow
                ) {
                    return;
                }
                const rect = targetRow.getBoundingClientRect();
                const offset = event.clientY - rect.top;
                if (offset > rect.height / 2) {
                    targetRow.after(draggedRow);
                } else {
                    targetRow.before(draggedRow);
                }

            });
            tbody.addEventListener('drop', function(event) {
                event.preventDefault();
                saveSocialOrder();
            });

            function saveSocialOrder() {
                const items = Array.from(
                    tbody.querySelectorAll('tr')
                ).map((row, index) => ({
                    id: row.dataset.id,
                    sort_order: index + 1
                }));
                if (items.length === 0) {
                    return;
                }
                fetch(
                        "{{ route('admin.contact.social.reorder') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                items: items
                            })
                        }
                    )
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(
                                'Failed to save social media order.'
                            );
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(
                                'Failed to save social media order.'
                            );
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert(
                            @json(__('messages.contact_social_reorder_failed'))
                        );
                    });
            }
            const deleteForms = document.querySelectorAll(
                '.delete-form'
            );
            deleteForms.forEach(form => {
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
