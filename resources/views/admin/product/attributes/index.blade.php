@extends('layouts.admin')

@section('title', 'Product Attributes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Attributes</h1>
            <p class="text-sm text-gray-600 mt-1">Manage attributes for product variations</p>
        </div>
        <a href="{{ route('admin.attributes.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add Attribute
        </a>
    </div>

    @if($attributes->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No attributes yet</h3>
        <p class="text-gray-500 mb-6">Create your first attribute to enable product variations</p>
        <a href="{{ route('admin.attributes.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create First Attribute
        </a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Attribute
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Values
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Settings
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attributes as $attribute)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $attribute->name }}</div>
                                <div class="text-xs text-gray-500">{{ $attribute->slug }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $attribute->type === 'select' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $attribute->type === 'color' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $attribute->type === 'button' ? 'bg-green-100 text-green-800' : '' }}">
                                <i class="fas fa-{{ $attribute->type === 'select' ? 'list' : ($attribute->type === 'color' ? 'palette' : 'square') }} mr-1"></i>
                                {{ ucfirst($attribute->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($attribute->values->take(5) as $value)
                                @if($attribute->type === 'color')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs border border-gray-200">
                                    <span class="w-3 h-3 rounded-full mr-1 border border-gray-300" 
                                          style="background-color: {{ $value->color_code }}"></span>
                                    {{ $value->value }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                    {{ $value->value }}
                                </span>
                                @endif
                                @endforeach
                                @if($attribute->values->count() > 5)
                                <span class="text-xs text-gray-500">+{{ $attribute->values->count() - 5 }} more</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2">
                                @if($attribute->is_visible)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    <i class="fas fa-eye mr-1"></i> Visible
                                </span>
                                @endif
                                @if($attribute->is_variation)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    <i class="fas fa-code-branch mr-1"></i> Variation
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.attributes.edit', $attribute) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="delete-attribute-{{ $attribute->id }}" action="{{ route('admin.attributes.destroy', $attribute) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                            detail: { 
                                                title: 'Delete Attribute', 
                                                message: 'Are you sure you want to delete this attribute?',
                                                onConfirm: () => document.getElementById('delete-attribute-{{ $attribute->id }}').submit()
                                            } 
                                        }))"
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
