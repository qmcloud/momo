@if(Admin::user()->visible($item['roles']) && (empty($item['permission']) ?: Admin::user()->can($item['permission'])))
    @if(!isset($item['children']))
        <li class="nav-item">
            @if(url()->isValidUrl($item['uri']))
                <a href="{{ $item['uri'] }}" target="_blank" class="nav-link">
            @else
                 <a href="{{ admin_base_path($item['uri']) }}" class="nav-link">
            @endif
                <i class="fa {{$item['icon']}} nav-icon"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <p>{{ __($titleTranslation) }}</p>
                @else
                    <p>{{ $item['title'] }}</p>
                @endif
            </a>
        </li>
    @else
        <li class="nav-item has-treeview">
            <a href="javascript:;" class="nav-link">
                <i class="fa {{ $item['icon'] }} nav-icon"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <p>{{ __($titleTranslation) }}<i class="right fa fa-angle-left"></i></p>
                @else
                    <p>{{ $item['title'] }}<i class="right fa fa-angle-left"></i></p>
                @endif
            </a>
            <ul class="nav nav-treeview">
                @foreach($item['children'] as $item)
                    @include('admin::partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif