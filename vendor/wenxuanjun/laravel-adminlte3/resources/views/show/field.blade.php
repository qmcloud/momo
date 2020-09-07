<div class="form-group row">
    <label class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-8">
        @if($wrapped)
        <div class="card">
            <div class="card-body">
                @if($escape)
                    {{ $content }}&nbsp;
                @else
                    {!! $content !!}&nbsp;
                @endif
            </div>
        </div>
        @else
            @if($escape)
                {{ $content }}
            @else
                {!! $content !!}
            @endif
        @endif
    </div>
</div>