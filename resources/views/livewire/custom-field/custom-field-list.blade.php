<div class="custom-fields-list">
    <h6 class="mb-2">Custom Fields</h6>
    
    @forelse($fields as $field)
        <livewire:custom-field.custom-field-edit :field="$field" :key="'field-' . $field->id" />
    @empty
        <p class="text-muted small">No custom fields yet</p>
    @endforelse
</div>