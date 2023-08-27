$(function () {

    if ($('.carousel').length > 0) {
        $('.carousel').carousel({
            interval: 5000
        });
    }

    if ($(".btn-experience").length > 0) {
        $(".btn-experience").on("click", function () {
            location.href = "/addons/epay/index/experience?amount=" + $("input[name=amount]").val() + "&type=" + $(this).data("type") + "&method=" + $("#method").val();
        });
    }

    if ($(".qrcode").length > 0) {
        $(".qrcode").qrcode({width: 250, height: 250, text: $(".qrcode").data("text")});
    }

    var si, xhr;
    if (typeof queryParams != 'undefined') {
        var queryResult = function () {
            xhr && xhr.abort();
            xhr = $.ajax({
                url: "",
                type: "post",
                data: queryParams,
                dataType: 'json',
                success: function (ret) {
                    if (ret.code == 1) {
                        var data = ret.data;
                        if (typeof data.status != 'undefined') {
                            var status = data.status;
                            if (status == 'SUCCESS' || status == 'TRADE_SUCCESS') {
                                $(".scanpay-qrcode .paid").removeClass("hidden");
                                $(".scanpay-tips p").html("支付成功！<br><span>3</span>秒后将自动跳转...");

                                var sin = setInterval(function () {
                                    $(".scanpay-tips p span").text(parseInt($(".scanpay-tips p span").text()) - 1);
                                }, 1000);

                                setTimeout(function () {
                                    clearInterval(sin);
                                    location.href = queryParams.returnurl;
                                }, 3000);

                                clearInterval(si);
                            } else if (status == 'REFUND' || status == 'TRADE_CLOSED') {
                                $(".scanpay-tips p").html("请求失败！<br>请返回重新发起支付");
                                clearInterval(si);
                            } else if (status == 'NOTPAY' || status == 'TRADE_NOT_EXIST') {
                            } else if (status == 'CLOSED' || status == 'TRADE_CLOSED') {
                                $(".scanpay-tips p").html("订单已关闭！<br>请返回重新发起支付");
                                clearInterval(si);
                            } else if (status == 'USERPAYING' || status == 'WAIT_BUYER_PAY') {
                            } else if (status == 'PAYERROR') {
                                clearInterval(si);
                            }
                        }
                    }
                }
            });
        };
        si = setInterval(function () {
            queryResult();
        }, 3000);
        queryResult();
    }

});
