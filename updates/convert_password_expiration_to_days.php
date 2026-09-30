<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Migration;

return new class () extends Migration {
    private const SETTINGS_CODE = 'wobqqq_fortify_fortify';

    public function up(): void
    {
        $this->convert(static fn (mixed $value): int => is_numeric($value) ? max(0, (int)$value) : 0);
    }

    public function down(): void
    {
        $this->convert(static fn (mixed $value): bool => is_numeric($value) && (int)$value > 0);
    }

    private function convert(callable $cast): void
    {
        if (!DB::getSchemaBuilder()->hasTable('system_settings')) {
            return;
        }

        $rows = DB::table('system_settings')->where('item', self::SETTINGS_CODE)->get(['id', 'value']);

        foreach ($rows as $row) {
            $value = is_string($row->value) ? json_decode($row->value, true) : null;

            if (!is_array($value) || !is_array($value['config'] ?? null)
                || !array_key_exists('password_policy_expire_days', $value['config'])) {
                continue;
            }

            $value['config']['password_policy_expire_days'] = $cast($value['config']['password_policy_expire_days']);

            DB::table('system_settings')->where('id', $row->id)->update(['value' => json_encode($value)]);
        }
    }
};
