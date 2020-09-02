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

<table id="auditsTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Usuário</th>
            <th>E-mail</th>
            <th>Url</th>
            <th>IP</th>
            <th>Data de modificação</th>
            <th>Data do último login</th>
        </tr>
    </thead>

    @forelse ($audits as $audit)
        <tr>
            <td>
                {{ $audit->user->name ?? '' }}
            </td>
            <td>
                {{ $audit->user->email ?? '' }}
            </td>
            <td>
                {{ $audit->url }}
            </td>
            <td>
                {{ $audit->ip_address }}
            </td>
            <td>
                {{ $audit->formatted_created_at }}
            </td>
            <td>
                {{ $audit->user->last_login_at ?? ''}}
            </td>
        </tr>
    @empty
        <p>Nenhum dado encontrado</p>
    @endforelse
</table>
