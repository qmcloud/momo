<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $form->title() }}</h3>

        <div class="card-tools">
            {!! $form->renderTools() !!}
        </div>
    </div>
    {!! $form->open(['class' => "form-horizontal"]) !!}

    <div class="card-body">

        @if(!$tabObj->isEmpty())
            @include('adminlte::form.tab', compact('tabObj'))
        @else
            <div class="fields-group">

                @if($form->hasRows())
                @foreach($form->getRows() as $row)
                {!! $row->render() !!}
                @endforeach
                @else
                @foreach($form->fields() as $field)
                {!! $field->render() !!}
                @endforeach
                @endif

            </div>
        @endif

    </div>

    {!! $form->renderFooter() !!}

    @foreach($form->getHiddenFields() as $field)
        {!! $field->render() !!}
    @endforeach

    {!! $form->close() !!}
</div>