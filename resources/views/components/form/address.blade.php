@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => '',
    'required' => false
])

<div class="mb-3" x-data="{
    searchQuery: '{{ $value }}',
    searchResults: [],
    showResults: false,
    isSearching: false,
    
    allowedStates: @json(json_decode(\App\Models\Setting::get('allowed_states', '[]'))) || [],
    
    async searchAddress() {
        if (this.searchQuery.length < 3) {
            this.searchResults = [];
            this.showResults = false;
            return;
        }

        this.isSearching = true;

        try {
            let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=50&countrycodes=in&addressdetails=1`);
            let data = await response.json();
            
            // Filter by allowed states if configured
            if (this.allowedStates.length > 0) {
                data = data.filter(result => {
                    return this.allowedStates.includes(result.address.state);
                });
            }
            
            this.searchResults = data.slice(0, 5); // Limit to 5 after filtering
            this.showResults = true;
        } catch (error) {
            console.error('Error searching address:', error);
            this.searchResults = [];
        } finally {
            this.isSearching = false;
        }
    },

    selectAddress(result) {
        this.searchQuery = result.display_name;
        this.showResults = false;
        // Optionally dispatch event or update other fields if needed, 
        // but for basic usage the input value is what matters.
    }
}">
    <label class="form-label {{ $required ? 'required' : '' }}" for="{{ $name }}">{{ $label }}</label>
    
    <div class="position-relative">
        <div class="input-icon">
            <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M21 21l-6 -6" /></svg>
            </span>
            <input 
                type="text" 
                class="form-control @error($name) is-invalid @enderror" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                x-model="searchQuery" 
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                @input.debounce.500ms="searchAddress()"
                @keydown.escape="showResults = false"
                @click.away="showResults = false"
                {{ $attributes }}
            >
            <span class="input-icon-addon" x-show="isSearching" style="display: none;">
                <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
            </span>
        </div>

        <div class="dropdown-menu show w-100" x-show="showResults && searchResults.length > 0" style="display: none; max-height: 200px; overflow-y: auto;">
            <template x-for="result in searchResults" :key="result.place_id">
                <a href="#" class="dropdown-item icon-link" @click.prevent="selectAddress(result)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                    <span class="text-truncate" x-text="result.display_name"></span>
                </a>
            </template>
        </div>
    </div>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
