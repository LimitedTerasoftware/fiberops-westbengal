@extends('layouts.app')

@section('title', 'View OLT Location')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">OLT Location Details</h2>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.olt.edit', $oltLocation) }}" 
                       class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ route('admin.olt.index') }}" 
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Location Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">OLT Location</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->olt_location }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Location Code</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ $oltLocation->olt_location_code }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">LGD Code</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->lgd_code }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Administrative Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Administrative Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">State</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->state->state_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">District</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->district->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Block</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->block->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">OLT IP Address</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ $oltLocation->olt_ip }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Number of GPs</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->no_of_gps }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Record Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created At</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $oltLocation->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-200">
                <form method="POST" 
                      action="{{ route('admin.olt.destroy', $oltLocation) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this OLT location?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Delete
                    </button>
                </form>
                <a href="{{ route('admin.olt.edit', $oltLocation) }}" 
                   class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit Location
                </a>
            </div>
        </div>
    </div>
</div>
@endsection