<?php

namespace App\Support;

use App\Models\Feature;

class MembershipPlanFeatureValueRules
{
    /**
     * @deprecated Prefer {@see Feature::resolvedValueType()}. Kept for code that only has a key string.
     */
    public static function typeForKey(string $featureKey): string
    {
        $def = config('membership_feature_keys.'.$featureKey);

        return is_array($def) ? (string) ($def['type'] ?? 'text') : 'text';
    }

    /**
     * When attaching a feature to a plan, uses {@see Feature::resolvedValueType()} (database first).
     *
     * @return array{ok: bool, value?: string, message?: string}
     */
    public static function validateForNewAssignment(Feature $feature, mixed $raw): array
    {
        $type = $feature->resolvedValueType();

        if ($type === 'boolean') {
            return ['ok' => true, 'value' => 'true'];
        }

        $s = trim((string) ($raw ?? ''));
        if ($s === '') {
            return ['ok' => false, 'message' => 'A value is required.'];
        }

        return match ($type) {
            'number' => self::assertNumberOnly($s),
            'number_or_unlimited' => self::assertNumberOrUnlimited($s),
            default => strlen($s) > 255
                ? ['ok' => false, 'message' => 'Maximum 255 characters.']
                : ['ok' => true, 'value' => $s],
        };
    }

    /**
     * @return array{ok: bool, value?: string, message?: string}
     */
    public static function validateForUpdate(Feature $feature, mixed $raw): array
    {
        $type = $feature->resolvedValueType();
        $s = trim((string) ($raw ?? ''));

        if ($s === '') {
            return ['ok' => false, 'message' => 'A value is required.'];
        }

        if (strlen($s) > 255) {
            return ['ok' => false, 'message' => 'Maximum 255 characters.'];
        }

        if ($type === 'boolean') {
            $low = strtolower($s);
            if (in_array($low, ['true', '1', 'yes'], true)) {
                return ['ok' => true, 'value' => 'true'];
            }
            if (in_array($low, ['false', '0', 'no'], true)) {
                return ['ok' => true, 'value' => 'false'];
            }

            return ['ok' => false, 'message' => 'Use true or false only.'];
        }

        return match ($type) {
            'number' => self::assertNumberOnly($s),
            'number_or_unlimited' => self::assertNumberOrUnlimited($s),
            default => ['ok' => true, 'value' => $s],
        };
    }

    /**
     * @return array{ok: bool, value?: string, message?: string}
     */
    private static function assertNumberOnly(string $s): array
    {
        $low = strtolower($s);
        if ($low === 'unlimited') {
            return ['ok' => false, 'message' => 'Enter a number only (not "unlimited") for this feature.'];
        }
        if (in_array($low, ['true', 'false', 'yes', 'no'], true)) {
            return ['ok' => false, 'message' => 'Enter a numeric value only.'];
        }
        if (! is_numeric($s)) {
            return ['ok' => false, 'message' => 'Enter a valid number.'];
        }

        return ['ok' => true, 'value' => $s];
    }

    /**
     * @return array{ok: bool, value?: string, message?: string}
     */
    private static function assertNumberOrUnlimited(string $s): array
    {
        $low = strtolower($s);
        if ($low === 'unlimited') {
            return ['ok' => true, 'value' => 'unlimited'];
        }
        if (in_array($low, ['true', 'false', 'yes', 'no'], true)) {
            return ['ok' => false, 'message' => 'Enter a number or the word "unlimited".'];
        }
        if (! is_numeric($s)) {
            return ['ok' => false, 'message' => 'Enter a valid number or "unlimited".'];
        }

        return ['ok' => true, 'value' => $s];
    }
}
