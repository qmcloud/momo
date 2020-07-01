<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">现有轮播</h3>
    </div>
    <!-- /.box-header -->
    @if ($specialItem && !$specialItem->carousels->isEmpty())
    <div class="box-body">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">
                @foreach($specialItem->carousels as $item)
                <li data-target="#carousel-example-generic" data-slide-to="{{$loop->index}}"
                @if ($loop->index ==0)
                class="active"
                @endif
                ></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach($specialItem->carousels as $item)
                <div class="item
                @if ($loop->index ==0)
                        active
                @endif
                ">
                    <img src="{{config('filesystems.disks.oss.url').'/'.$item->carousel_img}}" alt="{{$item->carousel_title}}">
                    <div class="carousel-caption">
                        {{$item->carousel_title}}
                    </div>
                </div>
                @endforeach
            </div>
            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                <span class="fa fa-angle-left"></span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                <span class="fa fa-angle-right"></span>
            </a>
        </div>
    </div>
    @endif
    <!-- /.box-body -->
</div>