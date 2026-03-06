@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-700 text-sm">
            ← Back to Users
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
        <p class="text-sm text-gray-600 mt-1">Update user information and role</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 max-w-2xl">
        <div class="p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-gray-500 text-xs mt-1">Leave blank to keep current password</p>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>Client</option>
                        <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="guru" {{ old('role', $user->role) === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        Update User
                    </button>
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
