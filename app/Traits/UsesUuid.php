<?php


namespace App\Traits;

use Illuminate\Support\Str;

trait UsesUuid {

    public function getKeyType() {
        return 'string';
    }

    public function getIncrementing() {
        return false;
    }

    protected static function bootUsesUuid() {

    static::creating( function($model) {
            if( empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid();
            }});
    }

}
