@extends('admin.layouts.app')
@section('title', __('messages.menu_management'))
@section('content') <h1>{{ __('messages.menu_management') }}</h1>
    <a href="{{ route('admin.menu.create') }}" class="admin-add-button">
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
    <div id="menu-categories">
        @foreach ($groupedItems as $categoryId => $categoryItems)
            @php
                $category = $categoryItems->first()->category;
            @endphp
            <div class="menu-category" data-category-id="{{ $categoryId }}">
                <div class="menu-category-header">
                    {{ $category ? $category->{'name_' . $localeField} : __('messages.category') }}
                </div>
                <div class="menu-sortable-list" data-category-id="{{ $categoryId }}">
                    @foreach ($categoryItems as $item)
                        <div class="menu-item" data-id="{{ $item->id }}">
                            <div class="drag-handle">
                                ☰
                            </div>
                            <div class="menu-item-image">
                                @if ($item->image)
                                    <img src="{{ asset('images/' . $item->image) }}"
                                        alt="{{ $item->{'name_' . $localeField} }}">
                                @else
                                    -
                                @endif
                            </div>
                            <div class="menu-item-content">
                                <div class="menu-item-name">
                                    {{ $item->{'name_' . $localeField} ?? '-' }}
                                </div>
                                <div class="menu-item-description">
                                    {{ $item->{'description_' . $localeField} ?? '-' }}
                                </div>
                                <div class="menu-item-price">
                                    {{ number_format($item->price, 2) }}
                                </div>
                            </div>
                            <div class="menu-item-actions">
                                <a href="{{ route('admin.menu.edit', $item) }}" class="menu-edit-button">
                                    <span class="action-icon">✏️</span>
                                </a>
                                <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-delete-button">
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
            const sortableLists = document.querySelectorAll('.menu-sortable-list');
            sortableLists.forEach(list => {
                new Sortable(list, {
                    animation: 150,
                    handle: '.drag-handle',
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
@endsection
