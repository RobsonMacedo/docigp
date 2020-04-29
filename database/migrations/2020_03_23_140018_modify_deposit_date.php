<?php

use App\Data\Models\Entry;
use App\Data\Repositories\EntryTypes as EntryTypesRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDepositDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Entry::disableGlobalScopes();
        Entry::disableEvents();
        $entries = app(Entry::class)->all();
        $entryType = app(EntryTypesRepository::class)->findByName(
            'Depósito Alerj'
        );

        //dump($entries);
        foreach ($entries as $entry) {
            if ($entry['entry_type_id'] == $entryType->id) {
                dump(
                    'atualizando ' .
                        $entry['id'] .
                        ' para a data ' .
                        $entry['date']->firstOfMonth()
                );
                DB::table('entries')
                    ->where('id', $entry['id'])
                    ->update(['date' => $entry['date']->firstOfMonth()]);
            }
        }

        Entry::enableGlobalScopes();
        Entry::enableEvents();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
