@extends('layouts.app')

@section('title', 'OLT Locations')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-900">OLT Location Management</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.olt.index') }}" class="flex gap-3">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search locations..."
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="relative">
                        <i data-lucide="filter" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="state_filter" 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white min-w-[140px]">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state->state_id }}" {{ request('state_filter') == $state->state_id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Filter
                    </button>
                </form>
                <a href="{{ route('admin.olt.create') }}" 
                   class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Add New
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location Details
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Administrative
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Technical
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($oltLocations as $location)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $location->olt_location }}</div>
                            <div class="text-sm text-gray-500">Code: {{ $location->olt_location_code }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm text-gray-900">{{ $location->state->name }}</div>
                            <div class="text-sm text-gray-500">{{ $location->district->name }} • {{ $location->block->name }}</div>
                            <div class="text-sm text-gray-500">LGD: {{ $location->lgd_code }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm text-gray-900 font-mono">{{ $location->olt_ip }}</div>
                            <div class="text-sm text-gray-500">{{ $location->no_of_gps }} GPs</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $location->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.olt.show', $location) }}" 
                               class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" 
                               title="View">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.olt.edit', $location) }}" 
                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" 
                               title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.olt.destroy', $location) }}" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete {{ $location->olt_location }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" 
                                        title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12">
                        <div class="text-gray-500">No OLT locations found</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($oltLocations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $oltLocations->firstItem() }} to {{ $oltLocations->lastItem() }} of {{ $oltLocations->total() }} results
            </div>
            <div class="flex items-center space-x-2">
                {{ $oltLocations->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection