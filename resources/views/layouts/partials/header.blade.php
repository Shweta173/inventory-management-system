<header class="top-header">
    <button class="btn p-0 border-0 bg-transparent" style="font-size:1.2rem; color:#374151;" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    <div class="dropdown">
        <div class="user-badge" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <span class="fw-500">{{ Auth::user()->name ?? 'Admin' }}</span>
            <i class="bi bi-chevron-down" style="font-size:.7rem;color:#9ca3af;"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end" style="min-width:160px;font-size:.84rem;">
            <li>
                <span class="dropdown-item-text text-muted" style="font-size:.76rem;padding:.4rem 1rem;">
                    {{ Auth::user()->email ?? '' }}
                </span>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" class="dropdown-item text-danger" id="logoutBtn">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
