<div class="card">
        @if(isset($title))
            <div class="card-header">
                <h3 class="card-title"> {{ $title }}</h3>
            </div>
        @endif
    
        <div class="card-header">
            <div class="float-right" style="margin-right: -10px;">
                {!! $grid->renderExportButton() !!}
                {!! $grid->renderCreateButton() !!}
            </div>
            <span>
                {!! $grid->renderHeaderTools() !!}
            </span>
        </div>
    
        {!! $grid->renderFilter() !!}
    
        <div class="card-body table-responsive no-padding">
            <ul class="mailbox-attachments clearfix">
                @foreach($grid->rows() as $row)
                    <li>
                        <span class="mailbox-attachment-icon has-img">
                            <img src="{!! isset($server) ? $server . '/' . $row->column($image_column) : \Illuminate\Support\Facades\Storage::disk(config('admin.upload.disk'))->url($row->column($image_column)) !!}" alt="Attachment">
                        </span>
                        <div class="mailbox-attachment-info">
                            <a href="#" class="mailbox-attachment-name" style="word-break:break-all;">
                                <i class="fa fa-camera"></i>&nbsp;&nbsp;
                                {!! isset($text_column) ? $row->column($text_column) : '' !!}
                            </a>
                            <span class="mailbox-attachment-size">
                              <input type="checkbox" class="grid-item" data-id="{{ $row->id() }}" />
                                <span class="pull-right">
                                    {!! $row->column('__actions__') !!}
                                    <a href="{!! isset($server) ? $server . '/' . $row->column($image_column) : \Illuminate\Support\Facades\Storage::disk(config('admin.upload.disk'))->url($row->column($image_column)) !!}" target="_blank" download="custom-filename.jpg">
                                        <i class="fa fa-cloud-download"></i>
                                    </a>
                                </span>
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    
        <div class="card-footer clearfix">
            {!! $grid->paginator() !!}
        </div>
    </div>