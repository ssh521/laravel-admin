<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditorImageUploader
{
    public function store(Request $request, string $configKey, ?string $pathSuffix = null): JsonResponse
    {
        $maxSize = (int) config($configKey.'.max_size', 10240);
        $imageTypes = array_map('strtolower', (array) config($configKey.'.image_types', ['jpg', 'jpeg', 'png', 'gif', 'webp']));

        $request->validate([
            'file' => ['required', 'file', 'max:'.$maxSize, 'mimes:'.implode(',', $imageTypes)],
        ]);

        $file = $request->file('file');
        $disk = (string) config($configKey.'.disk', 'public');
        $basePath = trim((string) config($configKey.'.path', 'editor/images'), '/');
        $path = collect([$basePath, trim((string) $pathSuffix, '/')])
            ->filter()
            ->implode('/');

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $filename = Str::uuid().'.'.$extension;
        $storedPath = $file->storeAs($path, $filename, $disk);

        return response()->json([
            'location' => Storage::disk($disk)->url($storedPath),
        ]);
    }
}
