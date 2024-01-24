@extends('core.fm')

@section('content')

    <!-- Navbar -->
    @include('layouts.admin.nav')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('layouts.file_manager.sidebar')
    <!-- /.Main Sidebar Container -->

    <!-- ContentController Wrapper. Contains page content -->
    @include('layouts.file_manager.content')
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    @include('layouts.admin.controlbar')
    <!-- /.control-sidebar -->

    <!-- Admin Footer -->
    @include('layouts.admin.footer')

@endsection


