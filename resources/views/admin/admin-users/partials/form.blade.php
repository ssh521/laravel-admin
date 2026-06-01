<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-laravel-admin::label for="name" :value="__('Name')" />
        <x-laravel-admin::input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $adminUser?->name)" autocomplete="name" />
        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-laravel-admin::label for="email" :value="__('Email')" />
        <x-laravel-admin::input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $adminUser?->email)" autocomplete="email" />
        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('email')" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-laravel-admin::input-label for="password" :value="__('Password')" />
        <x-laravel-admin::input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div>
        <x-laravel-admin::input-label for="confirm-password" :value="__('Confirm Password')" />
        <x-laravel-admin::input id="confirm-password" name="confirm-password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('confirm-password')" />
    </div>
</div>

<div>
    <x-laravel-admin::input-label :value="__('Email Verification Status')" />
    <div class="mt-2 flex items-center">
        <x-laravel-admin::checkbox id="email_verified" name="email_verified" value="1" :checked="old('email_verified', $adminUser?->email_verified_at ? '1' : null) == '1'" />
        <label for="email_verified" class="ms-2 text-sm font-medium">{{ __('Email Verified') }}</label>
    </div>
</div>

<x-laravel-admin::section-border />

<div>
    <h4 class="text-md font-medium mb-4">{{ __('Assign Roles') }}</h4>
    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($roles as $role)
            <div class="flex items-center">
                <x-laravel-admin::checkbox id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" :checked="in_array($role->id, old('roles', $adminUser?->roles->pluck('id')->all() ?? []))" />
                <label for="role_{{ $role->id }}" class="ms-2 text-sm font-medium">{{ $role->name }}</label>
            </div>
        @endforeach
    </div>
    <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('roles')" />
</div>

<div class="flex items-center justify-end">
    <x-laravel-admin::primary-button>
        {{ $submitLabel }}
    </x-laravel-admin::primary-button>
</div>
