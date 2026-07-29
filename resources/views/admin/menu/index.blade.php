@extends('admin.layouts.app')
@section('title', __('messages.menu_management'))
@section('content') <h1>{{ __('messages.menu_management') }}</h1>
    <a href="{{ route('admin.menu.create') }}" class="btn"
        style="
        background-color: #2a9d8f;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
    ">
        + {{ __('messages.create_menu_item') }}
    </a>
    @php
        $localeField = match (app()->getLocale()) {
            'en' => 'en',
            'hu' => 'hu',
            'sr' => 'sr_lat',
            'sr_cyrl' => 'sr_cyr',
            default => 'en',
        };
        $groupedItems = $items->groupBy('category_id');
    @endphp
    <div id="menu-categories" style="margin-top: 20px;">
        @foreach ($groupedItems as $categoryId => $categoryItems)
            @php
                $category = $categoryItems->first()->category;
            @endphp
            <div class="menu-category" data-category-id="{{ $categoryId }}"
                style="
                margin-bottom: 30px;
                border: 1px solid #ccc;
                border-radius: 8px;
                overflow: hidden;
            ">
                <div
                    style="
                background-color: #2a9d8f;
                color: white;
                padding: 12px 15px;
                font-size: 1.2em;
                font-weight: bold;
            ">
                    {{ $category ? $category->{'name_' . $localeField} : __('messages.category') }}
                </div>
                <div class="menu-sortable-list" data-category-id="{{ $categoryId }}" style="padding: 10px;">
                    @foreach ($categoryItems as $item)
                        <div class="menu-item" data-id="{{ $item->id }}"
                            style="
                            display: flex;
                            align-items: center;
                            gap: 15px;
                            padding: 12px;
                            margin-bottom: 8px;
                            background-color: #fff;
                            border: 1px solid #ddd;
                            border-radius: 6px;
                            cursor: grab;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
                        ">
                            <div class="drag-handle"
                                style="
                                font-size: 22px;
                                color: #777;
                                cursor: grab;
                                user-select: none;
                            ">
                                ☰
                            </div>
                            <div
                                style="
                            width: 80px;
                            flex-shrink: 0;
                            text-align: center;
                        ">
                                @if ($item->image)
                                    <img src="{{ asset('images/' . $item->image) }}"
                                        alt="{{ $item->{'name_' . $localeField} }}"
                                        style="
                                        width: 70px;
                                        height: 70px;
                                        object-fit: cover;
                                        border-radius: 5px;
                                    ">
                                @else
                                    -
                                @endif
                            </div>
                            <div
                                style="
                            flex: 1;
                            min-width: 0;
                        ">
                                <div
                                    style="
                                font-weight: bold;
                                font-size: 1.1em;
                                margin-bottom: 5px;
                            ">
                                    {{ $item->{'name_' . $localeField} ?? '-' }}
                                </div>
                                <div
                                    style="
                                color: #555;
                                font-size: 0.9em;
                                margin-bottom: 5px;
                            ">
                                    {{ $item->{'description_' . $localeField} ?? '-' }}
                                </div>
                                <div
                                    style="
                                color: #2a9d8f;
                                font-weight: bold;
                            ">
                                    {{ number_format($item->price, 2) }}
                                </div>
                            </div>
                            <div
                                style="
                            display: flex;
                            flex-direction: column;
                            gap: 6px;
                            flex-shrink: 0;
                        ">
                                <a href="{{ route('admin.menu.edit', $item) }}"
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
                                    ">
                                    <span class="action-icon">✏️</span>
                                </a>
                                <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" class="delete-form"
                                    style="display: inline;">
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
                                        ">
                                        <span class="action-icon">🗑️</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const confirmed = confirm(
                        "{{ __('messages.confirm_delete') }}"
                    );

                    if (!confirmed) {
                        e.preventDefault();
                    }
                });
            });


            // Menu item sorting
            const sortableLists = document.querySelectorAll('.menu-sortable-list');

            sortableLists.forEach(list => {

                new Sortable(list, {
                    animation: 150,

                    // Csak a fogantyúnál lehessen húzni
                    handle: '.drag-handle',

                    // Ne lehessen másik kategóriába áthúzni
                    group: {
                        name: 'menu-items',
                        pull: false,
                        put: false
                    },

                    ghostClass: 'menu-item-ghost',

                    onEnd: function() {

                        const items = [];

                        list.querySelectorAll('.menu-item').forEach((item, index) => {

                            items.push({
                                id: item.dataset.id,
                                sort_order: index + 1
                            });

                        });

                        if (items.length === 0) {
                            return;
                        }

                        fetch("{{ route('admin.menu.reorder') }}", {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },

                                body: JSON.stringify({
                                    items: items
                                })
                            })
                            .then(response => {

                                if (!response.ok) {
                                    throw new Error(
                                        'Failed to save menu order.'
                                    );
                                }

                                return response.json();
                            })
                            .then(data => {

                                if (data.success) {
                                    console.log(
                                        'Menu order saved successfully.'
                                    );
                                }

                            })
                            .catch(error => {

                                console.error(
                                    'Error while saving menu order:',
                                    error
                                );

                                alert(
                                    'A menü sorrendjének mentése sikertelen.'
                                );

                            });

                    }

                });

            });

        });
    </script>
    <style>
        .menu-item {
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .menu-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        }

        .menu-item:active {
            cursor: grabbing !important;
        }

        .menu-item-ghost {
            opacity: 0.4;
            background-color: #e0f7fa !important;
        }

        @media (max-width: 700px) {

            .menu-item {
                flex-wrap: wrap;
            }

            .menu-item>div:nth-child(3) {
                width: calc(100% - 110px);
            }

            .menu-item>div:last-child {
                flex-direction: row;
                width: 100%;
                justify-content: flex-end;
            }

            .menu-item>div:last-child form {
                width: auto !important;
            }
        }
    </style>
@endsection
