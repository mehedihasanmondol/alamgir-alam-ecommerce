@extends('layouts.admin')

@section('title', 'View Contact Message')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.contact.messages.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h6 class="mb-0">Contact Message</h6>
                        </div>
                        <div>
                            @if($message->status == 'unread')
                                <span class="badge bg-gradient-info">Unread</span>
                            @elseif($message->status == 'read')
                                <span class="badge bg-gradient-secondary">Read</span>
                            @elseif($message->status == 'replied')
                                <span class="badge bg-gradient-success">Replied</span>
                            @else
                                <span class="badge bg-gradient-dark">Archived</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Message Details -->
                    <div class="mb-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">From</label>
                                <p class="text-sm font-weight-bold mb-0">{{ $message->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Email</label>
                                <p class="text-sm mb-0">
                                    <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                </p>
                            </div>
                        </div>

                        @if($message->phone)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Phone</label>
                                <p class="text-sm mb-0">
                                    <a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Date</label>
                                <p class="text-sm mb-0">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @else
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Date</label>
                                <p class="text-sm mb-0">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Subject</label>
                            <p class="text-sm font-weight-bold mb-0">{{ $message->subject }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">Message</label>
                            <div class="p-3 bg-gray-100 rounded">
                                <p class="text-sm mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">IP Address</label>
                                <p class="text-xs text-secondary mb-0">{{ $message->ip_address ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder">User Agent</label>
                                <p class="text-xs text-secondary mb-0">{{ Str::limit($message->user_agent ?? 'N/A', 50) }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Update Status Form -->
                    <form action="{{ route('admin.contact.messages.update-status', $message) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Update Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="unread" {{ $message->status == 'unread' ? 'selected' : '' }}>Unread</option>
                                    <option value="read" {{ $message->status == 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="replied" {{ $message->status == 'replied' ? 'selected' : '' }}>Replied</option>
                                    <option value="archived" {{ $message->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="admin_note" class="form-label">Admin Note (Internal)</label>
                            <textarea 
                                name="admin_note" 
                                id="admin_note" 
                                class="form-control" 
                                rows="3"
                                placeholder="Add internal notes about this message..."
                            >{{ old('admin_note', $message->admin_note) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <form action="{{ route('admin.contact.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i>Delete Message
                                </button>
                            </form>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.contact.messages.index') }}" class="btn btn-secondary">Back to List</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-envelope me-1"></i>Reply via Email
                        </a>
                        @if($message->phone)
                        <a href="tel:{{ $message->phone }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-phone me-1"></i>Call
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
