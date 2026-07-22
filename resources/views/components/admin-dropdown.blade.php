@php
    $admin = auth('admin')->user();
@endphp
<div class="profile-dropdown">
    <button id="adminProfileBtn" onclick="event.stopPropagation(); toggleDropdown('adminDropdown')"
            style="border:none; background:transparent; cursor:pointer;">
        <img src="{{ $admin->profile_image_url }}" alt="Profile" 
            style="width:40px; height:40px; border-radius:50%; border:2px solid #ccc; object-fit:cover;">
    </button>
    <div id="adminDropdown" class="profile-dropdown-menu">
        <div class="profile-info">
            <a href="{{ route('admin.admins.edit', $admin) }}">
                <img src="{{ $admin->profile_image_url }}" alt="Profile" 
                     style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
            </a>
            <div>
                <strong>{{ $admin->name }}</strong>
                <span>{{ $admin->email }}</span>
            </div>
        </div>
        <div class="logout-section">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">{{ __('messages.logout') }}</button>
            </form>
        </div>
    </div>
</div>
<script>
function toggleAdminDropdown() {
    const dropdown = document.getElementById('adminDropdown');
    dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
}
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('adminDropdown');
    const button = document.getElementById('adminProfileBtn');
    if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
<style>
.profile-dropdown {
    position:absolute; 
    right: 50px;
}

@media (max-width: 530px) {
    .profile-dropdown {
        right: 30px;
    }
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ccc;
}

.avatar-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ccc;
    margin-bottom: 5px;
}

.dropdown {
    display: inline-block;
}

.dropdown-toggle {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 5px;
}

.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 50px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    min-width: 220px;
    padding: 10px;
    z-index: 100;
    flex-direction: column;
}

.dropdown-menu .dropdown-item {
    width: 100%;
    text-align: left;
    padding: 5px 0;
    background: none;
    border: none;
    cursor: pointer;
    color: #333;
    text-decoration: none;
}

.dropdown-menu .dropdown-item:hover {
    background: #f0f0f0;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}
</style>