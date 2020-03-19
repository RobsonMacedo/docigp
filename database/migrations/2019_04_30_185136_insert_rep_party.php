<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Data\Models\Party;

class InsertRepParty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Party::firstOrCreate(
            ['code' => 'REP'],
            ['code' => 'REP', 'name' => 'Republicanos']
        );

        Party::firstOrCreate(
            ['code' => 'Cidadania'],
            ['code' => 'Cidadania', 'name' => 'Cidadania']
        );
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
