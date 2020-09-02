<?php

namespace App\Exports;

use App\Data\Models\Audit as Audits;
use App\Data\Models\User as Users;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;


class UsersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $data_ini;
    protected $data_fim;


    public function __construct($data_ini, $data_fim)
    {
        $this->data_ini = $data_ini;
        $this->data_fim = $data_fim;
    }

    public function collection()
    {
        $users = Users::all();

        $period = \Carbon\CarbonPeriod::create($this->data_ini, $this->data_fim);
        

        $periodCollection = collect($period->toArray());

        $firstline = collect(
            array_merge(
                ['nome'],
                ['E-mail'],
                collect(
                    $period->map(function ($date) {
                        return $date->format('d/m/Y');
                    })
                )->toArray()
            )
        );


        $allLinesCollection = collect();
        $allLinesCollection->push($firstline);

        $users->each(function ($user) use ($periodCollection, $allLinesCollection) {
            $lineCollection = collect([]);
            $lineCollection->push($user->name);
            $lineCollection->push($user->email);

            $periodCollection->each(function ($date) use ($user, $lineCollection) {
                $lineCollection->push(
                    Audits::where('user_id', $user->id)
                        ->whereDate('created_at', $date)
                        ->count()

                );
            });

            $allLinesCollection->push($lineCollection);
        });
        
        return $allLinesCollection;
    }
};
