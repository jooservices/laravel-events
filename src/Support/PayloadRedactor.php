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
        if (! config('events.redaction.enabled', true)) {
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
     * @param  array<mixed>  $values
     * @param  list<string>  $keys
     * @return array<mixed>
     */
    private function redactArray(array $values, array $keys, mixed $replacement): array
    {
        foreach ($values as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $keys, true)) {
                $values[$key] = $replacement;

                continue;
            }

            if (is_array($value)) {
                $values[$key] = $this->redactArray($value, $keys, $replacement);
            }
        }

        return $values;
    }
}
