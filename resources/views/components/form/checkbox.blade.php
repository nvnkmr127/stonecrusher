<div class="mb-3">
    <label class="form-check">
        <input type="checkbox" class="form-check-input @error($name) is-invalid @enderror" id="{{ $name }}"
            name="{{ $name }}" value="1" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
        <span class="form-check-label">{{ $label }}</span>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </label>
</div>