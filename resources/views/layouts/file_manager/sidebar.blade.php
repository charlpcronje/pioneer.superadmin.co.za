<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="{{asset('images/Pioneer_Hi-Bred_International_Logo.png')}}" a
             lt="{{ config('app.name', 'Laravel') }}"
             class="img-responsive brand-image img-square elevation-3"
             style="opacity: .8; max-width: 50px">
        <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="info">
                <a class="d-block" href="#">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->




                <li class="nav-item">

                        <a href="/dashboard"   class="nav-link @if (preg_match('/dasboard/', URL::current()))  active @endif ">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Dashboard</p>
                        </a>
                </li>

                <li class="nav-item">

                        <a href="/users/permission"   class="nav-link @if (preg_match('/users\/permission/', URL::current()))  active @endif">
                        <i class="fas fa-cog nav-icon"></i>
                        <p>Manage Permissions</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/users/types"   class="nav-link">
                        <i class="fas fa-cog nav-icon" class="nav-link @if (preg_match('/users\/types/', URL::current()))  active @endif"></i>
                        <p>Manage Roles</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users/index"  class="nav-link @if (preg_match('/users\/index/', URL::current()))  active @endif">
                        <i class="fas fa-cog nav-icon"></i>
                        <p>Manage Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/file_manager"   class="nav-link @if (preg_match('/file_manager/', URL::current()))  active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Files</p>
                    </a>
                </li>
                <li class="nav-item">
                    <router-link tag="a" to="/file-links" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Links</p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <a href="/content-manager"   class="nav-link @if (preg_match('/content-manager/', URL::current()))  active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Content</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/media-manager"   class="nav-link @if (preg_match('/content-manager/', URL::current()))  active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Videos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/communications"   class="nav-link @if (preg_match('/communications/', URL::current()))  active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Comms</p>
                    </a>
                </li>

                <!--<li class="nav-item">
                    <router-link tag="a" to="/users" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Users</p>
                    </router-link>
                </li>-->

            </ul>
        </nav>
        <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>
