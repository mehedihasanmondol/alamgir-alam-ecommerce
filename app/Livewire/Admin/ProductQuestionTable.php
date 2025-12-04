<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use App\Modules\Ecommerce\Product\Services\ProductQuestionService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: Product Question Table Livewire Component
 * Purpose: Admin table for managing product questions with filters and pagination
 * 
 * Key Methods:
 * - render(): Display questions with filters
 * - approve(): Approve a question
 * - reject(): Reject a question
 * - deleteQuestion(): Delete a question
 * 
 * Dependencies:
 * - ProductQuestionService
 * 
 * @category Livewire
 * @package  Admin
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductQuestionTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $questionToDelete = null;
    public $enableQna = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->enableQna = SiteSetting::get('enable_product_qna', '1') === '1';
    }

    public function toggleEnableQna()
    {
        $this->enableQna = !$this->enableQna;
        SiteSetting::set('enable_product_qna', $this->enableQna ? '1' : '0');
        
        session()->flash('success', 'Product Q&A ' . ($this->enableQna ? 'enabled' : 'disabled') . ' successfully!');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function approve($questionId, ProductQuestionService $service)
    {
        try {
            $service->approveQuestion($questionId);
            session()->flash('success', 'Question approved successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve question: ' . $e->getMessage());
        }
    }

    public function reject($questionId, ProductQuestionService $service)
    {
        try {
            $service->rejectQuestion($questionId);
            session()->flash('success', 'Question rejected successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject question: ' . $e->getMessage());
        }
    }

    public function confirmDelete($questionId)
    {
        $this->questionToDelete = $questionId;
        $this->showDeleteModal = true;
    }

    public function deleteQuestion(ProductQuestionService $service)
    {
        if ($this->questionToDelete) {
            try {
                $service->deleteQuestion($this->questionToDelete);
                session()->flash('success', 'Question deleted successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete question: ' . $e->getMessage());
            }
        }

        $this->showDeleteModal = false;
        $this->questionToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = ProductQuestion::with(['product', 'user', 'answers']);

            // Search filter
            if ($this->search) {
                $query->where('question', 'like', '%' . $this->search . '%');
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('status', $this->statusFilter);
            }

            // Sorting
            $query->orderBy($this->sortBy, $this->sortOrder);

            $questions = $query->paginate($this->perPage);

            return view('livewire.admin.product-question-table', [
                'questions' => $questions,
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductQuestionTable render error: ' . $e->getMessage());
            return view('livewire.admin.product-question-table', [
                'questions' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
