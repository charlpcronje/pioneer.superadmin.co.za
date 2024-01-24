@extends('core.auth')
@section('content')
<!-- ContentController Wrapper. Contains page content -->
<div class="login-logo">
    <a href="/">
        <img src="{{ asset('images/Pioneer_Hi-Bred_International_Logo.png')}}" class="img-responsive"
             style="max-width:70px">
    </a>
</div>
<!-- /.login-logo -->
<div class="card">

    <div class="card-body login-card-body">
        <p class="login-box-msg">{{$message}}</p>

    </div>
    <!-- /.login-card-body -->
</div>
<!-- /.content-wrapper -->
@endsection




