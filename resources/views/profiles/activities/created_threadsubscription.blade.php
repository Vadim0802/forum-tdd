<x-slot name="header">
    <div class="flex items-center gap-x-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <span>
            {{ $user->name }} subscribed to <a class="underline underline-offset-4" href="{{ route('threads.show', ['channel' => $record->subject->thread->channel, 'thread' => $record->subject->thread]) }}">{{ $record->subject->thread->title }}</a>
        </span>
    </div>
</x-slot>

<x-slot name="body">
    {{ $record->subject->thread->body }}
</x-slot>
