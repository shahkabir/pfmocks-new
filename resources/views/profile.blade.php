@extends('layouts.app')

@section('content')
<h2>Profile</h2>
<p>Name: {{ auth()->user()->name }}</p>
<p>Email: {{ auth()->user()->email }}</p>
@endsection
