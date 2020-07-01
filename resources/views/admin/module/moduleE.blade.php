<style>
    .checkedgoodsImg img {
        width: 60px;
        height: 60px;
    }

    .pad {
        width: 50%;
        height: 20%;
        margin-left: 25%;
        float: left;
    }
</style>
@if ($goodsList &&!$goodsList->isEmpty())
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">已配商品</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <section class="content">
                <div class="row">
                    @foreach($goodsList as $item)
                    <img class="img-responsive pad" src="{{config('filesystems.disks.oss.url').'/'.$item->primary_pic_url}}" alt="Photo">
                    @endforeach
                </div>
            </section>
        </div>
    <!-- /.box-body -->
    </div>
@endif