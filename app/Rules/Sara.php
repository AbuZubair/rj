<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Sara implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $restricted = ['anjing','babi','bangsat','shit','fuck','goblok','idiot','tolol','setan','pelacur','asshole','fucking','autis','homoseks','gay','lesbi','lgbt','monyet','tai','eek','kampret','damn','iblis'];
        $valid = true;
        foreach ($restricted as $v) {
            if (strpos(strtolower($value), $v) !== false) {
                $valid = false;
            }
        }

        return $valid;
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute contains SARA';
    }
}
