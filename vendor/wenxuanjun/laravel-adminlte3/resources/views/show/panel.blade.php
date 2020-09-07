<div class="card card-{{ $style }}">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>

        <div class="card-tools">
            {!! $tools !!}
        </div>
    </div>
    <div class="form-horizontal">
        <div class="card-body">
            <div class="fields-group">
                @foreach($fields as $field)
                    {!! $field->render() !!}
                @endforeach
            </div>
        </div>
    </div>
</div>