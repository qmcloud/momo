<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>遊戲大廳</title>
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
    <main class="">
        <header id="myHeader" class="scroll-sticky">
            <div class="header--title-bar floating">
                <div class="flex">
                    <a href="#" onclick="history.back()"><i class="icon size30 icon-navi_back"></i></a>
                    <a href="/index.php?m=page&a=search"><i class="icon size30 icon-search"></i></a>
                    <a href=""><i class="icon size30 icon-user"></i></a>

                </div>
                <div class="flex">
                <a href="/">
                    <img src="/public/images/logo-ighot-inner.png" width="150" alt=""></a>
                </div>
                <div class="flex">
                    <div class="ui-button-rectangle p5 fz-sm text-yellow2 outline border-yellow2 radius10">遊戲大廳</div>
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

        </header>

        <section class="contents">
            <div class="h160"></div>
            <div class="games-anchor-area">
                <div>
                    <a href="" class="inline-block">
                        <img src="/public/images/icon-gamezone.png" width="80" alt="">
                    </a>
                </div>
                <div>
                    <div class="title-cube mr5">在線<br>主播</div>
                </div>
                <div class="scroll-contents flex-1">
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                    <a href="">
                        <i class="icon circle size47">
                            <img class="w100p" src="https://lee.hpplus.jp/wp-content/uploads/2016/11/d6bd8c06bb0fd1ead5d24f4ddd4b4843.jpg" alt="">
                        </i>
                    </a>
                </div>
            </div>
            <div class="games-banner">
                <figure>
                    <img src="/public/images/banner-games1.jpg" alt="">
                    <figcaption>
                        <div class="block">10位主播在線</div>
                    </figcaption>
                </figure>
                <figure>
                    <img src="/public/images/banner-games2.jpg" alt="">
                    <figcaption>
                        <div class="block">10位主播在線</div>
                    </figcaption>
                </figure>
                <figure>
                    <img src="/public/images/banner-games3.jpg" alt="">
                    <figcaption>
                        <div class="block">10位主播在線</div>
                    </figcaption>
                </figure>
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

</body>
</html>