@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="column">
            <admin-leased-items prop-items="{{$items}}"  prop-unit-types="{{$unitTypes}}" prop-users="{{$users}}"/>
        </div>
    </div>
@endsection