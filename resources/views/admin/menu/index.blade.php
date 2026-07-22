@extends('admin.layouts.app')
@section('title', __('messages.menu_management'))
@section('content')
<h1>{{ __('messages.menu_management') }}</h1>
<a href="{{ route('admin.menu.create') }}" class="btn" 
   style="background-color: #2a9d8f; color: white; padding: 8px 12px; border-radius: 5px; text-decoration:none;">
    + {{ __('messages.create_menu_item') }}
</a>
@php
$localeField = match(app()->getLocale()) {
    'en' => 'en',
    'hu' => 'hu',
    'sr' => 'sr_lat',
    'sr_cyrl' => 'sr_cyr',
    default => 'en',
};
@endphp
<table border="1" cellpadding="5" cellspacing="0" style="width:100%; margin-top:20px; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>ID</th>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.category') }}</th>
            <th>{{ __('messages.description') }}</th>
            <th>{{ __('messages.price') }}</th>
            <th>{{ __('messages.image') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->{'name_'.$localeField} ?? '-' }}</td>
                <td>{{ $item->category ? $item->category->{'name_'.$localeField} : '-' }}</td>
                <td>{{ $item->{'description_'.$localeField} ?? '-' }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>
                    @if($item->image)
                        <img src="{{ asset('images/'.$item->image) }}" alt="{{ $item->{'name_'.$localeField} }}" width="80">
                    @else
                        -
                    @endif
                </td>
                <td style="text-align:center;">
                    <a href="{{ route('admin.menu.edit', $item) }}" 
                       style="background-color:aqua; padding: 8px; border: 1px solid rgba(0,0,0,0.2); border-radius:5px; display:inline-block;">
                        ✏️
                    </a>
                    <br><br>
                    <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" class="delete-form" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 5px 8px; border-radius:5px; cursor:pointer;">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = confirm("{{ __('messages.confirm_delete') }}");
            if (!confirmed) e.preventDefault();
        });
    });
});
</script>
@endsection