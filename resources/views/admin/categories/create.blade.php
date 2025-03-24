<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter une catégorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf

                        <!-- Nom de la catégorie -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nom')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Statut (activé/désactivé) -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="enabled" type="checkbox" name="enabled" value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ old('enabled') ? 'checked' : '' }}>
                                <label for="enabled"
                                    class="ml-2 block text-sm text-gray-900">{{ __('Activer cette catégorie') }}</label>
                            </div>
                            <x-input-error :messages="$errors->get('enabled')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button>
                                {{ __('Ajouter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
