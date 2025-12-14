<div class="card-sortable" data-list-id="{{ $list->id }}">
    @foreach ($cards as $ca)
        <div class="card mb-2 shadow-sm card-items" data-card-id="{{ $ca->id }}" wire:key="card-{{ $ca->id }}">
            <div class="card-body p-2">{{ $ca->card_title }}</div>
        </div>
    @endforeach

    <div wire:ignore.self>
    @if (! $showCreateCardForm)
        <button class="btn add-card btn-sm btn-outline-primary w-100 mt-2 no-sort" wire:click="showForm">Add Card</button>
    @else
        <livewire:card.card-create :list="$list" :key="'card-create-' . $list->id" />
    @endif
    </div>
</div>
