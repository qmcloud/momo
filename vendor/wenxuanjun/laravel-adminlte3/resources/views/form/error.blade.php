@if(is_array($errorKey))
    @foreach($errorKey as $key => $col)
        @if($errors->has($col.$key))
            @foreach($errors->get($col.$key) as $message)
                <div class="invalid-feedback mb-1"><i class="fa fa-times-circle-o"></i> {{$message}}</div>
            @endforeach
        @endif
    @endforeach
@else
    @if($errors->has($errorKey))
        @foreach($errors->get($errorKey) as $message)
            <div class="invalid-feedback mb-1"><i class="fa fa-times-circle-o"></i> {{$message}}</div>
        @endforeach
    @endif
@endif