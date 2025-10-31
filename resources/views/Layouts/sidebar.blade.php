<nav class="sidebar">
  <div class="sidebar-header">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">Tracking<span>App</span></a>
    <div class="sidebar-toggler not-active"><span></span><span></span><span></span></div>
  </div>

  <div class="sidebar-body">
    <ul class="nav">
      <li class="nav-item nav-category">Main</li>
      <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item nav-category">Application</li>
      <li class="nav-item">
        <a href="{{ route('users.index') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Users</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('roles.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shield"></i>
          <span class="link-title">Roles</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('permissions.index') }}" class="nav-link">
          <i class="link-icon" data-feather="key"></i>
          <span class="link-title">Permissions</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('activity.index') }}" class="nav-link">
          <i class="link-icon" data-feather="activity"></i>
          <span class="link-title">Activity</span>
        </a>
      </li>

      <li class="nav-item nav-category">Account</li>
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="link-icon" data-feather="log-out"></i>
          <span class="link-title">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      </li>
    </ul>
  </div>
</nav>