@extends('layouts.admin')

@section('title', 'Create FAQ')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.contact.faqs.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h6 class="mb-0">Create New FAQ</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.faqs.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('question') is-invalid @enderror" 
                                id="question" 
                                name="question" 
                                value="{{ old('question') }}"
                                required
                            >
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('answer') is-invalid @enderror" 
                                id="answer" 
                                name="answer" 
                                rows="5"
                                required
                            >{{ old('answer') }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">Display Order</label>
                                <input 
                                    type="number" 
                                    class="form-control @error('order') is-invalid @enderror" 
                                    id="order" 
                                    name="order" 
                                    value="{{ old('order', 0) }}"
                                >
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="is_active" 
                                        name="is_active"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.contact.faqs.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create FAQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
