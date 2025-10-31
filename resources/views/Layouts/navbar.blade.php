<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="wd-30 ht-30 rounded-circle bg-primary d-flex align-items-center justify-content-center">
                        <span class="text-white fw-bold">{{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}</span>
                    </div>
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <div class="wd-80 ht-80 rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold" style="font-size: 2rem;">{{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{ Auth::user()->full_name ?? 'User' }}</p>
                            <p class="tx-12 text-muted">{{ Auth::user()->email ?? '' }}</p>
                            <p class="tx-12">
                                <span class="badge bg-info">{{ Auth::user()->role->role_name ?? 'User' }}</span>
                            </p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2">
                            <a href="{{ route('users.edit', Auth::user()) }}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="edit"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{ route('activity.index') }}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="activity"></i>
                                <span>Activity Log</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
