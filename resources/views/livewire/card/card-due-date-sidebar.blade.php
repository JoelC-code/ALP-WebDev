<div class="h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-clock text-primary"></i> Due Dates
        </h5>
        
        <!-- Limit Selector -->
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                {{ $limit }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" wire:click.prevent="updateLimit(3)">3</a></li>
                <li><a class="dropdown-item" href="#" wire:click.prevent="updateLimit(5)">5</a></li>
                <li><a class="dropdown-item" href="#" wire:click.prevent="updateLimit(10)">10</a></li>
                <li><a class="dropdown-item" href="#" wire:click.prevent="updateLimit(15)">15</a></li>
            </ul>
        </div>
    </div>

    <div style="max-height: calc(100vh - 200px); overflow-y: auto;">
        @if ($upcomingCards->count() > 0)
            <div class="d-flex flex-column gap-2">
                @foreach ($upcomingCards as $card)
                    @php
                        $dueDate = \Carbon\Carbon::parse($card->dates);
                        $isOverdue = $dueDate->isPast();
                        $daysUntilDue = now()->diffInDays($dueDate, false);
                        $isDueSoon = !$isOverdue && $daysUntilDue >= 0 && $daysUntilDue <= 7;
                    @endphp

                    <div class="card shadow-sm"
                        style="cursor: pointer;"
                        wire:click="$dispatch('open-card-from-sidebar', { cardId: {{ $card->id }} })">
                        <div class="card-body p-2">
                            <!-- Card Title -->
                            <div class="d-flex align-items-start gap-2 mb-1">
                                @if ($card->labels->first())
                                    <span class="badge flex-shrink-0"
                                        style="background-color: {{ $card->labels->first()->color }}; width: 8px; height: 8px; padding: 0; border-radius: 50%;">
                                    </span>
                                @endif
                                <span class="small fw-semibold flex-grow-1">{{ Str::limit($card->card_title, 35) }}</span>
                            </div>

                            <!-- List Name -->
                            <div class="small text-muted mb-1">
                                <i class="fas fa-list" style="font-size: 10px;"></i>
                                {{ $card->list->list_name }}
                            </div>

                            <!-- Due Date -->
                            <div class="mb-1">
                                <span
                                    class="badge {{ $isOverdue ? 'bg-danger' : ($isDueSoon ? 'bg-warning text-dark' : 'bg-secondary') }} small">
                                    <i class="fas fa-clock"></i>
                                    {{ $dueDate->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div class="small text-muted">
                                {{ $dueDate->diffForHumans() }}
                            </div>

                            @if ($isOverdue)
                                <div class="small text-danger mt-1">
                                    <i class="fas fa-exclamation-triangle"></i> Overdue
                                </div>
                            @elseif($isDueSoon)
                                <div class="small text-warning mt-1">
                                    <i class="fas fa-bell"></i> Due soon!
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-calendar-check fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">No upcoming due dates</p>
            </div>
        @endif
    </div>
</div>