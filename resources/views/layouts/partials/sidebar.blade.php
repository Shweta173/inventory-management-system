<nav id="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-badge">INV</div>
        <span class="logo-text">Inventory</span>
    </div>

    <ul class="sidebar-nav list-unstyled">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </li>

        {{-- Masters parent --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('categories.*') || request()->routeIs('materials.index') ? 'parent-active' : '' }}"
               data-bs-toggle="collapse" href="#mastersSub" role="button"
               aria-expanded="{{ request()->routeIs('categories.*') || request()->routeIs('materials.index') ? 'true' : 'false' }}">
                Masters
                <i class="bi bi-chevron-right nav-arrow"></i>
            </a>
            <ul class="sidebar-sub collapse {{ request()->routeIs('categories.*') || request()->routeIs('materials.index') ? 'show' : '' }}" id="mastersSub">
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('materials.index') }}"
                       class="nav-link {{ request()->routeIs('materials.index') ? 'active' : '' }}">
                        Materials
                    </a>
                </li>
            </ul>
        </li>

        {{-- Transactions parent --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('inward_outward.*') ? 'parent-active' : '' }}"
               data-bs-toggle="collapse" href="#transactionsSub" role="button"
               aria-expanded="{{ request()->routeIs('inward_outward.*') ? 'true' : 'false' }}">
                Transactions
                <i class="bi bi-chevron-right nav-arrow"></i>
            </a>
            <ul class="sidebar-sub collapse {{ request()->routeIs('inward_outward.*') ? 'show' : '' }}" id="transactionsSub">
                <li class="nav-item">
                    <a href="{{ route('inward_outward.index') }}"
                       class="nav-link {{ request()->routeIs('inward_outward.*') ? 'active' : '' }}">
                        Inward / Outward
                    </a>
                </li>
            </ul>
        </li>

        {{-- Reports parent --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('materials.manage') ? 'parent-active' : '' }}"
               data-bs-toggle="collapse" href="#reportsSub" role="button"
               aria-expanded="{{ request()->routeIs('materials.manage') ? 'true' : 'false' }}">
                Reports
                <i class="bi bi-chevron-right nav-arrow"></i>
            </a>
            <ul class="sidebar-sub collapse {{ request()->routeIs('materials.manage') ? 'show' : '' }}" id="reportsSub">
                <li class="nav-item">
                    <a href="{{ route('materials.manage') }}"
                       class="nav-link {{ request()->routeIs('materials.manage') ? 'active' : '' }}">
                        Manage Materials
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
