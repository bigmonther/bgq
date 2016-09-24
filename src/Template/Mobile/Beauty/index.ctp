<link rel="stylesheet" type="text/css" href="/mobile/css/zt.css"/>
<div class="wraper">
    <!--banner-->
    <div class="a_search_box">
        <a href="/beauty/search"><i class="iconfont">&#xe618;</i></a>
    </div>
    <div class="a-banner" >
        <ul class="pic-list-container" id="imgList">
                <!--<li><a href="#this"><img src="/mobile/images/a-banner.png"/></a></li>-->
            <li><a href="#this"><img src="/mobile/images/zhti.png"/></a></li>
            <!--<li><a href="#this"><img src="/mobile/images/a-banner.png"/></a></li>-->
        </ul>
        <div class="yd" id="imgTab">
            <span class="cur"></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <!--banner__end-->
    <!--活动介绍-->
    <div class="z_top_intro">
        <div class='z_top_title innercon clearfix'>
            <h3 class="fl color-items">活动介绍</h3>
            <span class="fr col-lblue rule">活动规则</span>
        </div>
        <div class='z_top_con p20 bd2 bd1'>
            <div class="content_inner">
                <p>选美大赛是由美女和媒体联合，帮助美女提升知名度，带动美丽产业发展的一种娱乐活动。选美自古就有，广泛存在于宫庭之中。选美大赛是由美女和媒体联合参与，帮助美女提升知名度，带动美丽产业发展的一种娱乐活动。
                </p></div>
        </div>
    </div>
    <!--活动介绍 end-->
    <!--top 10-->
    <div class="z_top10 z_top_title bd1">
        <h3 class="tc">Top 10</h3>
    </div>
    <!--top 10-end->
    <!---->
    <section class="z_items content_inner" id="beauty">
        
    </section>
    <div style='height:1.2rem;'></div>
    <div class="fixed-btn">
        <a href="/beauty/want-vote" class="l-btn">我要投票</a>
        <?php if($user): ?>
            <a href="javascript:$.util.checkLogin('/beauty/userinfo');" class="r-btn" >我的报名</a>
        <?php else: ?>
            <a href="javascript:$.util.checkLogin('/beauty/enroll');" class="r-btn" >我要报名</a>
        <?php endif; ?>
    </div>
    <div class="zt_tips">
        <span class="zt_r_closed">&times;</span>
        <div class="zt_tips_box">
            <h3 class="color-items tc">规则说明</h3>
            <p class="jusitify"><strong>1.投票说明</strong><br />
                •只有已注册并购帮的用户才能成为参赛选手。<br />
                •只有登录并购帮的用户才能投票。<br /> 
                •投票要进行限制，一个用户24小时内只能对一个
                选手投一票，但可以投票给多个选手，不做限制。<br />
                •评审专家只有30票（男女各15票）的额度，每人
                限投一票。</p>
            <br />
            <p class="jusitify"><strong>2.评选方式</strong><br />
                •线上投票。可以是并购帮独家投票，也可以联合
                第三方。海选评审团投票。<br />
                •最终评选结果由组委会根据评审团、网络票选综
                合决定。通过线上投票(60%)+评审团投票(40%)的
                方式评出获奖名单，选择得分排名前十的选手。
            </p>
            <br />
            <p>
                <strong>3.比赛流程</strong><br />
                启动项目，收集候选人名单，邀请评审团<br />
                进入网络投票阶段<br />
            </p>
        </div>
    </div>
</div>
<script type="text/html" id="tpl">
    <dl>
        <a href="/beauty/homepage/{#id#}">
            <dt class="posi_top"><img src="{#user_avatar#}" alt="" /><span></span><i>{#beauty_id#}</i></dt>
            <dd class="zt_name"><span class="p_name color-items mr10">{#username#}</span><span class="p_job color-gray">{#position#}</span></dd>
            <dd class="zt_commpany">{#company#}</dd>
        </a>
        <dd class="mt20"><span class="zt_num color-items fl">{#vote_nums#}票</span><span class="fr zt_r_btn vote" user_id="{#user_id#}">投 票</span></dd>
    </dl>
</script>
<?php $this->start('script'); ?>
<script type="text/javascript">
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "/beauty/get-top-user",
        success: function (res) {
            if(res.status){
                $.util.dataToTpl('beauty', 'tpl', res.data, function(d){
                    d.position = d.user.position;
                    d.company = d.user.company;
                    d.username = d.user.truename;
                    d.user_id = d.user.id;
                    d.user_avatar = d.user.avatar ? d.user.avatar : '/mobile/images/touxiang.png';
                    return d;
                });
                $('.vote').on('tap', function(){
                    var obj = $(this);
                    $.util.ajax({
                        url: '/beauty/vote/'+obj.attr('user_id'),
                        func: function(res){
                            if(res.status){
                                obj.prev('span.zt_num').html(parseInt(obj.prev('span.zt_num').html())+1+'票');
                            } else {
                                $.util.alert(res.msg);
                            }
                        }
                    });
                });
            }
        }
    });
    
    
    
    $('.rule').on('tap', function () {
        $('.zt_tips').addClass('zt_tips_show');
        $('body').css({'overflow': 'hidden', 'height': '100%'});
        $('html').css({'overflow': 'hidden', 'height': '100%'});
    });
    $('.zt_r_closed').on('tap', function () {
        $('.zt_tips').removeClass('zt_tips_show');
        $('body').css({'overflow': 'auto', 'height': 'auto'})
        $('html').css({'overflow': 'auto', 'height': 'auto'})
    });

//    $('#enroll').on('tap', function () {
//        $.util.checkLogin('/beauty/enroll');
//    });

    $.util.searchHide();
</script>
<?php
$this->end('script');