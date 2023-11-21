<x-guest-layout>
    <div class="m-auto w-1/2">
        <a class="my-4" href="/">Retour</a>
        <h1 class="font-bold text-xl mb-4">{{ $post->caption }}</h1>
        <div>
            <img class="object-cover w-full" src="{{ Storage::url($post->img_path) }}" alt="">
        </div>
        <div class="mb-4 text-xs text-gray-500">
            {{ $post->published_at }}
        </div>
        <div>
            {!! \nl2br($post->caption) !!}
        </div>
        <div class="flex mt-8">
            <x-avatar class="h-20 w-20" :user="$post->user" />
            <div class="ml-4 flex flex-col justify-center">
                <div class="text-gray-700">{{ $post->user->username }}</div>
                <div class="text-gray-500">{{ $post->user->email }}</div>
            </div>
        </div>
    </div>
</x-guest-layout>
