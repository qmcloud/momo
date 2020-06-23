<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>訊息</title>
    <link rel="stylesheet" href="/public/home/css/mstyle.css">
    <!-- banner slideshow 用 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.caroufredsel/6.2.1/jquery.carouFredSel.packed.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.2.5/js/swiper.min.js"></script>
    <script src="https://use.fontawesome.com/26d5579a34.js"></script>
</head>
<body>

    <main class="" id="app">

        <header id="myHeader" class="scroll-sticky">
            <div class="header--title-bar">
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
                    <a href="/index.php?m=page&a=game">
                        <img src="/public/images/icon-gamezone.png" width="56" alt="">
                    </a>
                </div>
            </div>
        </header>

        <section class="contents">
            <div class="h50"></div>
            <div class="messages">
                <div class="messages--header">
                    <div></div>
                    <div><h3 class="bold">涼涼</h3></div>
                    <div>
                        <a href=""><i class="fa fa-lg fa-times-circle text-brown"></i></a>
                    </div>
                </div>
                <div class="messages--body">
                    <div class="chats">
                        <p class="chats-times">下午 11:12</p>
                        <ul class="chats-lists">
                            <li>
                                <div class="avatar">
                                    <i class="icon circle size45">
                                        <img src="https://haircatalog-images-cached.appnt.me/hairstyles/1042/24648/lle391e62a9d24889bcdeb103fb25b0e78.jpg" alt="">
                                    </i>
                                </div>
                                <div>
                                    <div class="chats-bubble">
                                        <p>基金就是基金經理公司依法募集投資人的資金，將小錢匯聚成大錢，並受託代為管</p>
                                    </div>
                                </div>
                            </li>
                            <li class="reverse">
                                <div class="avatar">
                                    <i class="icon circle size45">
                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5fklwQy1n3boe-920ipFXkStE2qFIURLPOFJSyQJTtkMLOnxo&s" alt="">
                                    </i>
                                </div>
                                <div>
                                    <div class="chats-bubble">
                                        <p>基金就是基金經理公司</p>
                                    </div>
                                </div>
                            </li>
                            <li class="reverse">
                                <div class="avatar">
                                    <i class="icon circle size45">
                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5fklwQy1n3boe-920ipFXkStE2qFIURLPOFJSyQJTtkMLOnxo&s" alt="">
                                    </i>
                                </div>
                                <div>
                                    <div class="chats-bubble">
                                        <p>基金就是基金</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="avatar">
                                    <i class="icon circle size45">
                                        <img src="https://haircatalog-images-cached.appnt.me/hairstyles/1042/24648/lle391e62a9d24889bcdeb103fb25b0e78.jpg" alt="">
                                    </i>
                                </div>
                                <div>
                                    <div class="chats-bubble">
                                        <p>將小錢匯聚成大錢，並受託代為管</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>



        </section>

        <footer class="bg-white top-border">
            <div class="flex">
                <div class="flex-1 mr10">
                    <div class="ui-input-text radius50">
                        <input type="text">
                    </div>
                </div>
                <div>
                    <a href="" class="ui-button _lightblue">送出</a>
                </div>
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
                login(event) {
                    phone = this.$refs.phone.value;
                    pass = this.$refs.pass.value;
                    reg = this.$refs.reg.value;
                    //alert(phone + " -*- " + pass + " -*- " + reg);
                    axios.get('/index.php', {
                        params: {
                            g: 'home',
                            m: 'user',
                            a: 'userLogin',
                            mobile: phone,
                            pass: pass,
                            countrycode: reg,
                        }
                    })
                    .then(function (response) {
                        response= response.data;
                        if (response.errno == '0'){
                            alert(response.errmsg);
                            window.location.href="/";
                        }else{
                            alert(response.errmsg);
                        }
                    })
                        .catch(function (error) {
                        console.log(error);
                    });
                }
        },
    })
</script>
</body>
</html>