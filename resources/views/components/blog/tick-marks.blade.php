@props(['post'])

@if($post->tickMarks && $post->tickMarks->count() > 0)
<div class="flex flex-wrap items-center gap-2">
    @foreach($post->tickMarks as $tickMark)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tickMark->bg_color }}; color: {{ $tickMark->text_color }};">
            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                {!! $tickMark->getIconHtml() !!}
            </svg>
            {{ $tickMark->label }}
        </span>
    @endforeach
</div>
@endif
