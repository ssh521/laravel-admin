@props(['value', 'selected' => false])

<option value="{{ $value }}" {{ $selected ? 'selected' : '' }} {{ $attributes->except(['selected']) }}>
    {{ $slot }}
</option>

