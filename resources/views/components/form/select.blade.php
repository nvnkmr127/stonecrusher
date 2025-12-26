<div class="mb-3">
    <label class="form-label {{ $required ? 'required' : '' }}" for="{{ $name }}">{{ $label }}</label>
    <select class="form-select @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $attributes }}>
        <option value="">Select {{ $label }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>