        <aside id="sidebar" class="p-3 border-end bg-light">
            <h5 class="mb-3">Inbox</h5>
            <small class="text-muted">For this board only</small>

            <div class="inbox-form mb-3 d-flex flex-row gap-1">
                <input type="text" class="form-control w-75" placeholder="Inbox Title" wire:model.defer="inboxName">
                <button class="btn btn-primary" wire:click="addInbox">Add</button>
            </div>

            <div class="inbox-list d-flex flex-column gap-2" wire:ignore id="inboxSortable">
                @forelse($inboxes as $inbox)
                    <div class="card inbox-card" data-id="{{ $inbox['id'] }}" wire:key="inbox-{{ $inbox['id'] }}">
                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                            <p class="fw-medium">
                                {{ $inbox['title'] }}
                            </p>
                            <button
                                class="btn text-muted position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent"
                                style="z-index: 999" wire:click="removeInbox('{{ $inbox['id'] }}')">✕</button>
                        </div>
                    </div>
                @empty
                <div class="justify-content-center align-items-center">
                    <p class="text-muted">No Inbox</p>
                </div>
                @endforelse
            </div>
        </aside>
