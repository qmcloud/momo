<div class="card">
    @if(isset($title))
        <div class="card-header">
            <h3 class="card-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )
        <div class="card-header">
            <div class="float-right" style="margin-right: -10px;">
                {!! $grid->renderColumnSelector() !!}
				{!! $grid->renderExportButton() !!}
				{!! $grid->renderCreateButton() !!}
            </div>
            @if ( $grid->showTools() )
            <span>
                {!! $grid->renderHeaderTools() !!}
            </span>
            @endif
        </div>
    @endif

    {!! $grid->renderFilter() !!}
    
    {!! $grid->renderHeader() !!}

    <div class="card-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    @foreach($grid->visibleColumns() as $column)
                    <th class="column-{!! $column->getName() !!}">{{$column->getLabel()}}{!! $column->renderHeader() !!}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($grid->rows() as $row)
                <tr {!! $row->getRowAttributes() !!}>
                    @foreach($grid->visibleColumnNames() as $name)
                    <td {!! $row->getColumnAttributes($name) !!} class="column-{!! $name !!}">
                        {!! $row->column($name) !!}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
            {!! $grid->renderTotalRow() !!}
        </table>

    </div>

    {!! $grid->renderFooter() !!}

    <div class="card-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
</div>
