<header class="w-100 fixed-top d-flex flex-row justify-content-between bg-white align-items-center p-3 z-2">
    <a href="/dashboard" class="text-decoration-none d-flex z-1 flex-row h-100 align-items-center">
        <img src="/images/StackBoard.png" width="50" height="50" class="me-2" style="border-radius: 10px"/>
        <h4 class="fw-bold mb-0 mt-0" style="color: #1800AD">StackBoard</h4>
    </a>
    <div class="py-2 gap-2 align-items-center d-none z-2 justify-content-center">
        <div class="search-container">
            <input type="text" placeholder="Search A Board" name="board-search" class="form-control">
        </div>
        <button class="search-btn btn btn-primary">Search</button>
    </div>
    <div class="dropdown" wire:ignore>
        <button class="btn btn-link dropdown-toggle text-dark" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            {{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="position: relative; z-index: 9999" aria-labelledby="userDropdown">
            <li class="dropdown-item-text">
                <strong>Invite ID:</strong> {{ auth()->user()->invite_id }}
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="dropdown-item text-danger">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</header>