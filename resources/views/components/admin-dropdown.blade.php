@php
    $admin = auth('admin')->user();
@endphp

<div class="profile-dropdown">
    <button id="adminProfileBtn" class="profile-dropdown-toggle"
        onclick="event.stopPropagation(); toggleDropdown('adminDropdown')">
        <img src="{{ $admin->profile_image_url }}" alt="Profile" class="profile-dropdown-avatar">
    </button>
    <div id="adminDropdown" class="profile-dropdown-menu">
        <div class="profile-info">
            <a href="{{ route('admin.admins.edit', $admin) }}">
                <img src="{{ $admin->profile_image_url }}" alt="Profile" class="profile-info-avatar">
            </a>
            <div>
                <strong>{{ $admin->name }}</strong>
                <span>{{ $admin->email }}</span>
            </div>
        </div>
        <div class="logout-section">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
