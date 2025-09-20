@props(['label' => '', 'name' => '', 'type' => 'text'])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
           {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200']) }}>
</div>
