<nav class="main-header navbar navbar-expand fixed-top navbar-light border-bottom navbar-white">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="javascript:;"><i class="fa fa-bars"></i></a>
        </li>
        {!! Admin::getNavbar()->render('left') !!}
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        {!! Admin::getNavbar()->render() !!}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ Admin::user()->avatar }}" class="img-circle mr-1" width="30" alt="User Image">
                <span class="hidden-xs">{{ Admin::user()->name }}</span>
            </a>
            <div class="dropdown-menu">
                <a href="{{ admin_base_path('auth/setting') }}" class="dropdown-item">{{ trans('admin.setting') }}</a>
                <a href="{{ admin_base_path('auth/logout') }}" class="dropdown-item">{{ trans('admin.logout') }}</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="javascript:;"><i class="fa fa-th-large"></i></a>
        </li>
    </ul>
</nav>