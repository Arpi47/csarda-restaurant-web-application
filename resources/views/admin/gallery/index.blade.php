@extends('admin.layouts.app')

@section('title', __('messages.gallery_management'))

@section('content')
    <div class="gallery-management-page">
        <h1>{{ __('messages.gallery_management') }}</h1>

        <br>

        @if (session('success'))
            <div class="gallery-alert gallery-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="gallery-alert gallery-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data"
            class="gallery-upload-form">
            @csrf

            <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp,.gif" multiple required>

            <button type="submit" class="btn gallery-upload-button">
                {{ __('messages.upload') }}
            </button>

            <button type="button" id="save-order" class="btn gallery-save-order-button">
                {{ __('messages.save_order') }}
            </button>
        </form>

        <hr class="gallery-divider">

        <div class="gallery-grid">
            @foreach ($images as $image)
                <div class="gallery-card" draggable="true" data-id="{{ $image->id }}">
                    <img src="{{ asset('images/gallery/' . $image->image) }}" alt="{{ $image->image }}">

                    <p>
                        {{ $image->image }}
                    </p>

                    <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}" class="delete-form">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn gallery-delete-button">
                            <span class="action-icon">🗑️</span>
                            {{ __('messages.delete') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.delete-form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (
                        !confirm(
                            "{{ __('messages.confirm_delete') }}"
                        )
                    ) {
                        e.preventDefault();
                    }
                });
            });

            let dragged;

            document
                .querySelectorAll('.gallery-card')
                .forEach(card => {
                    card.addEventListener('dragstart', function() {
                        dragged = this;
                        this.classList.add('dragging');
                    });

                    card.addEventListener('dragend', function() {
                        this.classList.remove('dragging');
                    });

                    card.addEventListener('dragover', function(e) {
                        e.preventDefault();
                    });

                    card.addEventListener('drop', function(e) {
                        e.preventDefault();

                        if (dragged !== this) {
                            let container = this.parentNode;

                            let cards = [
                                ...container.children
                            ];

                            let draggedIndex =
                                cards.indexOf(dragged);

                            let targetIndex =
                                cards.indexOf(this);

                            if (draggedIndex < targetIndex) {
                                container.insertBefore(
                                    dragged,
                                    this.nextSibling
                                );
                            } else {
                                container.insertBefore(
                                    dragged,
                                    this
                                );
                            }
                        }
                    });
                });

            document
                .getElementById('save-order')
                .addEventListener('click', function() {
                    let order = [
                        ...document.querySelectorAll('.gallery-card')
                    ].map(card => card.dataset.id);

                    fetch(
                            "{{ route('admin.gallery.reorder') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    order: order
                                })
                            }
                        )
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(
                                    "{{ __('messages.saved') }}"
                                );
                            } else {
                                alert(
                                    "{{ __('messages.save_failed') }}"
                                );
                            }
                        })
                        .catch(() => {
                            alert(
                                "{{ __('messages.save_failed') }}"
                            );
                        });
                });
        });
    </script>
@endsection
