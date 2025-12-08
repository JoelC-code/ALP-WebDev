<div class="container workspace-section justify-content-start">
    <h3>Your Workspace</h3>
    <div class="board-cards flex flex-warp gap-2">

        @foreach ($myWorkspaces as $board)
            <div class="card w-25 h-25 p-4 pt-5 bg-card-color shadow rounded justify-content-between d-flex flex-row align-items-start">
                <a href="{{ route('board.access', $board->id) }}" class="text-decoration-none">{{ $board->board_name }}</a>

                <livewire:board.board-delete 
                    :board-id="$board->id" 
                    :key="'m-'.$board->id" />
            </div>
        @endforeach

        <a href="/board-create"
           class="card w-25 p-4 pt-5 bg-white shadow rounded flex items-center justify-center cursor-pointer hover:bg-gray-100 transition no-underline">
            <h4 class="fw-bold text-center">Make a<br>Board</h4>
        </a>

    </div>

    <br>

    <h3>Other Workspace</h3>
    <div class="board-cards flex flex-warp gap-2">

        @forelse ($otherWorkspaces as $board)
            <div class="card p-4 pt-5 bg-card-color shadow rounded justify-content-between d-flex flex-row align-items-start">
                <a class="text-decoration-none">{{ $board->board_name }}</a>

                {{-- FIX #1: Replace WRONG call delete($id) --}}
                <livewire:board.board-delete
                    :board-id="$board->id"
                    :key="'o-'.$board->id" />

            </div>
        @empty
            <p>Ask your friends to invite you!</p>
        @endforelse

    </div>
</div>
