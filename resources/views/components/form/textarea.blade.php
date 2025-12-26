<div class="mb-3">
    <label class="form-label {{ $required ? 'required' : '' }}" for="{{ $name }}">{{ $label }}</label>
    <textarea 
        class="form-control @error($name) is-invalid @enderror" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >{{ $value }}</textarea>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>