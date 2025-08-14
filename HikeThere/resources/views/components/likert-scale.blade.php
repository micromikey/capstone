@props([
    'name',
    'type' => 'standard',
    'value' => null,
    'radioClasses' => 'w-6 h-6 border-2 border-yellow-400 text-yellow-500 rounded-full focus:ring-2 focus:ring-yellow-400 transition-all duration-150',
    'labelClasses' => 'flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-3 py-2 transition-all duration-150'
])

@php
    if ($type === 'frequency') {
        $options = [
            'never' => 'Never',
            'rarely' => 'Rarely', 
            'sometimes' => 'Sometimes',
            'often' => 'Often',
            'always' => 'Always'
        ];
    } else {
        $options = [
            '5' => '5 - Strongly Agree',
            '4' => '4 - Agree',
            '3' => '3 - Neutral',
            '2' => '2 - Disagree',
            '1' => '1 - Strongly Disagree'
        ];
    }
@endphp

<div class="space-y-2">
    @foreach($options as $optionValue => $label)
        <label class="{{ $labelClasses }}">
            <input 
                type="radio" 
                name="{{ $name }}" 
                value="{{ $optionValue }}" 
                class="{{ $radioClasses }}"
                {{ $value === $optionValue ? 'checked' : '' }}
                {{ $loop->first ? 'required' : '' }}
            >
            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
        </label>
    @endforeach
</div>
