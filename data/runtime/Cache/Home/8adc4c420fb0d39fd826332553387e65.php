<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>直撥首頁</title>
    <link rel="stylesheet" href="/public/home/css/mstyle.css">
    <!-- banner slideshow 用 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.caroufredsel/6.2.1/jquery.carouFredSel.packed.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.2.5/js/swiper.min.js"></script>
    <script src="https://use.fontawesome.com/26d5579a34.js"></script>
    <script>
    jQuery(document).ready(function() {
      "use strict";
      $(".carousel").carouFredSel({
        responsive: true,
        width: "100%",
        circular: true,
        scroll: {
          item: 1,
          duration: 500,
          pauseOnHover: true
        },
        auto: true,
        items: {
          visible: {
            min: 1,
            max: 1
          },
          height: "variable"
        },
        pagination: {
          container: ".sliderpagnation",
          anchorBuilder: false
        }
      });

      // 秀menu
      $('.sliderow').hide();
      $('#showSliderow').click(function(){
          $('.sliderow').slideToggle();
      });
    });

    </script>
</head>
<body>

    <main class="" id="app">
        <div class="ui-dialog">
            <div class="window">
                <div class="window--contents">
                    <h3 class="dialog-title">提示</h3>
                    <p class="dialog-subtitle">本房間為計時房間，每分鐘需支付<br>
                        <span class="text-red bold">500</span>&nbsp;<span class="bold">GP</span>
                    </p>
                </div>
                <div class="window--buttons">
                    <a @click="cancel">取消</a>
                    <div class="ui-divider"></div>
                    <a  @click="cancel" class="text-red">確定</a>
                </div>
            </div>
        </div>
        <header id="myHeader" class="scroll-sticky">
            <div class="header--title-bar">
                <div class="flex">
                    <a href=""><i class="icon size30 icon-navi_back"></i></a>
                    <a href=""><i class="icon size30 icon-search"></i></a>
                    <a href=""><i class="icon size30 icon-user"></i></a>

                </div>
                <div class="flex">
                    <img src="/public/images/logo-ighot-inner.png" width="150" alt="">
                </div>
                <div class="flex">
                    <a href="">
                        <img src="/public/images/icon-gamezone.png" width="56" alt="">
                    </a>
                </div>
            </div>
            <div class="slider">
                <ul class="carousel" id="carousel">
                  <li>
                      <img class="w100p" src="http://s1.picswalls.com/wallpapers/2015/09/20/beautiful-hd-wallpaper-2015_111526537_269.jpg" alt="">
                  </li>
                  <li>
                      <img class="w100p" src="http://4.bp.blogspot.com/-oxlezteeOII/TfiTImj4RlI/AAAAAAAAA1k/UAgctmU5VZo/s1600/Widescreen+Unique+And+Beautiful+Photography+%25284%2529.jpg" alt="">
                     <!-- <h1>Slide 2</h1>
                     <p>For desktops: hovering pauses slide.</p>
                     <p>For mobile: ?! Fork me! -->
                  </li>
                  <li>
                      <img class="w100p" src="https://i.pinimg.com/originals/cf/ed/a0/cfeda03d01da4b779093c9d928c01dee.jpg" alt="">
                     <!-- <h1>Slide 3</h1>
                     <p>For desktops: hovering pauses slide.</p>
                    <p>For mobile: ?! Fork me!</p> -->
                  </li>
                </ul>
                <ul class="sliderpagnation">
                    <li><a href="#"><i class="fa fa-circle"></i></a></li>
                    <li><a href="#"><i class="fa fa-circle"></i></a></li>
                    <li><a href="#"><i class="fa fa-circle"></i></a></li>
                </ul>
            </div>
            <div class="catalog-menu">
                <ul class="row">
                    <li>
                        <a href="">
                            <i class="icon icon-catalog1 size32"></i>
                            <p>男神</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon icon-catalog2 size32"></i>
                            <p>男神</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon icon-catalog3 size32"></i>
                            <p>男神</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon icon-catalog4 size32"></i>
                            <p>男神</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon icon-catalog5 size32"></i>
                            <p>男神</p>
                        </a>
                    </li>
                    <li>
                        <a id="showSliderow" href="javascript:void(0)">
                            <i class="icon icon-plus size10 mb10 mt10"></i>
                            <p>全部</p>
                        </a>
                    </li>
                </ul>
                <div class="sliderow">
                    <ul class="row">
                        <li>
                            <a href="">
                                <i class="icon icon-catalog6 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="icon icon-catalog7 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="icon icon-catalog8 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="icon icon-catalog9 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="icon icon-catalog10 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="icon icon-catalog11 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="row">
                        <li>
                            <a href="">
                                <i class="icon icon-catalog12 size32"></i>
                                <p>男神</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <section class="contents">
            <div class="h215"></div>
            <div class="live-cube">
                <ul>
                    <li style="background-image: url('http://www.beautynewstokyo.jp/admin/wp-content/uploads/2017/03/hair_170308_main.jpg')">
                        <div class="room-type"><p class="_yellow">密碼房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>1234567890</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('https://imgcp.aacdn.jp/img-a/550/auto/aa/gm/article/4/7/3/5/9/9/201803061234/800__allabout0224m.jpg')">
                        <div class="room-type"><p class="_blue">一般房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>89000</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://haircatalog-images-cached.appnt.me/hairstyles/1042/24648/lle391e62a9d24889bcdeb103fb25b0e78.jpg" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('http://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjEyMDd9');">
                        <div class="room-type"><p class="_pink">付費房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>4560</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://rr.img.naver.jp/mig?src=http%3A%2F%2Fimgcc.naver.jp%2Fkaze%2Fmission%2FUSER%2F20190611%2F16%2F10146966%2F0%2F485x660x12bf464e4ee6b7392a83bfa7.jpg%2F300%2F600&twidth=300&theight=600&qlt=80&res_format=jpg&op=r" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('https://rr.img.naver.jp/mig?src=http%3A%2F%2Fimgcc.naver.jp%2Fkaze%2Fmission%2FUSER%2F20140816%2F35%2F3726865%2F0%2F310x372x751acb0d02a93169f8d9b1e3.jpg%2F300%2F600&twidth=300&theight=600&qlt=80&res_format=jpg&op=r');">
                        <div class="room-type"><p class="_green">門票房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>333</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5fklwQy1n3boe-920ipFXkStE2qFIURLPOFJSyQJTtkMLOnxo&s" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>


                    <li style="background-image: url('http://www.beautynewstokyo.jp/admin/wp-content/uploads/2017/03/hair_170308_main.jpg')">
                        <div class="room-type"><p class="_yellow">密碼房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>1234567890</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('https://imgcp.aacdn.jp/img-a/550/auto/aa/gm/article/4/7/3/5/9/9/201803061234/800__allabout0224m.jpg')">
                        <div class="room-type"><p class="_blue">一般房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>89000</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://haircatalog-images-cached.appnt.me/hairstyles/1042/24648/lle391e62a9d24889bcdeb103fb25b0e78.jpg" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('http://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjEyMDd9');">
                        <div class="room-type"><p class="_pink">付費房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>4560</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://rr.img.naver.jp/mig?src=http%3A%2F%2Fimgcc.naver.jp%2Fkaze%2Fmission%2FUSER%2F20190611%2F16%2F10146966%2F0%2F485x660x12bf464e4ee6b7392a83bfa7.jpg%2F300%2F600&twidth=300&theight=600&qlt=80&res_format=jpg&op=r" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                    <li style="background-image: url('https://rr.img.naver.jp/mig?src=http%3A%2F%2Fimgcc.naver.jp%2Fkaze%2Fmission%2FUSER%2F20140816%2F35%2F3726865%2F0%2F310x372x751acb0d02a93169f8d9b1e3.jpg%2F300%2F600&twidth=300&theight=600&qlt=80&res_format=jpg&op=r');">
                        <div class="room-type"><p class="_green">門票房型</p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p>333</p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5fklwQy1n3boe-920ipFXkStE2qFIURLPOFJSyQJTtkMLOnxo&s" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name">小魚泡泡...</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>



        </section>

        <footer>
            <div class="tabbar">
                <a href=""><i class="icon icon-live size35"></i></a>
                <a href="/index.php?m=page&a=room1v1"><i class="icon icon-1v1 size35"></i></a>
                <a href=""><i class="icon icon-video size35"></i></a>
                <a href=""><i class="icon icon-heart2 size35"></i></a>
                <a href=""><i class="icon icon-shop size35"></i></a>
                <a href="/index.php?m=page&a=msg"><i class="icon icon-chat size35"></i></a>
                <a href="/index.php?m=page&a=login" class="no-login"><i class="icon icon-myL size35"></i></a>
            </div>
        </footer>
    </main>

    <!-- <div class="outer-frame">
        <a class="goBig" href="javascript:void()">放大</a>
        <iframe src="https://demo.v5sm.com/game/index.html" frameborder="0" width="100%" height="300"></iframe>
    </div> -->

    <script src="js/main.js"></script>
<script src="https://cdn.staticfile.org/axios/0.18.0/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>

<script>
    // set default csrf token
    axios.defaults.withCredentials = true;
    axios.defaults.xsrfCookieName = 'csrftoken';
    axios.defaults.xsrfHeaderName = "X-CSRFToken";
    let response = null;
    new Vue({
        el: '#app',
        data:{
            info: null,
            phone: null,
            pass: null,
            reg: null,
        },
        methods: {
                cancel(event) {
                    window.history.back();
                }
        },
    })
</script>
</body>
</html>