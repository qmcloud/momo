<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <title><?php echo ($site_name); ?></title>
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
            <div class="header--title-bar">
                <div class="flex">
                    <a href=""><i class="icon size30 icon-navi_back"></i></a>
                    <a href="/index.php?m=page&a=search"><i class="icon size30 icon-search"></i></a>
                    <a href=""><i class="icon size30 icon-user"></i></a>

                </div>
                <div class="flex">
                    <img src="/public/images/logo-ighot-inner.png" width="150" alt="">
                </div>
                <div class="flex">
                    <a href="http://www.mo78s.cn/index.php?m=page&a=game">
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
                            <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_12.png');"></i>
                            <p>熱女孩</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_2.png');"></i>
                            <p>舞蹈</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                             <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_5.png');"></i>
                            <p>交友</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                             <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_1.png');"></i>
                            <p>音乐</p>
                        </a>
                    </li>
                    <li>
                        <a href="">
                             <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_4.png');"></i>
                            <p>校园</p>
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
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_6.png');"></i>
                                <p>喊麦</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_7.png');"></i>
                                <p>游戏</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_9.png');"></i>
                                <p>美食</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_10.png');"></i>
                                <p>才艺</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_11.png');"></i>
                                <p>男神</p>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_8.png');"></i>
                                <p>直播购</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="row">
                        <li>
                            <a href="">
                                 <i class="icon  size32" style="background-image: url('http://qiniu.mo78s.cn/liveclass_3.png');"></i>
                                <p>户外</p>
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
                <?php if(is_array($hot)): $i = 0; $__LIST__ = $hot;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li style="background-image: url('<?php echo ($v['thumb']); ?>')" style="cursor:pointer;" onclick="location.href='/<?php echo ($v['uid']); ?>';">
                    <a class="link" href="/<?php echo ($v['uid']); ?>" target="_blank">
                        <div class="room-type"><p class="_yellow">
                        
                        <?php switch($v["type"]): case "0": ?>普通房间<?php break;?>
    <?php case "1": ?>密码房间<?php break;?>    
    <?php case "2": ?>门票房间<?php break;?>
    <?php case "3": ?>计时房间<?php break;?>
    <?php default: ?>普通房间<?php endswitch;?>

                        </p></div>
                        <div class="like-area">
                            <div class="floating-r">
                                <i class="icon icon-heart size30"></i>
                            </div>
                            <div class="floating-l">
                                <p><?php echo ($v['fans_nums']); ?></p>
                            </div>
                        </div>
                        <div class="personal-area">
                            <div>
                                <i class="icon circle">
                                    <img src="<?php echo ($v['avatar_thumb']); ?>" alt="">
                                </i>
                            </div>
                            <div>
                                <p class="name"><?php echo ($v['user_nicename']); ?></p>
                            </div>
                        </div>
                        </a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>



        </section>

        <footer>
            <div class="tabbar hd-login">
                <a href=""><i class="icon icon-live size35"></i></a>
                <a href="/index.php?m=page&a=room1v1"><i class="icon icon-1v1 size35"></i></a>
                <a href=""><i class="icon icon-video size35"></i></a>
                <a href=""><i class="icon icon-heart2 size35"></i></a>
                <a href=""><i class="icon icon-shop size35"></i></a>
                <a href="/index.php?m=page&a=msg"><i class="icon icon-chat size35"></i></a>
                <?php if(!$user): ?><a href="/index.php?m=page&a=login" class="no-login"><i class="icon icon-myL size35"></i></a>
                <?php else: ?>
                <a href="/index.php?m=page&a=personal" class="no-login"><i class="icon icon-myC size35"></i></a><?php endif; ?>
            </div>
        </footer>

</body>
</html>