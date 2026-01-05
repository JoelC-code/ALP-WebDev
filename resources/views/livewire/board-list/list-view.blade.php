    <div class="container-fluid board-scroll mt-3 py-3"
        style="max-height: calc(87vh - 120px); min-height: calc(87vh - 120px); overflow:auto ;background-color: #5778FD; border-end-start-radius: 8px; border-end-end-radius: 8px;">
        <div class="d-flex gap-3 flex-nowrap" id="list-sortable">
            @foreach ($lists as $li)
                <div class="col-auto list-view d-flex flex-column h-100 flex-shrink-0" data-list-id={{ $li->id }}
                    wire:key="list-{{ $li->id }}">
                    <div class="card shadow-sm p-2" style="width: 300px">
                        <div class="card-header d-flex justify-content-between bg-white fw-bold mb-3 w-100">
                            <livewire:board-list.list-rename :board="$board" :list="$li" :key="'list-rename-' . $li->id . '-' . $li->updated_at" />
                        </div>
                        <livewire:card.card-list :list="$li" :key="$li->id" />
                    </div>
                </div>
            @endforeach
            @if (!$showCreateForm)
                <div class="card add-card shadow-sm d-flex flex-row p-2 align-items-center flex-shrink-0"
                    style="width: 300px; height: fit-content; cursor: pointer;" wire:click="showForm">
                    <p class="m-0 w-100">+ Add New List</p>
                </div>
            @else
                <livewire:board-list.list-create :board="$board" />
            @endif
        </div>
    </div>