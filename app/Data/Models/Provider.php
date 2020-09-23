<?php

namespace App\Data\Models;

use App\Services\CpfCnpj\CpfCnpj;

class Provider extends Model
{
    protected $fillable = [
        'cpf_cnpj',
        'type',
        'name',
        'created_by_id',
        'updated_by_id',
        'is_blocked'
    ];

    protected $orderBy = ['name' => 'asc'];

    protected $filterableColumns = ['cpf_cnpj', 'name'];

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->type = $this->type ?? CpfCnpj::type($this->cpf_cnpj);

        return parent::save();
    }
}
