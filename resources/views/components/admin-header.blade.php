<div class="flex justify-between">
    <div class="flex items-end">
        <h2 class="text-sm leading-tight">
            {{ $navigation }}
        </h2>
    </div>
    <div class="flex items-center">
        <a href="{{ route('home') }}"><img src="{{ asset('images/image.png') }}" alt="logo"></a>
    </div>
    <div class="flex items-end">
        <h2 class="text-sm leading-tight">
            {{ $description }}
        </h2>
    </div>
</div> 