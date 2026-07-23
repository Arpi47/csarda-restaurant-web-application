@extends('admin.layouts.app')
@section('title', __('messages.gallery_management'))
@section('content')
    <h1>{{ __('messages.gallery_management') }}</h1>
    <br>
    @if (session('success'))
        <div
            style="
        background-color:#d8f3dc;
        color:#1b4332;
        padding:12px 15px;
        border-radius:5px;
        margin-bottom:20px;
        ">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div
            style="
        background-color:#ffe5e5;
        color:#b00020;
        padding:12px 15px;
        border-radius:5px;
        margin-bottom:20px;
        ">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp,.gif" multiple required>
        <button type="submit" class="btn"
            style="
        background-color:#2a9d8f;
        color:white;
        padding:10px 15px;
        border-radius:5px;
        cursor:pointer;
        margin-left:10px;
        ">
            {{ __('messages.upload') }}
        </button>
        <button type="button" id="save-order" class="btn"
            style="
        background-color:#457b9d;
        color:white;
        padding:10px 15px;
        border-radius:5px;
        margin-top:30px;
        cursor:pointer;
        ">
            {{ __('messages.save_order') }}
        </button>
    </form>
    <hr style="margin:30px 0;">
    <div style="
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:25px;
    width:100%;
    ">
        @foreach ($images as $image)
            <div class="gallery-card" draggable="true" data-id="{{ $image->id }}"
                style="
        padding:15px;
        border-radius:10px;
        box-shadow:0 2px 8px rgba(0,0,0,0.15);
        text-align:center;
        cursor:grab;
        ">
                <img src="{{ asset('images/gallery/' . $image->image) }}" alt="{{ $image->image }}"
                    style="
            width:100%;
            height:180px;
            object-fit:cover;
            border-radius:8px;
            ">
                <p style="
            margin:12px 0;
            font-weight:bold;
            ">
                    {{ $image->image }}
                </p>
                <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn"
                        style="
                background-color:#e63946;
                color:white;
                padding:8px 12px;
                border-radius:5px;
                cursor:pointer;
                ">
                        🗑️ {{ __('messages.delete') }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms =
                document.querySelectorAll('.delete-form');
            forms.forEach(form => {
                form.addEventListener(
                    'submit',
                    function(e) {
                        if (
                            !confirm(
                                "{{ __('messages.confirm_delete') }}"
                            )
                        ) {
                            e.preventDefault();
                        }
                    }
                );
            });
            let dragged;
            document
                .querySelectorAll('.gallery-card')
                .forEach(card => {
                    card.addEventListener(
                        'dragstart',
                        function() {
                            dragged = this;
                            this.style.opacity = "0.5";
                        }
                    );
                    card.addEventListener(
                        'dragend',
                        function() {
                            this.style.opacity = "1";
                        }
                    );
                    card.addEventListener(
                        'dragover',
                        function(e) {
                            e.preventDefault();
                        }
                    );
                    card.addEventListener(
                        'drop',
                        function(e) {
                            e.preventDefault();
                            if (
                                dragged !== this
                            ) {
                                let container = this.parentNode;
                                let cards = [
                                    ...container.children
                                ];
                                let draggedIndex =
                                    cards.indexOf(
                                        dragged
                                    );
                                let targetIndex =
                                    cards.indexOf(
                                        this
                                    );
                                if (
                                    draggedIndex <
                                    targetIndex
                                ) {
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
                        }
                    );
                });
            document
                .getElementById('save-order')
                .addEventListener(
                    'click',
                    function() {
                        let order = [
                                ...document
                                .querySelectorAll(
                                    '.gallery-card'
                                )
                            ]
                            .map(card => card.dataset.id);
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
                            .then(
                                response =>
                                response.json()
                            )
                            .then(
                                data => {
                                    if (
                                        data.success
                                    ) {
                                        alert(
                                            "{{ __('messages.saved') }}"
                                        );
                                    } else {
                                        alert(
                                            "{{ __('messages.save_failed') }}"
                                        );
                                    }
                                }
                            )
                            .catch(
                                () => {
                                    alert(
                                        "{{ __('messages.save_failed') }}"
                                    );
                                }
                            );
                    }
                );
        });
    </script>
@endsection
