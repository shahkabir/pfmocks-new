@extends('layouts.app')

@section('content')
<h1>Welcome {{ auth()->user()->name }}</h1>

@if(auth()->user()->role === 'admin')
<p>Admin dashboard</p>
@else
<p>User dashboard</p>
@endif
@endsection
