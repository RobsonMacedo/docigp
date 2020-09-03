@extends('layouts.app')

@section('content')
    <div class="col-md-12" id="vue-admin">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="col-sm-12 col-md-6 col-lg-4">
                @include('partials.tile', [
                'route' => route('entries.index'),
                'title' => 'Prestação de contas',
                'color' => 'green',
                'icon' => 'fa fa-check-circle fa-5x'
                ])
            </div>

            @can('parties:show')
                <div class="col-sm-12 col-md-6 col-lg-4">
                    @include('partials.tile', [
                    'route' => route('parties.index'),
                    'title' => 'Partidos',
                    'color' => 'orange',
                    'icon' => 'fa fa-handshake fa-5x'
                    ])
                </div>
                @endCan

                @can('legislatures:show')
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        @include('partials.tile', [
                        'route' => route('legislatures.index'),
                        'title' => 'Legislaturas',
                        'color' => 'purple',
                        'icon' => 'fa fa-calendar fa-5x'
                        ])
                    </div>
                    @endCan

                    @can('congressmen:show')
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            @include('partials.tile', [
                            'route' => route('congressmen.index'),
                            'title' => 'Deputados',
                            'color' => 'red',
                            'icon' => 'fa fa-landmark fa-5x'
                            ])
                        </div>
                        @endCan

                        @can('users:show')
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                @include('partials.tile', [
                                'route' => route('users.index'),
                                'title' => 'Usuários',
                                'color' => 'blue',
                                'icon' => 'fa fa-users fa-5x'
                                ])
                            </div>
                            @endCan

                            @can('providers:show')
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    @include('partials.tile', [
                                    'route' => route('providers.index'),
                                    'title' => 'Fornecedores / Favorecidos',
                                    'color' => 'green',
                                    'icon' => 'fa fa-store fa-5x'
                                    ])
                                </div>
                                @endCan

                                @can('cost-centers:show')
                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                        @include('partials.tile', [
                                        'route' => route('cost-centers.index'),
                                        'title' => 'Centros de custo',
                                        'color' => 'purple',
                                        'icon' => 'fa fa-donate fa-5x'
                                        ])
                                    </div>
                                    @endCan

                                    @can('entry-types:show')
                                        <div class="col-sm-12 col-md-6 col-lg-4">
                                            @include('partials.tile', [
                                            'route' => route('entry-types.index'),
                                            'title' => 'Tipos de lançamento',
                                            'color' => 'red',
                                            'icon' => 'fa fa-clipboard fa-5x'
                                            ])
                                        </div>
                                        @endCan

                                        @can('audit')
                                            <div class="col-sm-12 col-md-6 col-lg-4">
                                                @include('partials.tile', [
                                                'route' => route('audits.index'),
                                                'title' => 'Auditorias',
                                                'color' => 'blue',
                                                'icon' => 'fas fa-user-tie fa-5x',
                                                ])
                                            </div>
                                            @endCan
                                        </div>
                                    </div>
                                @endsection
