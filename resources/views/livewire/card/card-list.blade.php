<div>
    @foreach ($cards as $ca)
        <div class="card mb-2 shadow-sm">
            <div class="card-body p-2">{{ $ca->card_title }}</div>
        </div>
    @endforeach

    @if (! $showCreateCardForm)
        <button class="btn btn-sm btn-outline-primary w-100 mt-2" wire:click="showForm">Add Card</button>
    @else
        <livewire:card.card-create :list="$list" :key="'card-create-' . $list->id" />
    @endif
</div>
