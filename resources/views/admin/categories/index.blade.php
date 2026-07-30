@extends('admin.layouts.app')
@section('title', __('messages.category_management'))
@section('content') <h1>{{ __('messages.category_management') }}</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn"
        style="
            background-color: #2a9d8f;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        ">
        + {{ __('messages.create_category') }}
    </a>
    @php
        $localeField = match (app()->getLocale()) {
            'en' => 'en',
            'hu' => 'hu',
            'sr' => 'sr_lat',
            'sr_cyrl' => 'sr_cyr',
            default => 'en',
        };
    @endphp
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
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.menu_items') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody id="category-sortable">
            @foreach ($categories as $category)
                <tr draggable="true" data-id="{{ $category->id }}">
                    <td class="drag-handle" style="text-align: center; cursor: grab;">☰</td>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->{'name_' . $localeField} ?? '-' }}</td>
                    <td>{{ $category->menu_count }}</td>
                    <td style="text-align: center;">
                        <div
                            style="
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                gap: 8px;
                                flex-wrap: wrap;
                            ">
                            <a href="{{ route('admin.categories.edit', $category) }}"
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
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                class="delete-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                @if ($category->menu_count <= 0)
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
                                @else
                                    <button type="button" disabled
                                        style="
                                            background-color: rgb(218, 218, 218);
                                            width: 40px;
                                            height: 40px;
                                            padding: 0;
                                            border: 1px solid rgba(0, 0, 0, 0.2);
                                            border-radius: 5px;
                                            cursor: not-allowed;
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            box-sizing: border-box;
                                        ">
                                        <span class="action-icon">🗑️</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('category-sortable');
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
                saveCategoryOrder();
            });

            function saveCategoryOrder() {
                const categories = Array.from(
                    tbody.querySelectorAll('tr')
                ).map(row => row.dataset.id);
                fetch("{{ route('admin.categories.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            categories: categories
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to save category order.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error('Failed to save category order.');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Failed to save category order.');
                    });
            }
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
        });
    </script>
@endsection
