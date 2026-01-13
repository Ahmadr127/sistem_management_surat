{{-- 
    This is a wrapper layout for backward compatibility
    All views using @extends('home') will automatically get the new sidebar
--}}
@extends('layouts.app')

@section('content')
    @yield('content')
@endsection
