<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Proyek Bisnis Saya') }}
            </h2>
            <a href="{{ route('projects.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Tambah Proyek
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($projects->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            Anda belum menambahkan profil proyek bisnis.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($projects as $project)
                                <div class="border rounded-lg p-6 hover:shadow-md transition text-left">
                                    <h3 class="text-xl font-bold mb-2">{{ $project->business_name }}</h3>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $project->business_description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="text-xs text-gray-400 capitalize">{{ $project->business_type }}</span>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('projects.edit', $project) }}"
                                                class="text-sm text-blue-600 hover:text-blue-900">Edit</a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                                onsubmit="return confirm('Hapus proyek ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-sm text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
