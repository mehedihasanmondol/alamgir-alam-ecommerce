<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class YoutubeCommentTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 15;
    public bool $showFilters = false;
    public $gotoPage = null;
    public array $selected = [];
    public bool $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedGotoPage($value)
    {
        if ($value && is_numeric($value) && $value > 0) {
            $this->setPage((int) $value);
            $this->gotoPage = null;
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function approve($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        session()->flash('success', 'Comment approved successfully!');
    }

    public function reject($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'status' => 'rejected',
        ]);

        session()->flash('success', 'Comment rejected successfully!');
    }

    public function delete($id)
    {
        Feedback::findOrFail($id)->delete();
        session()->flash('success', 'Comment deleted successfully!');
    }

    public function bulkApprove()
    {
        if (empty($this->selected)) {
            return;
        }

        Feedback::whereIn('id', $this->selected)->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $this->selected = [];
        $this->selectAll = false;
        session()->flash('success', count($this->selected) . ' comments approved!');
    }

    public function bulkReject()
    {
        if (empty($this->selected)) {
            return;
        }

        Feedback::whereIn('id', $this->selected)->update([
            'status' => 'rejected',
        ]);

        $this->selected = [];
        $this->selectAll = false;
        session()->flash('success', count($this->selected) . ' comments rejected!');
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }

        Feedback::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->selectAll = false;
        session()->flash('success', count($this->selected) . ' comments deleted!');
    }

    public function render()
    {
        $query = Feedback::where('source', 'youtube');

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('feedback', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $comments = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.feedback.youtube-comment-table', [
            'comments' => $comments,
        ]);
    }
}
