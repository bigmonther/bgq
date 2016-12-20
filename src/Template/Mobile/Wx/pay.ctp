<div class="wraper">
    <div class="train-pay-box">
        <div class="train-account bgff">
            <h3>充值<?= $money ?>元</h3>
        </div>
        <ul class="choose-pay-box outerblock bgff mt20">
            <li class="flex flex_jusitify choosed pay" payMethod="wx">
                <div class="pay-type">
                    <i class="iconfont wxpay">&#xe6b5;</i> 微信支付
                </div>
                <div class="choose-btn">
                    <i class="iconfont"></i>
                </div>
            </li>
            <?php if(!$isWx): ?>
            <li class="flex flex_jusitify pay" payMethod="ali">
                <div class="pay-type">
                    <i class="iconfont alipay">&#xe65a;</i> 支付宝支付
                </div>
                <div class="choose-btn">
                    <i class="iconfont"></i>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<a href="javascript:void(0)" class="f-bottom" id="submit">确认支付</a>
<script>
    var payMethod = 'wx';
    $('.pay').on('click', function () {
        payMethod = $(this).attr('payMethod');
        $(this).addClass('choosed').siblings().removeClass('choosed');
    });
    
    $('#submit').on('tap', function(){
        if (payMethod == 'wx') {
            var wxConfig = '<?= json_encode($jsApiParameters) ?>';
            if ($.util.isAPP) {
                if (wxConfig) {
                    LEMON.pay.wx(<?= json_encode($jsApiParameters) ?>, function (res) {
                        if (res == '0') {
                            $.util.alert('支付成功');
                            setTimeout(function () {
                                window.location.href = '/buy/buy-success/';
                            }, 1000);
                        } else {
                            $.util.alert('支付未成功');
                        }
                    });
                } else {
                    LEMON.event.getWXCode(function (code) {
                        $.util.ajax({//获取open id,比对是否存在,登录或是注册  生成token
                            data: {code: code},
                            url: '/wx/paywx/<?= $order->id ?>',
                            func: function (res) {
                                LEMON.pay.wx(res.jsApiParameters, function (res) {
                                    if (res == '0') {
                                        $.util.alert('支付成功');
                                        setTimeout(function () {
                                            window.location.href = '/wx/buy-success/';
                                        }, 1000);
                                    } else {
                                        $.util.alert('支付未成功');
                                    }
                                });
                            }
                        });
                    });
                }
                return false;
            }
            callpay();
        }
        if (payMethod == 'ali') {
            if ($.util.isAPP) {
                LEMON.pay.ali('<?= $aliPayParameters ?>', function (res) {
                    if (res == '9000') {
                        $.util.alert('支付成功');
                        setTimeout(function () {
                            window.location.href = '/wx/buy-success/';
                        }, 1000);
                    } else {
                        $.util.alert('支付未成功');
                    }
                });
            }
        }
    });
    
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?= json_encode($jsApiParameters) ?>,
                function (res) {
                    alert(res.err_msg);
                    if (res.err_msg == "get_brand_wcpay_request：ok") {
                        $.util.alert('支付成功');
                        setTimeout(function () {
                            window.location.href = '/wx/buy-success/<?= $order->id ?>';
                        }, 1000);
                    }else{
                        $.util.alert('未成功支付');
                    }
//                    $.each(res, function (i, n) {
//                        alert(i + ':' + n);
//                    });
                }
        );
    }

    function callpay()
    {
        console.log('微信支付被唤起');
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }
</script>