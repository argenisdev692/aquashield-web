<?php

namespace App\Livewire;

use Livewire\Component;

class BlogSearch extends Component
{
    public $query = '';
    protected $queryString = ['query' => ['except' => '']];

    public function updatedQuery()
    {
        // Emit event to the results component
        if (strlen($this->query) >= 3) {
            $this->dispatch('searchTermUpdated', $this->query);
        } else if (empty($this->query)) {
            $this->dispatch('searchTermUpdated', '');
        }
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->dispatch('searchTermUpdated', '');
        $this->dispatch('searchCleared');
    }

    public function render()
    {
        return view('livewire.blog-search');
    }
}
