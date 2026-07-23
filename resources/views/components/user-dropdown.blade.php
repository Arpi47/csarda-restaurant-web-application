@php
    $user = auth('web')->user();
@endphp
@if ($user)
    <div class="profile-dropdown">
        <button id="userProfileBtn" onclick="event.stopPropagation(); toggleDropdown('userDropdown')">
            <img src="{{ $user->profile_image_url }}" class="avatar" alt="Profile">
        </button>
        <div id="userDropdown" class="profile-dropdown-menu">
            <div class="profile-info">
                <strong>{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</strong>
                <span>{{ $user->email }}</span>
            </div>
            <a href="{{ route('user.profile.edit') }}" class="dropdown-item">
                {{ __('messages.edit_profile') }}
            </a>
            <a href="{{ route('user.reservations.index') }}" class="btn">
                {{ __('messages.my_reservations') }}
            </a>
            <div class="logout-section">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">{{ __('messages.logout') }}</button>
                </form>
            </div>
        </div>
    </div>
@else
    <a href="{{ route('login') }}" class="btn">{{ __('messages.login') }}</a>
@endif
<script>
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
    }
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = document.getElementById('userProfileBtn');
        if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
<style>
    .profile-dropdown {
        position: relative;
        display: inline-block;
        z-index: 2000;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
    }

    .profile-dropdown-menu {
        display: none;
        position: absolute;
        top: 50px;
        right: 0;
        width: 220px;
        flex-direction: column;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        z-index: 2000;
    }

    .profile-dropdown-menu .profile-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 10px;
        text-align: center;
    }

    .profile-dropdown-menu .dropdown-item {
        display: block;
        width: 100%;
        padding: 5px 0;
        text-decoration: none;
        color: #333;
        background: none;
        border: none;
        cursor: pointer;
    }

    .profile-dropdown-menu .dropdown-item:hover {
        background: #f0f0f0;
    }

    .logout-section {
        border-top: 1px solid #eee;
        padding-top: 10px;
        text-align: center;
    }

    .logout-section button {
        background-color: #2a9d8f;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .logout-section button:hover {
        background-color: #21867a;
    }
</style>
