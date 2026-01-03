<div>
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="fas fa-layer-group"></i> Card Templates</h6>
            @if (!$showForm)
                <button type="button" class="btn btn-sm btn-primary" wire:click="createTemplate">
                    <i class="fas fa-plus"></i> New Template
                </button>
            @endif
        </div>

        @if ($showForm)
            <!-- Template Form -->
            <livewire:card-template.card-template-create :board="$board" :templateId="$editingTemplateId" :key="'template-form-' . ($editingTemplateId ?? 'new')" />
        @else
            <!-- Template List -->
            @if ($templates->count() > 0)
                <div class="row g-3">
                    @foreach ($templates as $template)
                        <div class="col-md-6" wire:key="template-{{ $template->id }}">
                            <div class="card h-100">
                                @if ($template->image)
                                    <img src="{{ Storage::url($template->image) }}" class="card-img-top"
                                        alt="{{ $template->card_title }}" style="height: 150px; object-fit: cover;">
                                @endif

                                <div class="card-body">
                                    <h6 class="card-title">{{ $template->card_title }}</h6>

                                    @if ($template->description)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($template->description, 100) }}
                                        </p>
                                    @endif

                                    @if ($template->dates)
                                        <div class="mb-2">
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($template->dates)->format('M d, Y') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($template->labels->count() > 0)
                                        <div class="mb-2">
                                            @foreach ($template->labels as $label)
                                                <span class="badge me-1" style="background-color: {{ $label->color }};">
                                                    {{ $label->title }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($template->customFields->count() > 0)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-list"></i>
                                                {{ $template->customFields->count() }} custom field(s)
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-footer bg-white border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="editTemplate({{ $template->id }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <div class="d-flex gap-2">
                                            <livewire:card-template.card-template-use :board="$board"
                                                :template="$template" :key="'template-use-' . $template->id" />

                                            <livewire:card-template.card-template-delete :template="$template"
                                                :key="'template-delete-' . $template->id" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No templates yet. Create your first template to reuse card
                    configurations!
                </div>
            @endif
        @endif
    </div>
</div>
