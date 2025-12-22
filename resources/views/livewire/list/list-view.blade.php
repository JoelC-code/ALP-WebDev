    <div class="container-fluid board-scroll mt-3">
        <div class="d-flex gap-3 flex-nowrap" id="list-sortable">
            @foreach($lists as $li)
            <div class="col-auto list-view d-flex flex-column h-100 flex-shrink-0" data-list-id={{ $li->id }} wire:key="list-{{ $li->id }}">
                <div class="card shadow-sm p-2" style="width: 300px">
                    <div class="card-header d-flex justify-content-between bg-white fw-bold mb-3 w-100">
                        <livewire:boardlist.list-rename 
                            :board="$board" 
                            :list="$li" 
                            :key="'list-rename-' . $li->id . '-' . $li->updated_at" />
                    </div>
                    <livewire:card.card-list :list="$li" :key="$li->id" />
                </div>
            </div>
            @endforeach
            @if(! $showCreateForm)
            <div class="card add-card shadow-sm d-flex flex-row p-2 align-items-center flex-shrink-0" style="width: 300px; height: fit-content; cursor: pointer;" wire:click="showForm">
                <p class="m-0 w-100">+ Add New List</p>
            </div>
            @else
                <livewire:boardlist.list-create :board="$board" />
            @endif
        </div>
    </div>