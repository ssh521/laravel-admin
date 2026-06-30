<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalStack extends Component
{
    private const BASE_Z_INDEX = 5000;

    /**
     * @var array<int, array{id: string, component: string, params: array<string, mixed>, key: string, title: string, size: string, width: int, height: int, minWidth: int, minHeight: int, draggable: bool, resizable: bool, closeOnBackdrop: bool, version: int}>
     */
    public array $modals = [];

    public int $version = 0;

    #[On('admin:modal-stack:open')]
    public function openModal(mixed $component = null, array $params = [], ?string $title = null, ?string $size = null, ?string $id = null): void
    {
        $modal = $this->makeModal($this->normalizePayload($component, $params, $title, $size, $id));

        $this->modals = [$modal];
    }

    #[On('admin:modal-stack:push')]
    public function pushModal(mixed $component = null, array $params = [], ?string $title = null, ?string $size = null, ?string $id = null): void
    {
        $this->modals[] = $this->makeModal($this->normalizePayload($component, $params, $title, $size, $id));
    }

    #[On('admin:modal-stack:close')]
    public function closeModal(mixed $payload = null, ?string $id = null): void
    {
        $payload = $this->normalizeClosePayload($payload, $id);
        $modalId = $payload['id'] ?? null;

        if (! $modalId) {
            $this->closeTopModal();

            return;
        }

        $this->modals = array_values(array_filter(
            $this->modals,
            fn (array $modal): bool => $modal['id'] !== $modalId
        ));
    }

    #[On('admin:modal-stack:close-top')]
    public function closeTopModal(): void
    {
        array_pop($this->modals);
        $this->modals = array_values($this->modals);
    }

    #[On('admin:modal-stack:close-all')]
    public function closeAllModals(): void
    {
        $this->modals = [];
    }

    public function isTopModal(string $id): bool
    {
        $top = Arr::last($this->modals);

        return is_array($top) && ($top['id'] ?? null) === $id;
    }

    public function zIndexFor(int $index): int
    {
        return self::BASE_Z_INDEX + $index;
    }

    public function widthClassFor(string $size): string
    {
        return match ($size) {
            'sm' => 'max-w-sm',
            'lg' => 'max-w-3xl',
            'xl' => 'max-w-5xl',
            '2xl' => 'max-w-7xl',
            'full' => 'max-w-[calc(100vw-2rem)]',
            default => 'max-w-xl',
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string, component: string, params: array<string, mixed>, key: string, title: string, size: string, version: int}
     */
    private function makeModal(array $payload): array
    {
        $component = (string) ($payload['component'] ?? '');

        if ($component === '') {
            throw new \InvalidArgumentException('A modal component name is required.');
        }

        $this->version++;

        $id = (string) ($payload['id'] ?? $this->newModalId($component));
        $params = $this->normalizeParams($payload['params'] ?? []);
        $params['modalStackId'] ??= $id;

        return [
            'id' => $id,
            'component' => $component,
            'params' => $params,
            'key' => $this->makeKey($component, $id, $params, $this->version),
            'title' => (string) ($payload['title'] ?? ''),
            'size' => (string) ($payload['size'] ?? 'md'),
            'width' => $this->integerPayload($payload, 'width', $this->defaultWidthFor((string) ($payload['size'] ?? 'md'))),
            'height' => $this->integerPayload($payload, 'height', 560),
            'minWidth' => $this->integerPayload($payload, 'minWidth', 320),
            'minHeight' => $this->integerPayload($payload, 'minHeight', 220),
            'draggable' => $this->booleanPayload($payload, 'draggable', true),
            'resizable' => $this->booleanPayload($payload, 'resizable', true),
            'closeOnBackdrop' => $this->booleanPayload($payload, 'closeOnBackdrop', true),
            'version' => $this->version,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function integerPayload(array $payload, string $key, int $default): int
    {
        $value = $payload[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function booleanPayload(array $payload, string $key, bool $default): bool
    {
        if (! array_key_exists($key, $payload)) {
            return $default;
        }

        return filter_var($payload[$key], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    private function defaultWidthFor(string $size): int
    {
        return match ($size) {
            'sm' => 384,
            'lg' => 768,
            'xl' => 1024,
            '2xl' => 1280,
            'full' => 1200,
            default => 576,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizePayload(mixed $component, array $params, ?string $title, ?string $size, ?string $id): array
    {
        if (is_array($component)) {
            return $component;
        }

        if (is_object($component)) {
            return (array) $component;
        }

        return array_filter([
            'component' => $component,
            'params' => $params,
            'title' => $title,
            'size' => $size,
            'id' => $id,
        ], fn (mixed $value): bool => $value !== null);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeClosePayload(mixed $payload, ?string $id): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload)) {
            return (array) $payload;
        }

        if (is_string($payload) && $payload !== '') {
            return ['id' => $payload];
        }

        return array_filter(['id' => $id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeParams(mixed $params): array
    {
        if (is_array($params)) {
            return $params;
        }

        if (is_object($params)) {
            return (array) $params;
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function makeKey(string $component, string $id, array $params, int $version): string
    {
        $normalizedParams = $this->sortRecursive($params);
        $paramsHash = substr(sha1(json_encode($normalizedParams, JSON_THROW_ON_ERROR)), 0, 16);

        return Str::slug($component, '-').'-'.$id.'-'.$paramsHash.'-'.$version;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    private function sortRecursive(array $value): array
    {
        ksort($value);

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->sortRecursive($item);
            }
        }

        return $value;
    }

    private function newModalId(string $component): string
    {
        return Str::slug($component, '-').'-'.Str::uuid()->toString();
    }

    public function render()
    {
        return view('laravel-admin::livewire.admin.modal-stack');
    }
}
