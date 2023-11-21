<div>
    <a class="flex flex-col h-full space-y-4 bg-white rounded-md shadow-md p-5 w-full hover:shadow-lg hover:scale-105 transition"
        href="{{ route('posts.show', $post) }}">
        <div>
            <img class="object-cover  w-full h-52" src="{{ Storage::url($post->img_path) }}" alt="">
        </div>
        <div class="uppercase font-bold text-gray-800">
            {{ $post->caption }}
        </div>
        <div class="text-xs text-gray-500">
            {{ $post->published_at }}
        </div>
    </a>
</div>
