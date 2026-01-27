@extends('layouts.base')

@section('body')
    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset

    @include('partials.footer')
@endsection
