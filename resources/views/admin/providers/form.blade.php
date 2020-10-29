@extends('layouts.app')

@section('content')
    <div class="card card-default" id="vue-providers">
        <form name="formulario" id="formulario" @if(formMode() == 'show') action="{{ route('providers.update', ['id' => $provider->id]) }}" @else action="{{ route('providers.store')}}" @endIf method="POST">
            {{ csrf_field() }}

            <input name="id" type="hidden" value="{{$provider->id}}" id="id">

            <div class="card-header">
                <div class="row">
                    <div class="col-sm-8 align-self-center">
                        <h4 class="mb-0">
                            <a href="{{ route('providers.index') }}">Favorecidos</a>

                            @if(is_null($provider->id))
                                > NOVA
                            @else
                                > {{ $provider->cpf_cnpj }} - {{ $provider->name }}
                            @endif
                        </h4>
                    </div>

                    <div class="col-sm-4 align-self-center d-flex justify-content-end">
                        @include('partials.edit-button', ['model'=>$provider])
                        @include('partials.save-button', ['model'=>$provider, 'backUrl' => 'providers.index'])
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('partials.alerts')
                @if ($errors->has('cpf_cnpj'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('cpf_cnpj') }}
                    </div>
                @endif
                @if ($errors->has('type'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                @if ($errors->has('name'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('name') }}
                    </div>
                @endif

                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="cpf_cnpj">CPF / CNPJ</label>
                                <input
                                    class="form-control"
                                    name="cpf_cnpj"
                                    id="cpf_cnpj"
                                    v-mask="['###.###.###-##', '##.###.###/####-##']"
                                    value="{{is_null(old('cpf_cnpj')) ? $provider->cpf_cnpj : old('cpf_cnpj')}}"
                                    @include('partials.disabled', ['model'=>$provider])
                                />
                            </div>

                            <div class="form-group">
                                <label for="type">Tipo Pessoa</label>
                                <select class="custom-select" name="type" id="type"  @include('partials.disabled', ['model'=>$provider])>
                                    <option value="">Selecione</option>
                                    <option value="PF" {{(is_null(old('type')) ? $provider->type : old('type')) == 'PF' ? 'selected=selected' : ''}}>Pessoa Física</option>
                                    <option value="PJ" {{(is_null(old('type')) ? $provider->type : old('type')) == 'PJ' ? 'selected=selected' : ''}}>Pessoa Jurídica</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input class="form-control" name="name" id="name" value="{{is_null(old('name')) ? $provider->name : old('name')}}"  @include('partials.disabled', ['model'=>$provider])/>
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="hidden" name="is_blocked" value="false">
                                <input type="checkbox" name="is_blocked" id="is_blocked" {{ $provider->is_blocked == 1 ? 'checked="checked"' : '' }}  @include('partials.disabled', ['model'=>$provider])/>
                                <label for="is_blocked">Bloqueado pela DOCIGP</label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
