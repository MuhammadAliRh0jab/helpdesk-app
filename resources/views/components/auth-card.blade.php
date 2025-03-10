@props(['title'])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow-md rounded-lg p-6']) }}>
    <div class="text-center mb-4">
        {{ $title }}
    </div>

    <div class="space-y-6">
        {{ $slot }}
    </div>
</div>