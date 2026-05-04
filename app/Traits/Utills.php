<?php

namespace App\Traits;

use App\Constants\Status;

trait Utills {

    protected $moneyKeys = [
        'raise_amount_ownership',
        'raise_amount_distributions',
        'raise_quota',
        'min_raise_amount',
        'max_raise_amount',
        'minimum_investment',
        'maximum_investment',
        'price_per_unit',
        'investment_amount',
        'offering_size',
        'exit_price',
        'acquisition_price'
    ];

    public function moneyToDouble($data)
    {
        if(is_array($data)) {
            // check for each key if it is a money string with commas
            foreach($data as $key => $value) {
                if(is_string($value) && in_array($key, $this->moneyKeys)) {
                    $value = str_replace('$','',$value);
                    $data[$key] = (double)str_replace(',', '', $value);
                }else {
                    $data[$key] = $this->moneyToDouble($value);
                }
            }
        }
        return $data;
    }

}