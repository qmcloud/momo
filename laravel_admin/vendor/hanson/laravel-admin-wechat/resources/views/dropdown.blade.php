<style>
    .dropdown-item {
        display: block;
        padding: .25rem 1.5rem;
        font-weight: 400;
    }
    .dropdown-item:hover {
        color: #0081ff;
        background: #eee;
    }
    .nav-link .dropdown-toggle:hover {
        color: #0081ff;
    }
</style>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $current['name'] }}
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        @foreach($configs as $config)
            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['app_id' => $config->app_id]) }}">{{ $config->name }}</a>
        @endforeach
    </div>
</li>
