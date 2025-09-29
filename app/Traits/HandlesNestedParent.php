<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HandlesNestedParent
{
    /**
     * Гарантирует корректную обработку смены parent_id для моделей с Nested Set.
     */
    protected static function bootHandlesNestedParent(): void
    {
        static::updating(function (Model $model) {
            if (!property_exists($model, 'parent_id')) {
                return;
            }

            if (!$model->isDirty('parent_id')) {
                return;
            }

            if (!method_exists($model, 'appendToNode') || !method_exists($model, 'makeRoot')) {
                return;
            }

            $originalParent = $model->getOriginal('parent_id');
            $newParent = $model->parent_id;

            if ($newParent === $originalParent) {
                return;
            }

            if ($newParent) {
                $parent = $model->newModelQuery()->find($newParent);

                if ($parent) {
                    $model->appendToNode($parent);
                    return;
                }
            }

            $model->makeRoot();
        });
    }
}

