<?php

namespace Tests\Browser\Pages;

use App\Data\Models\Provider;
use App\Data\Models\User;
use App\Data\Repositories\Providers;
use App\Support\Constants;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProvidersTest extends DuskTestCase
{
    private static $providerRaw;
    private static $randomProviders;
    private static $administrator;

    public function createAdministrator()
    {
        static::$administrator = factory(
            User::class,
            Constants::ROLE_ADMINISTRATOR
        )->raw();
    }

    public function init()
    {
        static::$providerRaw = factory(Provider::class)->raw();
        static::$randomProviders = app(Providers::class)
            ->randomElement()
            ->toArray();
    }

    public function testInsert()
    {
        $this->createAdministrator();
        $this->init();
        $provider = static::$providerRaw;

        $administrator = static::$administrator;
        $this->browse(function (Browser $browser) use (
            $administrator,
            $provider
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/providers#/')
                ->assertSee('Novo')
                ->press('#novo')
                ->type('#name', $provider['name'])
                ->select('#type', $provider['type'])
                ->type('#cpf_cnpj', $provider['cpf_cnpj'])
                ->press('Gravar')
                ->assertSee($provider['cpf_cnpj']);
        });
    }
    public function testValidation()
    {
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use ($administrator) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/providers#/')
                ->clickLink('Novo')
                ->press('Gravar')
                ->assertSee('O CNPJ não é válido.')
                ->assertSee('O campo Tipo pessoa (PJ ou PF) é obrigatório.')
                ->assertSee('O campo nome é obrigatório.');
        });
    }
    public function testAlter()
    {
        $this->init();
        $provider = static::$providerRaw;
        $randomProviders1 = static::$randomProviders;
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use (
            $provider,
            $randomProviders1,
            $administrator
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/entry-types/' . $randomProviders1['id'] . '#/')
                ->click('#vue-editButton')
                ->type('#name', '*' . $provider['name'] . '*')
                ->press('Gravar')
                ->assertSee('*' . $provider['name'] . '*');
        });
    }
    public function testWrongSearch()
    {
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use ($administrator) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/providers#/')
                ->type('@search-input', '1323e12312vcxvdsf413543445654')
                ->click('@search-button')
                ->waitForText('Nenhum Fornecedor ou Favorecido encontrado')
                ->screenshot('Providers-wrongsearch')
                ->assertSee('Nenhum Fornecedor ou Favorecido encontrado');
        });
    }

    public function testRightSearch()
    {
        $this->init();
        $randomProviders1 = static::$randomProviders;
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use (
            $administrator,
            $randomProviders1
        ) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/providers#/')
                ->type('@search-input', $randomProviders1['name'])
                ->click('@search-button')
                ->assertSee($randomProviders1['cpf_cnpj'])
                ->assertSee($randomProviders1['type'])
                ->screenshot('Providers-rightsearch');
        });
    }
    public function testCancellation()
    {
        $administrator = static::$administrator;

        $this->browse(function (Browser $browser) use ($administrator) {
            $browser
                ->loginAs($administrator['id'])
                ->visit('admin/providers#/')
                ->clickLink('Novo')
                ->press('#cancelButton')
                ->assertSee('Fornecedores / Favorecidos');
        });
    }
}
