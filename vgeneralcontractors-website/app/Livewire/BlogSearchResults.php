<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class BlogSearchResults extends Component
{
    use WithPagination;

    public $query = '';
    public $isSearching = false;
    
    protected $listeners = [
        'searchTermUpdated' => 'updateSearchTerm', 
        'searchCleared' => 'clearSearch'
    ];

    public function updateSearchTerm($term)
    {
        $this->query = $term;
        $this->isSearching = !empty($term);
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->isSearching = false;
        $this->resetPage();
    }

    public function render()
    {
        $posts = collect([]);
        
        if ($this->isSearching && strlen($this->query) >= 3) {
            $searchTerm = '%' . $this->query . '%';
            
            $posts = Post::where('post_status', 'published')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('post_title', 'like', $searchTerm)
                        ->orWhere('post_content', 'like', $searchTerm)
                        ->orWhere('meta_keywords', 'like', $searchTerm)
                        ->orWhere('meta_description', 'like', $searchTerm);
                })
                ->with(['category', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(9);
        } else {
            // If no search, show only published posts
            $posts = Post::where('post_status', 'published')
                ->with(['category', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(9);
        }

        return view('livewire.blog-search-results', [
            'posts' => $posts,
            'isSearching' => $this->isSearching
        ]);
    }
} 