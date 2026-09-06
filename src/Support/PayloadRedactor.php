<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Support;

final class PayloadRedactor
{
    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function redact(array $values): array
    {
        if (! $this->redactionEnabled()) {
            return $values;
        }

        $configuredKeys = config('events.redaction.keys', []);
        $keys = [];
        if (is_array($configuredKeys)) {
            foreach ($configuredKeys as $configuredKey) {
                if (is_string($configuredKey)) {
                    $keys[] = strtolower($configuredKey);
                }
            }
        }

        $replacement = config('events.redaction.replacement', '[REDACTED]');

        return $this->redactArray($values, $keys, is_scalar($replacement) ? $replacement : '[REDACTED]');
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    private function redactArray(array $values, array $keys, mixed $replacement): array
    {
        $redacted = [];
        foreach ($values as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (in_array(strtolower($key), $keys, true)) {
                $redacted[$key] = $replacement;

                continue;
            }

            if (is_array($value)) {
                /** @var array<string, mixed> $nested */
                $nested = [];
                foreach ($value as $nestedKey => $nestedValue) {
                    if (is_string($nestedKey)) {
                        $nested[$nestedKey] = $nestedValue;
                    }
                }
                $redacted[$key] = $this->redactArray($nested, $keys, $replacement);

                continue;
            }

            $redacted[$key] = $value;
        }

        return $redacted;
    }

    private function redactionEnabled(): bool
    {
        $enabled = config('events.redaction.enabled', true);

        return is_bool($enabled) ? $enabled : filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
    }
}
