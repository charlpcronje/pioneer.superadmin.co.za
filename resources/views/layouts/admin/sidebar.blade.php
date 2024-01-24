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
                    <router-link tag="a" to="/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Dashboard</p>
                    </router-link>
                </li>

                    <li class="nav-item">
                        <router-link tag="a" to="/users/permissions" class="nav-link">
                            <i class="fas fa-cog nav-icon"></i>
                            <p>Manage Permissions</p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <router-link tag="a" to="/users/types" class="nav-link">
                            <i class="fas fa-cog nav-icon"></i>
                            <p>Manage Roles</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link tag="a" to="/users/index" class="nav-link">
                            <i class="fas fa-cog nav-icon"></i>
                            <p>Manage Users</p>
                        </router-link>
                    </li>

                <li class="nav-item">
                    <router-link tag="a" to="/file-manager" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Files</p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link tag="a" to="/file-links" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Links</p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link tag="a" to="/content-manager" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Content</p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link tag="a" to="/media-manager" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Videos</p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link tag="a" to="/communications" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Manage Comms</p>
                    </router-link>
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
