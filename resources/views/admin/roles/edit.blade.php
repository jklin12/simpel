@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Role: {{ $role->name }}</h1>
        <p class="text-gray-500">Update role name and permissions.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                <input type="text" name="name" id="name" value="{{ $role->name }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors border px-4 py-2 text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2 border border-gray-100 rounded-lg">
                    @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                class="rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg border border-gray-300">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">Update Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
