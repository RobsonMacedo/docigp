<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Data\Models\Congressman;
use App\Data\Models\CongressmanBudget;

class FixCongressmanBudgetsAnalysedButNotClosed extends Migration
{
    public $ids = [109, 177, 246, 315, 422, 493, 563, 628];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Congressman::disableGlobalScopes();
        CongressmanBudget::disableGlobalScopes();
        collect($this->ids)->each(function ($id) {
            if ($congressmanBudget = CongressmanBudget::find($id)) {
                $congressmanBudget->closed_at = $congressmanBudget->analysed_at;
                $congressmanBudget->save();
                dump(
                    "CongressmanBudget with id={$id} closed_at field is now set to {$congressmanBudget->closed_at}"
                );
            } else {
                dump("CongressmanBudget with id={$id} was not found");
            }
        });
        Congressman::enableGlobalScopes();
        CongressmanBudget::enableGlobalScopes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Congressman::disableGlobalScopes();
        CongressmanBudget::disableGlobalScopes();
        collect($this->ids)->each(function ($id) {
            if ($congressmanBudget = CongressmanBudget::find($id)) {
                $congressmanBudget->closed_at = null;
                $congressmanBudget->save();
                dump(
                    "CongressmanBudget with id={$id} closed_at field is now set to null"
                );
            } else {
                dump("CongressmanBudget with id={$id} was not found");
            }
        });
        Congressman::enableGlobalScopes();
        CongressmanBudget::enableGlobalScopes();
    }
}
