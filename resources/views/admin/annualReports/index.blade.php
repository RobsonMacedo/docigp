@extends('layouts.app')

@section('content')
<div class="card card-default">
    <div >
        <div class="card-header text-center">
            <h1>Relatórios anuais</h1>
        </div>
        <br>
        <div class="card-body text-center" >
            <form action="{{route('annualReports.index')}}" method="post">
                @csrf   
                <div class="form-row justify-content-center">
                    <div class="form-group col-md-1">
                        <label for="year">Selecione o ano </label>
                        <input class='form-control' type="number" name="year" id="" min='2019' step='1'>
                    </div>
                </div>    
                
                <button type="submit" class='btn btn-primary btn-sm'>Baixar pdf</button>
            </form>
            
        </div>
    </div>
</div>


@endsection
