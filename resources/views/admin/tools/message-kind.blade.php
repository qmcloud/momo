<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $label)
    <label class="btn btn-default btn-sm {{ \Request::get('kind', 'inbox') == $option ? 'active' : '' }}">
        <input type="radio" class="message-kind" value="{{ $option }}">{{$label}}
    </label>
    @endforeach
</div>