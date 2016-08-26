<!--<header>
    <div class='inner'>
        <a href='#this' class='toback'></a>
        <h1>
            话题详情
        </h1>
        <a href="#this" class='iconfont share h-regiser'>&#xe614;</a>
    </div>
</header>-->
<div class="wraper">
    <div class="h20">
    </div>
    <section>
        <ul class="m-detail">
            <li class='mtitle'>
                <h3><?= $subject->title ?><span class='m-block'><?= $subject->user->truename ?> <?= $subject->user->company ?> <?= $subject->user->position ?></span></h3>
<!--                <span class="meet-type">
                </span>-->
            </li>
            <!--            <li>
                            <span><?= $subject->price ?>元/次</span>
                            <span class="fr">约<?= $subject->last_time ?>小时</span>
                        </li>-->
            <li>
                <h3 class="t-tittle">话题简介</h3>
                <p><pre><?= $subject->summary ?></pre></p>
            </li>
        </ul>
    </section>
    <div class="a-form-box m-form-box">
        <ul>
            <li>
                <i>请简略介绍需求(300字以下)</i>
                <textarea id="summary"></textarea>
                <i class="m-tips"><b class="iconfont"></b>详细的介绍能让会员更加了解你<span>你填的信息只有会员能看到，不会公开给其它人</span></i>
            </li>
        </ul>
    </div>
    <a href="javascript:void(0)" id="submit" class="nextstep" user_id="<?= $user_id ?>">立即预约</a>
</div>
<div class="reg-shadow" ontouchmove="return false;" id="shadow" hidden></div>
<div class="totips" style="height: 3.6rem;" hidden id="checkBtn">
    <h3 id="msg">请先去完善个人资料</h3>
    <span></span>
    <a href="javascript:void(0)" class="tipsbtn" id="no">取消</a><a href="/home/edit_userinfo" class="tipsbtn" id="yes">去完善</a>
</div>

<?php $this->start('script') ?>
<script src="/mobile/js/loopScroll.js"></script>
<script>
    window.subject_id = '<?= $subject->id ?>';
</script>
<script>
//    $('.nextstep').on('tap', function () {
//        if ($(this).attr('user_id') == '') {
//            $.util.alert('请先登录');
//            setTimeout(function () {
//                location.href = '/user/login?redirect_url=/meet/subject-detail/<?= $subject->id ?>';
//            }, 2000);
//        } else {
//            location.href = '/meet/book/<?= $subject->id ?>';
//        }
//    });
    $.util.checkUserinfoStatus();
    
    $('#submit').on('tap', function () {
        if ($(this).attr('user_id') == '') {
            $.util.alert('请先登录');
            setTimeout(function () {
                location.href = '/user/login?redirect_url=/meet/subject-detail/<?= $subject->id ?>';
            }, 2000);
        } else {
            var id = window.subject_id;
            var summary = $('#summary').val();
            if (!summary) {
                $.util.alert('内容不可为空');
                return false;
            } else if (summary.length > 300) {
                $.util.alert('');
                return false;
            }
            $.util.ajax({
                url: '/meet/book/<?= $subject->id ?>',
                data: {id: id, summary: summary},
                func: function (res) {
                    if (res.status) {
                        $.util.alert(res.msg);
                        document.location.href = '/meet/book-success/<?= $subject->user->id ?>';
                    } else {
                        if (res.msg.indexOf('请先去完善个人资料') != -1) {
                            $('#msg').html(res.msg);
                            $('#shadow').show();
                            $('#checkBtn').show();
                        } else {
                            $.util.alert(res.msg);
                        }
                    }
                }
            });
        }
    });
</script>
<?php
$this->end('script');
