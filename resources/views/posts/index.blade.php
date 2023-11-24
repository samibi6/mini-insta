<x-app-layout>
    <h1 class="font-bold text-xl mb-4">Liste des posts</h1>

    <form action="{{ route('posts.index') }}" method="GET" class="mb-4">
        <div class="flex items-center">
            <input type="text" name="search" id="search" placeholder="Rechercher un article"
                class="flex-grow border border-gray-300 rounded shadow px-4 py-2 mr-4" value="{{ request()->search }}"
                autofocus />
            <button type="submit" class="bg-white text-gray-600 px-4 py-2 rounded-lg shadow">
                <x-heroicon-o-magnifying-glass class="h-5 w-5" />
            </button>
        </div>
    </form>

    <ul class="grid sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-8">
        @foreach ($posts as $post)
            <li>
                <x-post-card :post="$post" />
            </li>
        @endforeach
    </ul>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</x-app-layout>
