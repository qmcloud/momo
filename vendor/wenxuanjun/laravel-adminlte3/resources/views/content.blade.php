@extends('adminlte::index', ['header' => $header])

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        {{ $header ?: trans('admin.title') }}
                        <small>{{ $description ?: '' }}</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    @if ($breadcrumb)
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                            @foreach($breadcrumb as $item)
                                @if($loop->last)
                                    <li class="breadcrumb-item active">
                                        @if (array_has($item, 'icon'))
                                            <i class="fa fa-{{ $item['icon'] }}"></i>
                                        @endif
                                        {{ $item['text'] }}
                                    </li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ admin_url(array_get($item, 'url')) }}">
                                            @if (array_has($item, 'icon'))
                                                <i class="fa fa-{{ $item['icon'] }}"></i>
                                            @endif
                                            {{ $item['text'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    @elseif(config('admin.enable_default_breadcrumb'))
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>   
                            @for($i = 2; $i <= count(Request::segments()); $i++)
                                <li class="breadcrumb-item">
                                {{ucfirst(Request::segment($i))}}
                                </li>
                            @endfor
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            @include('adminlte::partials.alerts')
            @include('adminlte::partials.exception')
            @include('adminlte::partials.toastr')

            {!! $content !!}

        </div>
    </div>
@endsection