@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Agent Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }}!</p>
</div>
@endsection
