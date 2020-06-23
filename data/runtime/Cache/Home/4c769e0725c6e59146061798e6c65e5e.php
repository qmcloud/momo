<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>手機簡訊登入</title>
    <link rel="stylesheet" href="/public/home/css/mstyle.css">
</head>
<body>
    <main class="index" id="app">
        <header id="myHeader">
            <!-- <div class="system-bar">
                <div>中華電信</div>
                <div>9:41 AM</div>
                <div>100%</div>
            </div> -->
            <div class="header--title-bar">
                <div class="flex">
                    <a href="#" onclick="history.back()"><i class="icon size30 icon-navi_back"></i></a>
                </div>
                <!-- <div class="flex">
                    <a href=""><i class="icon size30 icon-search"></i></a>
                    <a href=""><i class="icon size30 icon-user"></i></a>
                </div> -->
            </div>
        </header>
        <section class="contents text-center">
        <a href="/">
            <img class="mb30" src="/public/images/logo-ighot.png" width="240" alt=""></a>
            <div class="margin-center w225">
                <div class="button-icon-circle _phone mb20">
                    <i class="icon icon-phone size25"></i>
                    <p>手機簡訊註冊登入</p>
                </div>
                <div class="ui-combo-input _black mb25">
                    <div>
                        <select name="" id="" ref="reg">
                            <option value="00886">+886</option>
                            <option value="0086">+86</option>
                        </select>
                    </div>
                    <div>
                        <div class="ui-divider sm"></div>
                    </div>
                    <div>
                        <input type="text" placeholder="請輸入手機號碼" ref="phone">
                    </div>
                </div>
                <div class="ui-combo-input _black mb10">
                    <div>
                        <input type="password" placeholder="請輸入密碼" ref="pass">
                    </div>
                </div>
                <a @click="login" class="ui-button _orange mb10">立即登入</a>
                <a href="/index.php?m=page&a=registered" class="ui-button _blue mb10">立即註冊</a>
                <a href="/index.php?m=page&a=forgetpass" class="ui-button _grey mb10">忘記密碼</a>
                <div class="flex child-between mt20" style="display:none;">
                    <a href=""><i class="icon icon-facebook-color size35"></i></a>
                    <a href=""><i class="icon icon-line-color size35"></i></a>
                    <a href=""><i class="icon icon-wechat-color size35"></i></a>
                    <a href=""><i class="icon icon-google-color size35"></i></a>
                </div>
            </div>
            <div class="mt40">
                <p class="fz-lg text-white mb10">登入後體驗更精彩</p>
                <p>
                    <span class="text-white">登入後即代表同意</span>
                    <a class="text-button-outline" href="/index.php?m=page&a=agreement">服務與隱私條款</a>
                </p>
            </div>
            <div class="banner-login">
                <img src="/public/images/banner02.jpg" alt="">
            </div>
        </section>
    </main>
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