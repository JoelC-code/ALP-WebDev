<header class="w-100 fixed-top d-flex flex-row justify-content-between align-items-center p-3" style="background-color: #f4f4f4; z-index: 10;">
    @auth
        <a href="/dashboard" class="text-decoration-none d-flex flex-row h-100 align-items-center">
            <img src="/images/StackBoard.png" width="50" height="50" class="me-2" style="border-radius: 10px" />
            <h4 class="fw-bold mb-0 mt-0" style="color: #1800AD">StackBoard</h4>
        </a>

        @if (!request()->routeIs('board.search'))
            <div class="py-2 gap-2 align-items-center d-flex justify-content-center">
                <form action="/search-board" method="get" id="searchForm" class="search-container d-flex flex-row gap-2">
                    <input type="text" placeholder="Search A Board" name="searchBoard"
                        class="form-control d-none d-md-block rounded">
                    <button class="search-btn btn btn-primary">Search</button>
                </form>
            </div>
        @endif
        <div class="dropdown" wire:ignore>
            <button class="btn btn-link dropdown-toggle text-dark" type="button" id="userDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="position: relative; z-index: 20"
                aria-labelledby="userDropdown">
                <li class="dropdown-item-text">
                    <strong>Invite ID:</strong> {{ auth()->user()->invite_id }}
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form method="POST" action="/logout">
                        @csrf
                        <button class="dropdown-item text-danger">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    @endauth

    @guest
        <a href="/" class="text-decoration-none d-flex z-1 flex-row h-100 align-items-center">
            <img src="/images/StackBoard.png" width="50" height="50" class="me-2" style="border-radius: 10px" />
            <h4 class="fw-bold mb-0 mt-0" style="color: #1800AD">StackBoard</h4>
        </a>
    @endguest
</header>
