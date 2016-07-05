<!--<header>
    <div class='inner'>
        <a href='#this' class='toback'></a>
        <h1>
           我的活动
        </h1>
         <a href="javascript:void(0);" class="h-regiser h-add"></a> 
    </div>
   
</header>-->
<div class="wraper">
<!--    <div class="h-add">
        <img src="/mobile/images/add1.png" alt="" />
    </div>-->
     <div  class="inner my-home-menu" >
        <a href="/home/my-following" class="active">我的关注</a>
        <a href="/home/my-fans" >我的粉丝</a>     
     </div>
            
    <div class="my-focus-box">
        <div class='inner my-search'>
            <a href='javascript:void(0)' class='toback iconfont news-serch'>&#xe613;</a>
            <form id="searchForm" onsubmit="return false;" style="width: 100%" >
            <h1><input type="text" name="keyword" placeholder="请输入关键词" value=""></h1>
            </form>
            <div class='h-regiser' id="doSearch">搜索</div>
        </div>
    </div>
    <div id="follow">
    </div>
</div>

<script type="text/html" id="listTpl">
    <section class="internet-v-info">
        <div class="innercon">
            <a href='/user/home-page/{#following_id#}'><span class="head-img"><img src="{#following_avatar#}"/><i></i></span></a>
            <div class="vipinfo">
                <a href="/user/home-page/{#following_id#}">
                    <h3>{#following_truename#}<span class="meetnum"></span></h3>
                    <span class="job">{#following_company#}&nbsp;&nbsp;{#following_position#}</span>
                </a>
                <div class="mark">
                    {#following_subject#}
                    <span class="meetnum">{#following_fans#}人关注</span>
                </div>
            </div>
        </div>
    </section>	
</script>
<script type="text/html" id="tpl">
    <a href="javascript:void(0)">{#title#}</a>
</script>
<?php $this->start('script') ?>
<script src="/mobile/js/loopScroll.js"></script>
<script>
    if(LEMON.isAPP)
    {
        LEMON.sys.back('/home/index');
    }
    $.util.dataToTpl('follow', 'listTpl',<?= json_encode($followings) ?>, function(d){
        console.log(d.following);
        d.following_id = d.following.id;
        d.following_truename = d.following.truename;
        d.following_company = d.following.company;
        d.following_avatar = d.following.avatar ? d.following.avatar : '/mobile/images/touxiang.png';
        d.following_position = d.following.position;
        d.following_fans = d.following.fans;
        d.following_subject = $.util.dataToTpl('', 'tpl', d.following.subjects);
        return d;
    });
    
    $('#doSearch').on('tap', function(){
        if($('input[name="keyword"]').val() === ''){
            $.util.alert('请输入内容');
            return false;
        }
        $.ajax({
            type: 'POST',
            data: $('form').serialize(),
            dataType: 'json',
            url: "/home/search_following",
            success: function (res) {
                console.log(res);
                if(res.status){
                    $.util.dataToTpl('follow', 'listTpl', res.data, function(d){
                        d.following_id = d.following.id;
                        d.following_truename = d.following.truename;
                        d.following_company = d.following.company;
                        d.following_avatar = d.following.avatar ? d.following.avatar : '/mobile/images/touxiang.png';
                        d.following_position = d.following.position;
                        d.following_fans = d.following.fans;
                        d.following_subject = $.util.dataToTpl('', 'tpl', d.following.subjects);
                        return d;
                    });
                } else {
                    $.util.alert(res.msg);
                }
            }
        });
    });
</script>
<?php $this->end('script'); ?>
