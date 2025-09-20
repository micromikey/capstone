@props(['id', 'name', 'value', 'label' => '', 'checked' => false])

<label for="{{ $id }}" class="flex items-center space-x-2 cursor-pointer">
    <input
        type="radio"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
    >
    <span class="text-gray-700">{{ $label }}</span>
</label>
