@extends('layouts.app')

@section('content')
@if(auth()->check())

<div id="vue-app">
            
    <app_view></app_view>
        
</div>                  

@endif
@endsection