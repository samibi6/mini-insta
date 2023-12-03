<div class="bg-white rounded-md shadow-md hover:shadow-lg hover:scale-105 transition">
    <a class="p-2 pl-5 rounded-md bg-white flex hover:-translate-y-1 hover:underline hover:bg-zinc-300 transition
    "
        href="{{ route('profile.show', $post->user) }}">
        <x-avatar class="h-5 w-5" :user="$post->user" />
        <div class="ml-2 flex flex-col justify-center">
            <div class="text-gray-700">{{ $post->user->username }}</div>
        </div>
    </a>
    <a class="flex flex-col h-full space-y-4 p-5 pt-0 w-full" href="{{ route('posts.show', $post) }}">
        <div>
            <img class="object-cover  w-full h-52" src="{{ Storage::url($post->img_path) }}" alt="">
        </div>
        <div class="uppercase font-bold text-gray-800">
            {{ $post->caption }}
        </div>
        <div class="flex justify-between items-center">
            <div class="text-xs text-gray-500">
                {{ $post->published_at }}
            </div>
            <div class="flex items-center space-x-2">
                <x-heroicon-o-chat-bubble-bottom-center-text class="h-5 w-5 text-gray-500" />
                <div class="text-sm text-gray-500">{{ $post->comments_count }}</div>
            </div>
            <div class="flex items-center space-x-2">
                <x-heroicon-o-heart class="h-5 w-5 text-gray-500" />
                <div class="text-sm text-gray-500">{{ $post->likes_count }}</div>
            </div>
            <div class="flex justify-center">
                @can('delete', $post)
                    <button x-data="{ id: {{ $post->id }} }"
                        x-on:click.prevent="window.selected = id; $dispatch('open-modal', 'confirm-post-deletion');"
                        type="submit" class="font-bold bg-white text-gray-700 px-4 py-2 rounded shadow">
                        <x-heroicon-o-trash class="h-5 w-5" />
                    </button>
                @endcan
            </div>
        </div>
    </a>
</div>
<x-modal name="confirm-post-deletion" focusable>
    <form method="post" onsubmit="event.target.action= '/posts/' + window.selected" class="p-6">
        @csrf @method('DELETE')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Êtes-vous sûr de vouloir supprimer ce commentaire ?
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Cette action est irréversible. Toutes les données seront supprimées.
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                Annuler
            </x-secondary-button>

            <x-danger-button class="ml-3" type="submit">
                Supprimer
            </x-danger-button>
        </div>
    </form>
</x-modal>
