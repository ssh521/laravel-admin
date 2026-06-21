<?php

namespace Ssh521\LaravelAdmin\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class FakeBroadcastNotification
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public static array $sent = [];

    public static function reset(): void
    {
        self::$sent = [];
    }

    /**
     * @param  iterable<int, Model>|Model  $notifiables
     */
    public static function to(iterable|Model $notifiables): FakePendingBroadcastNotification
    {
        return new FakePendingBroadcastNotification($notifiables);
    }
}

class FakePendingBroadcastNotification
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /**
     * @param  iterable<int, Model>|Model  $notifiables
     */
    public function __construct(private readonly iterable|Model $notifiables) {}

    public function type(string $type): self
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    public function title(string $title): self
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function body(?string $body): self
    {
        $this->attributes['body'] = $body;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function payload(array $payload): self
    {
        $this->attributes['payload'] = $payload;

        return $this;
    }

    public function source(?string $source): self
    {
        $this->attributes['source'] = $source;

        return $this;
    }

    public function related(?Model $related): self
    {
        $this->attributes['related'] = $related;

        return $this;
    }

    public function actor(?Model $actor): self
    {
        $this->attributes['actor'] = $actor;

        return $this;
    }

    public function send(): void
    {
        FakeBroadcastNotification::$sent[] = $this->attributes + [
            'notifiables' => $this->notifiables instanceof Model
                ? collect([$this->notifiables])
                : collect($this->notifiables),
        ];
    }
}
