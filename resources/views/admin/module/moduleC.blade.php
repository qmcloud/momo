<link rel="stylesheet" href="{{asset('vendor/admin/css/swiper.min.css')}}">
<script src="{{asset('vendor/admin/js/swiper.min.js')}}"></script>
<style>
    .checkedgoodsImg img {
        width: 60px;
        height: 60px;
    }

    .pad {
        width: 350px;
        height: 46%;
        display: inline-block;
    }
    .swiper-container {
        width: 100%;
        height: 100%;
    }
    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
        width: 70%;
        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

</style>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">滑块</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        @if ($specialItem && !$specialItem->carousels->isEmpty())
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($specialItem->carousels as $item)
                    <div class="swiper-slide"><img class="img-responsive pad" src="{{config('filesystems.disks.oss.url').'/'.$item->carousel_img}}" alt="{{$item->carousel_title}}"></div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        @endif
    </div>
    <script>
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 'auto',
            spaceBetween: 5,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
    <!-- /.box-body -->
</div>