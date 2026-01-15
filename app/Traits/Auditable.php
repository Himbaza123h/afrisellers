<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the trait
     */
    protected static function bootAuditable()
    {
        // Log when model is created
        static::created(function ($model) {
            if (static::shouldAudit('created')) {
                AuditLog::log(
                    'created',
                    static::getAuditDescription('created', $model),
                    $model,
                    null,
                    $model->getAuditableAttributes()
                );
            }
        });

        // Log when model is updated
        static::updated(function ($model) {
            if (static::shouldAudit('updated')) {
                $changes = $model->getChanges();
                if (!empty($changes)) {
                    AuditLog::log(
                        'updated',
                        static::getAuditDescription('updated', $model),
                        $model,
                        $model->getOriginal(),
                        $changes
                    );
                }
            }
        });

        // Log when model is deleted
        static::deleted(function ($model) {
            if (static::shouldAudit('deleted')) {
                AuditLog::log(
                    'deleted',
                    static::getAuditDescription('deleted', $model),
                    $model,
                    $model->getAuditableAttributes(),
                    null
                );
            }
        });
    }

    /**
     * Check if action should be audited
     */
    protected static function shouldAudit(string $action): bool
    {
        // Get excluded actions from model property if exists
        $excludedActions = property_exists(static::class, 'auditExclude')
            ? static::$auditExclude
            : [];

        return !in_array($action, $excludedActions);
    }

    /**
     * Get audit description
     */
    protected static function getAuditDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $identifier = method_exists($model, 'getAuditIdentifier')
            ? $model->getAuditIdentifier()
            : ($model->name ?? $model->title ?? "ID: {$model->id}");

        return match($action) {
            'created' => "{$modelName} '{$identifier}' was created",
            'updated' => "{$modelName} '{$identifier}' was updated",
            'deleted' => "{$modelName} '{$identifier}' was deleted",
            default => "{$modelName} '{$identifier}' was {$action}",
        };
    }

    /**
     * Get auditable attributes
     */
    protected function getAuditableAttributes(): array
    {
        // Get included attributes if specified
        if (property_exists($this, 'auditableAttributes')) {
            $attributes = array_intersect_key(
                $this->attributes,
                array_flip($this->auditableAttributes)
            );
        } else {
            $attributes = $this->attributes;
        }

        // Remove excluded attributes
        $excludedAttributes = property_exists($this, 'auditExcludeAttributes')
            ? $this->auditExcludeAttributes
            : ['password', 'remember_token', 'deleted_at'];

        return array_diff_key($attributes, array_flip($excludedAttributes));
    }

    /**
     * Manually log an audit entry
     */
    public function logAudit(string $action, string $description, array $oldValues = null, array $newValues = null)
    {
        return AuditLog::log(
            $action,
            $description,
            $this,
            $oldValues,
            $newValues
        );
    }
}
