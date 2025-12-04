@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Role Management</h1>
        <a href="{{ route('admin.roles.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
            <i class="fas fa-plus mr-2"></i>Add New Role
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $role->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $role->slug }}</p>
                </div>
                <span class="px-3 py-1 text-xs rounded-full {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $role->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($role->description)
            <p class="text-gray-600 text-sm mb-4">{{ $role->description }}</p>
            @endif

            <div class="mb-4 pb-4 border-b border-gray-200">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-shield-alt mr-2"></i>
                    {{ $role->permissions->count() }} Permissions
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-users mr-2"></i>
                    {{ $role->users->count() }} Users
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.roles.edit', $role->id) }}" 
                   class="flex-1 text-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form id="delete-role-{{ $role->id }}" action="{{ route('admin.roles.destroy', $role->id) }}" 
                      method="POST" 
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                detail: { 
                                    title: 'Delete Role', 
                                    message: 'Are you sure you want to delete this role?',
                                    onConfirm: () => document.getElementById('delete-role-{{ $role->id }}').submit()
                                } 
                            }))" 
                            class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
