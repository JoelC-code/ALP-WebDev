<div class="container workspace-section justify-content-start">
    <p class="mb-2 fw-semibold">Your Workspace</p>
    <div class="board-cards d-grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">

        @foreach ($myWorkspaces as $board)
            <div class="card p-4 bg-card-color shadow rounded justify-content-between d-flex flex-row align-items-center gap-4">
                <a href="{{ route('board.access', $board->id) }}" class="text-truncate text-decoration-none">{{ $board->board_name }}</a>

                <livewire:board.board-delete 
                    :board-id="$board->id" 
                    :key="'m-'.$board->id" />
            </div>
        @endforeach

        <a href="/board-create"
           class="card p-4 bg-white shadow rounded flex items-center justify-center cursor-pointer hover:bg-gray-100 transition no-underline">
            <h4 class="fw-bold text-center">Make a Board</h4>
        </a>
    </div>

    <br>

    <p class="mb-2 fw-semibold">Other Workspace</p>
    <div class="board-cards d-grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">

        @forelse ($otherWorkspaces as $board)
            <div class="card p-4 bg-card-color shadow rounded justify-content-between d-flex flex-row align-items-center gap-4">
                <a href="{{ route('board.access', $board->id) }}" class="text-decoration-none text-truncate">{{ $board->board_name }}</a>

                <livewire:board.board-disconnect
                    :board-id="$board->id"
                    :key="'o-'.$board->id" />

            </div>
        @empty
            <p>Ask your friends to invite you!</p>
        @endforelse

    </div>
</div>
