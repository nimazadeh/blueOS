<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Global platform settings (key/value, JSON-typed).
 *
 * Domain code must access these through App\Core\Settings\SettingsService,
 * never directly, so caching and type handling stay centralized.
 */
class Settings extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'is_public',
        'updated_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
            'is_public' => 'boolean',
            'updated_by' => 'integer',
        ];
    }
}
