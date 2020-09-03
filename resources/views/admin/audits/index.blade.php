@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> {{-- Aguardando solução de bug - Issue aberta --}}
<div class="card card-default">
    
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    
                    <h4 class="mb-0">Auditorias</h4></div>
                    <app-audits-button id='vue-audits' route="{{route('audits.export')}}"></app-audits-button>
                    

            </div>
            

            {{-- <div class="col-md-9">--}}
            {{-- @include(--}}
            {{-- 'layouts.partials.search-form',--}}
            {{-- [--}}
            {{-- 'routeSearch' => 'entry-types.index',--}}
            {{-- 'routeCreate' => 'entry-types.create',--}}
            {{-- ]--}}
            {{-- )--}}
            {{-- </div>--}}

        </div>
    </div>

    <div class="card-body">
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif

        @include('admin.audits.partials.table')
    </div>
</div>
@endsection