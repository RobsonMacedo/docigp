@extends('layouts.app')

@section('content')
    <div class="container-fluid" id="vue-users">
        <form name="formUsers" id="formUsers" @if(formMode() == 'show') action="{{ route('users.update', ['id' => $user->id]) }}" @else action="{{ route('users.store')}}" @endIf method="POST">
            {{ csrf_field() }}
            <input name="id" type='hidden' value="{{$user->id}}" id="id" >

            <div class="card card-default">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8 align-self-center">
                            <h4>
                                <a href="{{ route('users.index') }}">Usuários</a>

                                @if(is_null($user->id))
                                    > NOVO
                                @else
                                    > {{ $user->name }}
                                @endif
                            </h4>
                        </div>

                        <div class="col-sm-4 align-self-center d-flex justify-content-end">
                            @include('partials.edit-button', ['model'=>$user])
                            @include('partials.save-button', ['model'=>$user, 'backUrl' => 'users.index'])
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    @if ($errors->has('email'))
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="form-group col-md-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">E-mail ALERJ</label>
                                    <input class="form-control" name="email" id="email" value="{{$user->email}}" @include('partials.disabled', ['model'=>$user])/>
                                </div>
                            </div>

                            @if ($errors->has('name'))
                                <div class="alert alert-danger" role="alert">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <input class="form-control" name="name" id="name" value="{{$user->name}}" @include('partials.disabled', ['model'=>$user])/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input name="roles_array" id="roles_array" type="hidden" v-model="rolesJsonString">

                    <div class="row">
                        <div class="form-group col-md-3">
                            <select size="10" name="all-roles" id="all-roles" class="ui-widget-content ui-corner-all" style="width:250px;">
                                @foreach($assignableRoles as $role)
                                    <option value="{{$role->id}}">{{$role->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3" align="center">
                            <br>
                            <br>
                            <a href="#" id="add-profile-button" class="btn btn-primary" v-on:click="f_add($event)">
                                <i class="fa fa-angle-double-right fa-2x"></i>
                            </a>

                            <br>
                            <br>
                            <a href="#" id="remove-profile-button" class="btn btn-primary pull-right" v-on:click="f_remove($event)">
                                <i class="fa fa-angle-double-left fa-2x"></i>
                            </a>
                        </div>

                        <div class="form-group col-md-3">
                            <select multiple size="10" name="assigned-roles" id="assigned-roles" class="ui-widget-content ui-corner-all" style="width:250px;">
                                @foreach($user->roles as $role)
                                    <option value="{{$role->id}}">{{$role->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
