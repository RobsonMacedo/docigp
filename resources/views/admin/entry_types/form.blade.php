@extends('layouts.app')

@section('content')
    <div class="card card-default" id="vue-entry_types">
        <form name="formulario" id="formulario" @if(formMode() == 'show') action="{{ route('entry-types.update', ['id' => $entryType->id]) }}" @else action="{{ route('entry-types.store')}}" @endIf method="POST">
            {{ csrf_field() }}
            <input name="id" type="hidden" value="{{ $entryType->id }}" id="id" >

            <div class="card-header">
                <div class="row">
                    <div class="col-sm-8 align-self-center">
                        <h4 class="mb-0">
                            <a href="{{ route('entry-types.index') }}">Tipos de Lançamentos</a>

                            @if(is_null($entryType->id))
                                > NOVA
                            @else
                                > {{ $entryType->name }}
                            @endif
                        </h4>
                    </div>

                    <div class="col-sm-4 align-self-center d-flex justify-content-end">
                        @include('partials.edit-button', ['model'=>$entryType])
                        @include('partials.save-button', ['model'=>$entryType, 'backUrl' => 'entry-types.index'])
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('partials.alerts')
                @if ($errors->has('name'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                @if ($errors->has('document_number_required'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('document_number_required') }}
                    </div>
                @endif

                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input class="form-control" name="name" id="name" value="{{$entryType->name}}"  @include('partials.disabled', ['model'=>$entryType])/>
                            </div>

                            <div class="form-group">
                                <label for="document_number_required">Este tipo de lançamento exige número de documento</label>
                                <input class="form-control" type="hidden" name="document_number_required" value="false">
                                <input type="checkbox" name="document_number_required" id="document_number_required" {{ $entryType->document_number_required == 1 ? 'checked="checked"' : '' }}   @include('partials.disabled', ['model'=>$entryType])/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
