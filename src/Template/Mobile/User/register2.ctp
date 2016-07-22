<header>
    <div class='inner'>
        <a href='javascript:history.go(-1);' class='toback'></a>
        <h1>
            注册
        </h1>
    </div>
    <!--<div class="h-home-bottom">
        <div><span><img src="../images/home-pic.png"/></span><i class="iconfont">&#xe61e;</i></div>
        <h3>杨涛<span>IDG资本 董事长</span></h3>
    </div>-->
</header>
<div class="wraper">
    <div class="h2"></div>
    <form>
        <ul class="h-info-box e-info-box">
            <li>
                <a id="uploadPic" href="javascript:void(0);">
                    <span>我的名片：</span>
                    <div  class="upload-user-img">
                        <input  name="card_path" type="hidden" value=""/>
                        <span class="m-card"></span>
                    </div>
                </a>
            </li>
            <li class="no-right-ico">
                <a href="javascript:void(0);">
                    <span>姓名：</span>
                    <div>
                        <input name="truename" type="text"  />
                    </div>
                </a>
            </li>
            <li class="no-right-ico">
                <a href="javascript:void(0);">
                    <span>公司：</span>
                    <div>
                        <input name="company" type="text"  />
                    </div>
                </a>
            </li>
            <li class="no-right-ico">
                <a href="javascript:void(0);">
                    <span>职务：</span>
                    <div >
                        <input name="position" type="text"  />
                    </div>
                </a>
            </li>

            <li class="no-right-ico">
                <a href="javascript:void(0);">
                    <span>邮箱：</span>
                    <div>
                        <input name="email" type="text"  />
                    </div>
                </a>
            </li>
        </ul>
    </form>
    <!--机构类型-->
    <div class="markbox border" id="cart">
        <div class="a-s-title bgff">
            <span class="orgname">请选择机构类型<i class="orgtext"></i></span>
        </div>
        <div class="markslider">
            <div class="mark-items">
                <ul	class="b-mark headmark mt1">
                    <?php foreach ($agencys as $key => $agency): ?>
                        <?php if ($key < 3): ?>
                            <li data-target='car1tuli<?= $agency['id'] ?>' ><a href="#this"><?= $agency['name'] ?></a> <span class="icon-bottom"></span></li>
                        <?php else: ?>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php foreach ($agencys as $key => $agency): ?>
                    <?php if ($key < 3): ?>
                        <ul class="b-mark cart cart1 mt1" data-id='car1tuli<?= $agency['id'] ?>' id='u<?= $agency['id'] ?>'>
                            <?php foreach ($agency['children'] as $item): ?>
                                <li data-val="<?= $item['id'] ?>" ><a href="#this" ><?= $item['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <ul	class="b-mark headmark mt1">
                    <?php foreach ($agencys as $key => $agency): ?>
                        <?php if ($key > 2): ?>
                            <li data-target='car2tuli<?= $agency['id'] ?>' ><a href="#this"><?= $agency['name'] ?></a> <span class="icon-bottom"></span></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php foreach ($agencys as $key => $agency): ?>
                    <?php if ($key > 2): ?>
                        <ul class="b-mark cart cart1 mt1" data-id='car2tuli<?= $agency['id'] ?>' id='u<?= $agency['id'] ?>'>
                            <?php foreach ($agency['children'] as $item): ?>
                                <li data-val="<?= $item['id'] ?>" ><a href="#this" ><?= $item['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="h2">
    </div>
    <!--行业标签-->
    <div class="markbox border" id="classfy">
        <div class="a-s-title bgff">
            <span class="orgname">请选择行业标签<!--<i class="classfytext"></i>--></span>
        </div>
        <div class="markslider">
            <div class="classfytext">

            </div>
            <div class="mark-items">
                <ul	class="b-mark classfymark">
                    <?php foreach ($industries as $industry): ?>
                        <li  data-target='class1tuli<?= $industry['id'] ?>' ><a href="#this"><?= $industry['name'] ?></a> <span class="icon-bottom"></span></li>
                    <?php endforeach; ?>
                </ul>
                <?php foreach ($industries as $industry): ?>
                    <ul class="b-mark cart cart1 mt1" data-id='class1tuli<?= $industry['id'] ?>' id='u1'>
                        <?php foreach ($industry['children'] as $item): ?>
                            <li data-val="<?= $item['id'] ?>" ><a href="#this" ><?= $item['name'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <div style="height:1.6rem">

    </div>
    <a href="javascript:void(0);" id="submit" class="f-bottom">下一步</a>
</div>
<?php $this->start('script') ?>
<script type="text/javascript">

//		$('.a-s-title .orgname').on('tap',function(){
//			
//			$(this).toggleClass('active');
//			var mark = $(this).parents('.markbox').children('.markslider');
//			if(mark.hasClass('smark')){
//				mark.removeClass('smark').addClass('nosmark');
//			}else if(mark.hasClass('nosmark')){
//				mark.removeClass('nosmark').addClass('smark');
//			}else{
//				mark.addClass('smark');
//			}
//		})


    $(function () {
        var agency = null, formdata;
        var classfy = $('.classfymark>li');
        var cart = $('.headmark>li');
        var allUl = $('.markbox .cart');
        //console.log(allUl.length);
        classfy.on('tap', function () {
            var that = $(this);
            fixed(that);
        });
        $('#classfy .cart>li').on('tap', function () {
            var industry_id = $(this).data('val');
            $(this).children('a').addClass('active');
            $('.classfytext').append('<a class="industry_item" data-val="' + industry_id + '" href="javascript:void(0)">' + $(this)[0].innerText + '</a>');
        });
        cart.on('tap', function () {
            var that = $(this);
            fixed(that);
            $('#cart .cart>li').on('tap', function () {
                $(this).children('a').addClass('active')
                $(this).siblings().children('a').removeClass('active');
                $(this).parents('.cart1').hide();
                agency = $(this).data('val');
                $('.orgtext').html($(this)[0].innerText);
                that.children('span').removeClass('active');
            })
        })

        function fixed(that) {
            allUl.hide();
            var $value = that.attr('data-target');
            cart.children('span').removeClass('active');
            classfy.children('span').removeClass('active');
            $('.markbox ul[data-id = ' + $value + ']').show();
            that.children('span').addClass('active');
        }
        $('#submit').click(function () {
            var industry_ids = [];
            var data = $('form').serializeArray()
            formdata = {};
            $('.industry_item').each(function (i, elm) {
                industry_ids.push($(elm).data('val'));
            });
            $.each(data, function (i, elm) {
                if (elm.name) {
                    formdata[elm.name] = elm.value;
                }
            });

            if (industry_ids.length == 0) {
                $.util.alert('还没选择行业标签哦');
                return false;
            }
            if (industry_ids.length > 4) {
                $.util.alert('行业标签最多只能选择4个');
                return false;
            }
            if (!agency) {
                $.util.alert('还没选择机构哦');
            }
            formdata['industries[_ids]'] = industry_ids;
            //对象长度判断
            $.post('/user/register', formdata, function (res) {
                if (res.status === true) {
                    if ($.util.isAPP) {
                        $.util.setCookie('token_uin', res.token_uin, 10 * 365 * 24 * 60);
                        LEMON.db.set('token_uin', res.token_uin);
                    }
                    setTimeout(function () {
                        $.util.alert(res.msg);
                    }, '1000')
                    window.location.href = res.url;
                } else {
                    $.util.alert(res.msg);
                }
            }, 'json');
        });
        $('#uploadPic').on('touchstart', function () {
            if ($.util.isAPP) {
                LEMON.event.uploadPhoto('{"dir":"user/mp"}', function (data) {
                    var data = JSON.parse(data);
//                    $('#img').attr('src', data.path);
                    if (data.status === true) {
//                        $('#img').attr('src', data.path);
                        $('input[name="card_path"]').val(data.path);
                    } else {
                        $.util.alert('app上传失败');
                    }
                });
                return false;
            } else if ($.util.isWX) {
                $.util.wxUploadPic(function (id) {
                    $.util.ajax({
                        url: "/wx/wxUploadPic/" + id + '?dir=user/mp',
                        func: function (res) {
                            if (res.status === true) {
                                $('input[name="card_path"]').val(res.path);
                            }
                        }
                    });
                });
            } else {
                $.util.alert('请在微信或APP上传图片');
            }
        });
    });
</script>
<?php
$this->end('script')?>