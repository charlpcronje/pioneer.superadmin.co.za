@extends('core.reset')
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
        @if(session()->has('message'))
        <div class="messageClass"  class="alert alert-dismissible fade show alert-danger" role="alert" >
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        @elseif(session()->has('success'))
            <div class="messageClass"  class="alert alert-dismissible fade show alert-success" role="alert" >
                {{ session()->get('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @else
        <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>

        <form  method="post">
            @csrf()
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Password"  name="password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Confirm Password" name="conf_password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="user_id" value="{{$user_id}}" />
                    @if(isset($tenant))
                    <input type="hidden" name="tenant" value="{{$tenant}}" />
                   @endif
                    <input type="hidden" name="web" value="true" />
                    <button type="submit" class="btn btn-primary btn-block">Change password</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        @endif

    </div>
    <!-- /.login-card-body -->
</div>
<!-- /.content-wrapper -->

@endsection
