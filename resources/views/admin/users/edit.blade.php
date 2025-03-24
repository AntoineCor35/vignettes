<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'utilisateur') }} : {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('admin.users.index') }}"
                        class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
                        &larr; Retour à la liste des utilisateurs
                    </a>

                    <div class="mt-5">
                        <h3 class="text-lg font-medium text-gray-900">Informations de l'utilisateur</h3>
                        <div class="mt-3 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nom</p>
                                <p class="mt-1">{{ $user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="mt-1">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Modifier le rôle</h3>
                        <form method="POST" action="{{ route('admin.users.update.role', $user) }}" class="mt-5">
                            @csrf
                            @method('PATCH')

                            <div class="mt-4">
                                <x-input-label for="role" :value="__('Rôle')" />
                                <select id="role" name="role"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur
                                    </option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                        Administrateur</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('Enregistrer') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
