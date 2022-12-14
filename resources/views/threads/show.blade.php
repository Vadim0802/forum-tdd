<x-app-layout>
    <div class="mx-12 grid grid-cols-12 gap-x-6">
        <div class="col-span-8">
            <div class="py-8">
                <x-thread-card :thread="$thread"/>
            </div>

            <div class="pb-12">
                <x-card>
                    <x-slot name="header">
                        <h2>Replies</h2>
                    </x-slot>

                    <x-slot name="body">
                        @forelse ($replies as $reply)
                            <div class="mb-6" @can('update', $reply) x-data="{ editing: false }" @endcan>
                                <div class="pb-4 pt-2">
                                    <p x-show="!editing">{{ $reply->body }}</p>
                                    @can('update', $reply)
                                        <div x-show="editing">
                                            <form action="{{ route('replies.update', $reply) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <textarea class="mb-4 text-sm w-full rounded-md shadow-sm border-gray-800 focus:border-gray-500 focus:ring focus:ring-gray-200 focus:ring-opacity-50" name="body" rows="5">{{ $reply->body }}</textarea>
                                                <div class="flex justify-end gap-x-2">
                                                    <button class="bg-blue-500 py-1 px-2 text-sm rounded-md text-white hover:bg-blue-600" type="submit">Update</button>
                                                    <button class="hover:underline hover:underline-offset-4 text-sm" @click.prevent="editing = false">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endcan
                                    @if ($errors->reply->has('body'))
                                        <x-alert class="bg-red-400" message="{{ $errors->reply->first('body') }}"></x-alert>
                                    @endif
                                </div>

                                <span class="flex items-center justify-end gap-x-2 text-right border-b border-b-gray-800 text-xs pb-2">
                                    <img src="{{ $reply->owner->avatar() }}" alt="Owner profile image" class="w-10 h-10 rounded-md">
                                    <span>
                                        <a href="{{ route('profiles.show', $reply->owner) }}" class="font-semibold hover:text-gray-900 hover:underline hover:underline-offset-4">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}
                                    </span>

                                    <span class="border-r border-gray-700">&nbsp</span>

                                    @auth
                                        @if ($reply->favorited)
                                            <form action="{{ route('favorites.destroy', ['reply' => $reply, 'favorite' => $reply->favorite_by_user]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="flex justify-center items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 fill-black @if($reply->favorited) hover:fill-white  @endif">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('favorites.store', $reply) }}" method="POST">
                                                @csrf

                                                <button type="submit" class="flex justify-center items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hover:fill-black @if($reply->favorited) fill-black  @endif">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                    @guest
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                        </svg>
                                    @endguest
                                    <span>{{ $reply->favorites_count }}</span>

                                    @can('delete', $reply)
                                        <span class="border-r border-gray-700">&nbsp</span>

                                        <form action="{{ route('replies.destroy', $reply) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hover:stroke-red-700">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan

                                    @can('update', $reply)
                                        <span class="border-r border-gray-700">&nbsp</span>

                                        <button class="flex items-center justify-center" @click="editing = true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hover:stroke-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                    @endcan
                                </span>
                            </div>
                        @empty
                            <div class="flex justify-center mb-8">
                                <h2 class="underline underline-offset-8">Be the first to reply in this thread!</h2>
                            </div>
                        @endforelse
                        {{ $replies->links() }}

                        @auth
                            <x-reply-form :thread="$thread"/>
                        @else
                            <div class="flex justify-center">
                                <div class="h-8 px-4 py-2 mx-auto bg-blue-600 text-white text-sm inline-flex items-center rounded-xl">
                                    <h2>Please <a href="{{ route('login') }}" class="underline underline-offset-4">sign in</a> to participate in this discussion</h2>
                                </div>
                            </div>
                        @endauth
                    </x-slot>
                </x-card>
            </div>
        </div>

        <div class="py-8 col-span-4">
            <x-card>
                <x-slot name="header">
                    <h2>Information</h2>
                </x-slot>

                <x-slot name="body">
                    <p>
                        This thread was published {{ $thread->created_at->diffForHumans() }} by
                        <a href="{{ route('profiles.show', $thread->creator) }}" class="underline underline-offset-4">{{ $thread->creator->name }}</a>,
                        and currently has {{ $replies->count() }} {{ Str::plural('reply', $replies->count()) }}

                    </p>
                    @auth
                        @if (! $thread->subscriptions->pluck('user_id')->contains(auth()->id()))
                            <form action="{{ route('threads.subscriptions.store', $thread) }}" method="POST">
                                @csrf
                                <div class="inline-flex border border-gray-900 rounded-md px-4 py-2 mt-4 bg-gray-900 text-white hover:bg-white hover:text-black">
                                    <button type="submit" class="flex items-center justify-center gap-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                        </svg>
                                        <span>Subscribe</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            @can('delete', $thread->subscriptions->firstWhere('user_id', auth()->id()))
                                <form action="{{ route('threads.subscriptions.destroy', $thread) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="inline-flex border border-gray-900 rounded-md px-4 py-2 mt-4 bg-white text-black hover:bg-gray-900 hover:text-white">
                                        <button type="submit" class="flex items-center justify-center gap-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                            </svg>
                                            <span>Unsubscribe</span>
                                        </button>
                                    </div>
                                </form>
                            @endcan
                        @endif
                    @endauth
                </x-slot>
            </x-card>
        </div>
    </div>
</x-app-layout>
