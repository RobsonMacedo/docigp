@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

@if(session()->has('warning'))
    <div class="alert alert-warning">
        {{ session()->get('warning') }}
    </div>
@endif

<table id="providersTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>CPF_CNPJ</th>
        <th>Tipo de Pessoa</th>
        <th>Nome</th>
        <th>Bloqueado pelo DOCIGP</th>
    </tr>
    </thead>

    @forelse ($providers as $provider)
        <tr>
            <td>
                <a href="{{ route('providers.show', ['id' => $provider->id]) }}">{{ $provider->cpf_cnpj }}</a>
            </td>
            <td>
                {{ $provider->type }}
            </td>
            <td>
                {{ $provider->name }}
            </td>
            <td>
                {{$provider->is_blocked ? 'Sim' : 'Não'}}
            </td>
        </tr>
    @empty
        <p>Nenhum Fornecedor ou Favorecido encontrado</p>
    @endforelse
</table>
