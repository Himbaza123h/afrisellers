<?php

namespace App\Traits;

use App\Models\SystemLog;

trait LogsActivity
{
    // protected static function bootLogsActivity(): void
    // {
    //     $module = static::getLogModule();

    //     static::created(function ($model) use ($module) {
    //         SystemLog::info(
    //             class_basename($model) . ' #' . $model->id . ' was created.',
    //             [
    //                 'action'      => 'created',
    //                 'module'      => $module,
    //                 'entity_type' => class_basename($model),
    //                 'entity_id'   => $model->id,
    //             ]
    //         );
    //     });

    //     static::updated(function ($model) use ($module) {
    //         $changed = array_keys($model->getChanges());
    //         SystemLog::info(
    //             class_basename($model) . ' #' . $model->id . ' was updated. Fields: ' . implode(', ', $changed),
    //             [
    //                 'action'      => 'updated',
    //                 'module'      => $module,
    //                 'entity_type' => class_basename($model),
    //                 'entity_id'   => $model->id,
    //                 'metadata'    => ['changed' => $changed],
    //             ]
    //         );
    //     });

    //     static::deleted(function ($model) use ($module) {
    //         SystemLog::warning(
    //             class_basename($model) . ' #' . $model->id . ' was deleted.',
    //             [
    //                 'action'      => 'deleted',
    //                 'module'      => $module,
    //                 'entity_type' => class_basename($model),
    //                 'entity_id'   => $model->id,
    //             ]
    //         );
    //     });
    // }

    // protected static function getLogModule(): string
    // {
    //     return strtolower(class_basename(static::class)) . 's';
    // }
}
