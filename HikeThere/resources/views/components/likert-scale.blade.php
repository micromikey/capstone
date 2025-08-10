@props([
    'name',
    'type' => 'standard',
    'radioClasses' => 'w-4 h-4 border-2 border-gray-300 text-blue-600 rounded-full focus:ring-2 focus:ring-blue-500 transition-all duration-150',
    'labelClasses' => 'flex items-center space-x-3 cursor-pointer hover:bg-gray-50 rounded px-2 py-1.5 transition-all duration-150'
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
    @foreach($options as $value => $label)
        <label class="{{ $labelClasses }}">
            <input 
                type="radio" 
                name="{{ $name }}" 
                value="{{ $value }}" 
                class="{{ $radioClasses }}"
                required
            >
            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
        </label>
    @endforeach
</div>
