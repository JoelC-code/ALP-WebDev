    <div wire:poll.visible.1s="refreshLists" class="container-fluid board-scroll mt-3">
        <div class="d-flex gap-3 flex-nowrap">
            @foreach($lists as $li)
            <div class="col-auto list-view">
                <div class="card shadow-sm p-2" style="width: 300px">
                    <div class="card-header bg-white fw-bold mb-3">
                        {{ $li->list_name }}
                        <livewire:boardlist.list-delete
                            :board-id="$this->board->id"
                            :list-id="$li->id"
                            :key="'cardlist-' . $li->id" />
                    </div>
                    <livewire:card.card-list :list="$li" :key="$li->id" />
                </div>
            </div>
            @endforeach
            @if(! $showCreateForm)
            <div class="card add-card shadow-sm d-flex flex-row p-2 align-items-center" style="width: 300px; height: fit-content; cursor: pointer;" wire:click="showForm">
                <p class="m-0 w-100">+ Add New List</p>
            </div>
            @else
                <livewire:boardlist.list-create :board="$board" />
            @endif
        </div>
    </div>