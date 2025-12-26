<div class="mb-3">
    <label class="form-label {{ $required ? 'required' : '' }}" for="{{ $name }}">{{ $label }}</label>
    <input 
        type="{{ $type }}" 
        class="form-control @error($name) is-invalid @enderror" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ $value }}" 
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>