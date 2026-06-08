@php
    $laravelAdminViteConfig = config('laravel-admin.vite', [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ]);
    $laravelAdminViteInputs = $laravelAdminViteConfig === false
        ? []
        : array_values(array_filter((array) $laravelAdminViteConfig));

    $laravelAdminManifestPath = public_path('build/manifest.json');
    $laravelAdminShouldLoadVite = $laravelAdminViteInputs !== [] && file_exists(public_path('hot'));

    if (! $laravelAdminShouldLoadVite && file_exists($laravelAdminManifestPath)) {
        $laravelAdminManifest = json_decode((string) file_get_contents($laravelAdminManifestPath), true) ?: [];
        $laravelAdminShouldLoadVite = $laravelAdminViteInputs !== [];

        foreach ((array) $laravelAdminViteInputs as $laravelAdminViteInput) {
            if (! array_key_exists($laravelAdminViteInput, $laravelAdminManifest)) {
                $laravelAdminShouldLoadVite = false;

                break;
            }
        }
    }
@endphp

@if ($laravelAdminShouldLoadVite)
    @vite($laravelAdminViteInputs)
@endif
