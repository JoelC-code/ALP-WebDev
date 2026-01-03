<?php

namespace App\Livewire\CardTemplate;

use App\Models\Board;
use App\Models\CardTemplate;
use Livewire\Component;

class CardTemplateList extends Component
{
    public $board;
    public $templates = [];
    public $showForm = false;
    public $editingTemplateId = null;

    protected $listeners = [
        'template-saved' => 'refreshTemplates',
        'template-deleted' => 'refreshTemplates',
        'template-used' => 'refreshTemplates',
    ];

    public function mount(Board $board)
    {
        $this->board = $board;
        $this->refreshTemplates();
    }

    public function refreshTemplates()
    {
        $this->templates = $this->board->cardTemplates()->with(['labels', 'customFields'])->get();
        $this->showForm = false;
        $this->editingTemplateId = null;
    }

    public function createTemplate()
    {
        $this->showForm = true;
        $this->editingTemplateId = null;
    }

    public function editTemplate($templateId)
    {
        $this->showForm = true;
        $this->editingTemplateId = $templateId;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingTemplateId = null;
    }

    public function render()
    {
        return view('livewire.card-template.card-template-list');
    }
}