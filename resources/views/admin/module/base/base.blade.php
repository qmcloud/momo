<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="{{asset('vendor/admin/css/skin_0.css')}}">

    <title>@if (!empty($title)){{ $title }}  @else XiaoT商城 @endif</title>
    @yield('head')
</head>
<body id="MyPages">
@yield('content')

@yield('footer')
</body>

<script src="{{asset('vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/admin/js/jquery-ui/jquery.ui.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/admin/js/dialog/dialog.js')}}" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('vendor/admin/js/template.min.js')}}" charset="utf-8"></script>
@yield('scripts')
</html>
