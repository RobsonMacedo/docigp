<?php

namespace App\Rules;

use App\Services\CpfCnpj\CpfCnpj;
use Illuminate\Contracts\Validation\Rule;

class CheckCpforCnpj implements Rule
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
        $cpforcnpj = new CpfCnpj($value);
        return $cpforcnpj->check($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O CPF ou CNPJ não é válido';
    }
}
