@props(['name', 'type' => 'agreement'])

@php
    $options = match($type) {
        'frequency' => [
            'never' => 'Never',
            'rarely' => 'Rarely',
            'sometimes' => 'Sometimes',
            'often' => 'Often',
            'very_often' => 'Very Often',
        ],
        default => [
            'strongly_disagree' => 'Strongly Disagree',
            'disagree' => 'Disagree',
            'neutral' => 'Neutral',
            'agree' => 'Agree',
            'strongly_agree' => 'Strongly Agree',
        ],
    };
@endphp

<div class="flex flex-col space-y-1 mt-1">
    @foreach ($options as $value => $label)
        <label class="inline-flex items-center space-x-2">
            <input type="radio" name="{{ $name }}" value="{{ $value }}" class="h-4 w-4" required>
            <span class="text-sm">{{ $label }}</span>
        </label>
    @endforeach
</div>
