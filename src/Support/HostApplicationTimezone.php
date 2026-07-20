<?php

namespace Ssh521\LaravelAdmin\Support;

class HostApplicationTimezone
{
    public const UPDATED = 'updated';

    public const CONFIGURED = 'configured';

    public const CUSTOM = 'custom';

    public const MISSING = 'missing';

    public function update(string $path): string
    {
        if (! is_file($path)) {
            return self::MISSING;
        }

        $contents = (string) file_get_contents($path);

        if ($this->usesAsiaSeoul($contents)) {
            return self::CONFIGURED;
        }

        $pattern = '/(?<prefix>[\'\"]timezone[\'\"]\s*=>\s*)(?:[\'\"]UTC[\'\"]|env\(\s*[\'\"]APP_TIMEZONE[\'\"]\s*,\s*[\'\"]UTC[\'\"]\s*\))(?<suffix>\s*,)/';
        $updated = preg_replace_callback(
            $pattern,
            fn (array $matches): string => $matches['prefix']."env('APP_TIMEZONE', 'Asia/Seoul')".$matches['suffix'],
            $contents,
            1,
            $count,
        );

        if (! is_string($updated) || $count !== 1) {
            return self::CUSTOM;
        }

        file_put_contents($path, $updated);

        return self::UPDATED;
    }

    private function usesAsiaSeoul(string $contents): bool
    {
        return preg_match(
            '~[\'\"]timezone[\'\"]\s*=>\s*(?:[\'\"]Asia/Seoul[\'\"]|env\(\s*[\'\"]APP_TIMEZONE[\'\"]\s*,\s*[\'\"]Asia/Seoul[\'\"]\s*\))\s*,~',
            $contents,
        ) === 1;
    }
}
