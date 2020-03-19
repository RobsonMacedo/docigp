<?php

namespace Tests\Browser\Pages;

use App\Data\Models\User;
use App\Data\Repositories\CostCenters;
use App\Support\Constants;
use Faker\Generator as Faker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CostCentersTest extends DuskTestCase
{
    private static $nomeCentrosdeCusto;
    private static $codigoCentrosdeCusto;
    private static $codigoduperiorCentrosdeCusto;
    private static $limiteCentrosdeCusto;
    private static $revogadoCentrosdeCusto;
    private static $randomCostsCenter;
    private static $administrator;

    public function createAdminstrator()
    {
        static::$administrator = factory(
            User::class,
            Constants::ROLE_ADMINISTRATOR
        )->raw();
    }

    public function init()
    {
        $faker = app(Faker::class);
        static::$nomeCentrosdeCusto = only_letters_and_space(
            app(Faker::class)->name
        );
        static::$codigoCentrosdeCusto = only_numbers(
            app(Faker::class)->numberBetween(0, 1000)
        );
        static::$codigoduperiorCentrosdeCusto = only_numbers(
            app(Faker::class)->numberBetween(0, 1000)
        );
        static::$limiteCentrosdeCusto = only_numbers(
            app(Faker::class)->numberBetween(0, 100)
        );
        static::$revogadoCentrosdeCusto = $this->randomDate();

        static::$randomCostsCenter = app(CostCenters::class)
            ->randomElement()
            ->toArray();
    }

    public function testInsert()
    {
        $this->createAdminstrator();
        $this->init();
        $frequencia = ['daily', 'monthly', 'yearly'];
        $nomeA = static::$nomeCentrosdeCusto;
        $codigoA = static::$codigoCentrosdeCusto;
        $codigoB = static::$codigoduperiorCentrosdeCusto;
        $frequenciaA = $frequencia[array_rand($frequencia, 1)];
        $limiteA = static::$limiteCentrosdeCusto;
        $revogadoA = static::$revogadoCentrosdeCusto;
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use (
            $nomeA,
            $codigoA,
            $codigoB,
            $frequenciaA,
            $limiteA,
            $revogadoA,
            $administrator
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/cost-centers#/')
                ->assertSee('Novo')
                ->press('#novo')
                ->type('#name', $nomeA)
                ->type('#code', $codigoA)
                ->type('#parent_code', $codigoB)
                ->type('#frequency', $frequenciaA)
                ->type('#limit', $limiteA)
                ->press('#can_accumulate')
                ->type('#revoked_at', $revogadoA->format('d/m/Y'))
                ->press('Gravar')
                ->assertSee($nomeA);
        });
    }

    public function testValidation()
    {
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use ($administrator) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/cost-centers#/')
                ->clickLink('Novo')
                ->press('Gravar')
                ->assertSee('O campo nome é obrigatório.')
                ->assertSee('O campo código é obrigatório.');
        });
    }

    public function testAlter()
    {
        $this->init();
        $nomeA = static::$nomeCentrosdeCusto;
        $randomCostsCenter1 = static::$randomCostsCenter;
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use (
            $nomeA,
            $randomCostsCenter1,
            $administrator
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/entry-types/' . $randomCostsCenter1['id'] . '#/')
                ->click('#vue-editButton')
                ->type('#name', '*' . $nomeA . '*')
                ->screenshot('asdasd')
                ->press('Gravar')
                ->assertSee('*' . $nomeA . '*');
        });
    }

    public function testWrongSearch()
    {
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use ($administrator) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/cost-centers#/')
                ->type('@search-input', '1323e12312vcxvdsf413543445654')
                ->click('@search-button')
                ->waitForText('Nenhum Centro de Custo encontrado')
                ->screenshot('CostCenters-wrongsearch')
                ->assertSee('Nenhum Centro de Custo encontrado');
        });
    }

    public function testRightSearch()
    {
        $this->init();
        $randomCostCenters1 = static::$randomCostsCenter;
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use (
            $randomCostCenters1,
            $administrator
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/cost-centers#/')
                ->type('@search-input', $randomCostCenters1['name'])
                ->click('@search-button')
                ->assertSee($randomCostCenters1['code'])
                ->screenshot('CostCenters-rightsearch');
        });
    }
}
