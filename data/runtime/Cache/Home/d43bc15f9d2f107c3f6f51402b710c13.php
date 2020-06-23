<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>立即註冊</title>
    <link rel="stylesheet" href="/public/home/css/mstyle.css">
		<script type="text/javascript" src="/public/home/js/login.js"></script> 
		<script type="text/javascript" src="/public/home/js/layer.js"></script> 
</head>
<body>
    <main class="index" id="app">
        <header id="myHeader">
            <div class="header--title-bar">
                <div class="flex">
                    <a href="#" onclick="history.back()"><i class="icon size30 icon-navi_back"></i></a>
                </div>
            </div>
        </header>
        <section class="contents text-center">
            <img class="mb30" src="/public/images/logo-ighot.png" width="240" alt="">
            <div class="margin-center w225">
                <div href="#" class="ui-button _blue mb20">立即註冊</div>
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
                </div><img v-bind:src="imgUrl" style="margin-bottom:10px" @click="get_img">
                    <div class="ui-combo-input _black mb25">
                    
                    <div class="mr15">
                        <i class="icon icon-key size25"></i>
                    </div>
                    <div>
                        <input type="text" placeholder="請輸入圖形驗證碼" ref="captcha">
                    </div>
                </div>
                <a href="#" @click="get_sms" class="ui-button-rectangle _pink mb25 js_reg_getcode">獲取簡訊<br>認證碼</a>
                <div class="ui-combo-input _black mb25">
                    <div>
                        <input type="text" placeholder="請輸入簡訊認證碼" ref="smscaptcha">
                    </div>
                </div>
                <div class="ui-combo-input _black mb25">
                    <div class="mr15">
                        <i class="icon icon-key size25"></i>
                    </div>
                    <div>
                        <input type="password" placeholder="請自訂密碼輸入" ref="pass">
                    </div>
                </div>
                <div class="ui-combo-input _black mb25">
                    <div class="mr15">
                        <i class="icon icon-key size25"></i>
                    </div>
                    <div>
                        <input type="password" placeholder="請再次確認密碼" ref="pass2">
                    </div>
                </div>
                <a href="#" class="ui-button _orange js-login" @click="reg_go">註冊並登入</a>

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
    let imginit = null;
    let datetimestring = new Date().getTime();
    //alert(datetimestring);
    imginit = axios.get('/index.php?g=home&m=user&a=getCaptcha&_=' + datetimestring)
        .then(function (response) {
            config = response.data;
            //alert(config.data.captcha,);
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(function () { });
    imginit.then(function () {
        let app = new Vue({
        el: '#app',
        data:{
            imgUrl: config.data.captcha + "&v=" + datetimestring,
        },
        methods: {
                get_img(event) {
                    axios.get('/index.php?g=home&m=user&a=getCaptcha&_=' + datetimestring
                    ).then(function (response) {
                        if (response.data.errno == "0") {
                            let datetimestring = new Date().getTime();
                            app.imgUrl=response.data.data.captcha + "&v=" + datetimestring;
                            //alert(response.data.data.captcha + "&v=" + datetimestring);
                        } else {
                            alert(response.data.errmsg);
                        }
                    }).catch(function (response) {
                        alert('Failed')
                    });
                },
                get_sms(event){
                    datetimestring = new Date().getTime();
                    phone = this.$refs.phone.value;
                    reg = this.$refs.reg.value;
                    captcha = this.$refs.captcha.value;
                    if(phone==""){
                        alert("請輸入手機號碼！！");
                        return false
                    }
                    if(captcha==""){
                        alert("請輸入圖形驗證碼");
                        return false
                    }
                    //alert("phone :\n" + phone + "\ncaptcha ：\n" + captcha + "\ncountrycode :\n" + reg + "\n_ :\n" + datetimestring);
                    axios.get('/index.php', {
                        params: {
                            g: 'home',
                            m: 'user',
                            a: 'getCode',
                            mobile: phone,
                            captcha: captcha,
                            type: "reg",
                            countrycode: reg,
                            _: datetimestring,
                        }
                    })
                    .then(function (response) {
                        response= response.data;
                        if (response.errno == '0'){
                            alert("成功：" + response.errmsg);
                        }else{
                            alert("err" + response.errmsg);
                            app.get_img();
                            return !1;
                        }
                    })
                        .catch(function (error) {
                        console.log(error);
                        return !1;
                    });
                },
                reg_go(event){
                    phone = this.$refs.phone.value;
                    reg = this.$refs.reg.value;
                    smscaptcha = this.$refs.smscaptcha.value;
                    captcha = this.$refs.captcha.value;
                    pass = this.$refs.pass.value;
                    pass2 = this.$refs.pass.value;
                    if(phone==""){
                        alert("請輸入手機號碼！！");
                    }
                    //alert("Phone:" + phone + "\nreg:" + reg + "\nsmscaptcha:" + smscaptcha + "\ncaptcha:" + captcha + "\npass:" + pass + "\npass2:" + pass2);
                    // registered
                    axios.get('./index.php?g=home&m=user&a=userReg', {
                        params: {
                            mobile: phone,
                            pass: pass,
                            code: captcha,
                            countrycode: reg,
                        }
                    })
                    .then(function (response) {
                        response= response.data;
                        alert("1==" + response.errno );
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

                    // login redirect to index
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
    })
</script>
</body>

</html>