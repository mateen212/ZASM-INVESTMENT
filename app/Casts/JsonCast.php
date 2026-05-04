<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JsonCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if($key =='included_class')
        {
            // json decode and return every value as integer
            if(is_array(json_decode($value, true))){
                return array_map('intval', json_decode($value, true) ?? []);
            }else{
                return [];
            }
        }
        return json_decode($value, true) ?? [];
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if($key =='included_class')
        {
            // json encode and return every value as string
            return json_encode(array_map('strval', $value));
        }
        return json_encode($value);
    }
}
