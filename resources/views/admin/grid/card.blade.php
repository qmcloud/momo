<div class="box">
    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    <div class="box-header with-border">
        <div class="pull-right">
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>
        <span>
            {!! $grid->renderHeaderTools() !!}
        </span>
    </div>

    {!! $grid->renderFilter() !!}

    <!-- /.box-header -->
    <div class="box-body">
        <ul class="mailbox-attachments clearfix">
            @foreach($grid->rows() as $row)
                <li>
                    <span class="mailbox-attachment-icon has-img">
                        {!! $row->column('image') !!}
                    </span>
                    <div class="mailbox-attachment-info">
                        <a href="#" class="mailbox-attachment-name">
                            {!! $row->column('caption') !!}
                        </a>
                        <br />
                        {!! $row->column('author.name') !!}
                        <br />
                        <span class="mailbox-attachment-size">
                              {!! $row->column('__row_selector__') !!}
                            <span class="pull-right">
                                {!! $row->column('__actions__') !!}
                            </span>
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>

    </div>
    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>