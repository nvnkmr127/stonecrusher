<section>
    <header>
        <h2 class="h3 mb-1 text-danger">
            {{ __('Delete Account') }}
        </h2>

        <p class="text-secondary small">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="mt-3" x-data="{}" x-on:click="$dispatch('open-modal', 'confirm-user-deletion')">
        <button class="btn btn-danger">
            {{ __('Delete Account') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="md"
        title="{{ __('Delete Account') }}">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <p class="text-sm text-secondary mb-4">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mb-3">
                <label class="form-label required">{{ __('Password') }}</label>
                <input type="password" name="password"
                    class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                    placeholder="{{ __('Password') }}">
                @if($errors->userDeletion->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->userDeletion->first('password') }}
                    </div>
                @endif
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" class="btn btn-ghost"
                    x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-danger">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>