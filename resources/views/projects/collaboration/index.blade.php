@extends('layouts.client')

@section('title', 'Team Management - ' . $project->business_name)

@section('content')
<div class="p-6" x-data="teamManagement()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('projects.index') }}" class="hover:text-gray-700">Projects</a>
                <span>/</span>
                <a href="{{ route('projects.show', $project) }}" class="hover:text-gray-700">{{ $project->business_name }}</a>
                <span>/</span>
                <span class="text-gray-900">Team Management</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-900">Team Management</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola anggota tim dan permission untuk project ini</p>
        </div>
        @if($project->canUserApprove(auth()->user()))
        <button @click="showInviteModal = true" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Invite Member
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Members -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Project Owner -->
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600">{{ substr($project->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $project->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $project->user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Owner</span>
                        </div>
                    </div>

                    <!-- Team Members -->
                    @foreach($members as $member)
                    @if($member->isAccepted())
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">{{ substr($member->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $member->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->user->email }}</p>
                                <p class="text-xs text-gray-400">Joined {{ $member->joined_at?->diffForHumans() ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($project->canUserApprove(auth()->user()))
                            <select @change="updateMemberRole({{ $member->id }}, $event.target.value)" 
                                    class="text-xs border border-gray-300 rounded px-2 py-1">
                                <option value="viewer" {{ $member->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                <option value="editor" {{ $member->role === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button @click="removeMember({{ $member->id }})" 
                                    class="text-red-600 hover:text-red-700 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">{{ ucfirst($member->role) }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endforeach

                    @if($members->where('status', 'accepted')->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada anggota tim</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Invitations -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pending Invitations</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($pendingInvitations as $invitation)
                    <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-yellow-600">{{ substr($invitation->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $invitation->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $invitation->user->email }}</p>
                                <p class="text-xs text-gray-400">Invited {{ $invitation->invited_at?->diffForHumans() ?? '-' }} by {{ $invitation->invitedBy?->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">{{ ucfirst($invitation->role) }}</span>
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Pending</span>
                        </div>
                    </div>
                    @endforeach

                    @if($pendingInvitations->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada undangan pending</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Role Permissions Info -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h4 class="text-sm font-semibold text-gray-900 mb-3">Role Permissions</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <h5 class="font-medium text-gray-900 mb-2">👑 Admin</h5>
                <ul class="text-gray-600 space-y-1">
                    <li>• Create & edit content</li>
                    <li>• Approve/reject content</li>
                    <li>• Manage team members</li>
                    <li>• View all content</li>
                </ul>
            </div>
            <div>
                <h5 class="font-medium text-gray-900 mb-2">✏️ Editor</h5>
                <ul class="text-gray-600 space-y-1">
                    <li>• Create & edit content</li>
                    <li>• Submit for review</li>
                    <li>• Comment on content</li>
                    <li>• View approved content</li>
                </ul>
            </div>
            <div>
                <h5 class="font-medium text-gray-900 mb-2">👁️ Viewer</h5>
                <ul class="text-gray-600 space-y-1">
                    <li>• View approved content</li>
                    <li>• Comment on content</li>
                    <li>• View team members</li>
                    <li>• Read-only access</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Invite Member Modal -->
    <div x-show="showInviteModal" x-cloak 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showInviteModal = false">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite Team Member</h3>
            
            <form @submit.prevent="inviteMember()">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" x-model="inviteForm.email" required
                           placeholder="member@example.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select x-model="inviteForm.role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="viewer">Viewer - Read-only access</option>
                        <option value="editor">Editor - Can create & edit content</option>
                        <option value="admin">Admin - Full access except project settings</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                    <textarea x-model="inviteForm.message" rows="3"
                              placeholder="Personal message for the invitation..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" @click="showInviteModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="inviteLoading"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        <span x-show="!inviteLoading">Send Invitation</span>
                        <span x-show="inviteLoading">Sending...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function teamManagement() {
    return {
        showInviteModal: false,
        inviteLoading: false,
        inviteForm: {
            email: '',
            role: 'viewer',
            message: ''
        },

        async inviteMember() {
            this.inviteLoading = true;
            
            try {
                const response = await fetch(`/projects/{{ $project->id }}/team/invite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.inviteForm)
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    this.showInviteModal = false;
                    this.inviteForm = { email: '', role: 'viewer', message: '' };
                    location.reload(); // Refresh to show new invitation
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Invite error:', error);
                alert('❌ Terjadi kesalahan saat mengirim undangan');
            } finally {
                this.inviteLoading = false;
            }
        },

        async updateMemberRole(memberId, newRole) {
            try {
                const response = await fetch(`/projects/{{ $project->id }}/team/${memberId}/role`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ role: newRole })
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ Role berhasil diupdate');
                } else {
                    alert('❌ ' + data.message);
                    location.reload(); // Refresh to revert changes
                }
            } catch (error) {
                console.error('Update role error:', error);
                alert('❌ Terjadi kesalahan saat mengupdate role');
                location.reload();
            }
        },

        async removeMember(memberId) {
            if (!confirm('Yakin ingin menghapus anggota ini dari tim?')) {
                return;
            }

            try {
                const response = await fetch(`/projects/{{ $project->id }}/team/${memberId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Remove member error:', error);
                alert('❌ Terjadi kesalahan saat menghapus anggota');
            }
        }
    }
}
</script>
@endsection