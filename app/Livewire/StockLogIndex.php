<?php

namespace App\Livewire;

use App\Models\StockLog;
use Livewire\Component;
use Livewire\WithPagination;

class StockLogIndex extends Component
{
    use WithPagination;

    // Filter parameters
    public $search = '';
    public $filterType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = StockLog::with(['product.category', 'user']);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        $stockLogs = $query->latest()->paginate(15);

        return view('livewire.stock-log-index', [
            'stockLogs' => $stockLogs,
        ])->layout('layouts.app');
    }
}
