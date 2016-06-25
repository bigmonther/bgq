<header>
    <div class='inner'>
        <h1>
            专家约见
        </h1>
        <a href="#this" class='iconfont news-serch h-regiser'>&#xe613;</a>
    </div>
</header>
<link rel="stylesheet" type="text/css" href="/mobile/font/font/iconfont.css">
<div class="wraper newswraper">
    <div class="a-banner" >
        <ul class="pic-list-container" id="imgList">
            <?php foreach ($banners as $k=>$v): ?>
            <li><a href="<?= $v['url'] ?>"><img src="<?= $v['img'] ?>"/></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="yd" id="imgTab">
            <?php foreach ($banners as $v): ?>
            <span></span>
            <?php endforeach; ?>
        </div>
        <div class="a-search-box" id="search">
            <div class="a-search">
               <a href="#this"> <i class="iconfont">&#xe613;</i></a>
                <div class="s-con">
                    <input type="text" placeholder="请输入关键词" class='search'/>
                </div>
            </div>
        </div>
    </div>
    <!--分类--start-->
    <div class="menusort clearfix">
        <div class="allmenu">
            <div class="menulist clearfix" id="allsort">
                <a href="/meet/industries" id='sort_1' sort='1'>
                    <i class="iconfont">&#xe63f;</i>
                    <span>互联网</span>
                </a>
                <a href="/meet/industries" id='sort_2' sort='2'>
                    <i class="iconfont">&#xe642;</i>
                    <span>金融</span>
                </a>
                <a href="/meet/industries" id='sort_3' sort='3'>
                    <i class="iconfont">&#xe640;</i>
                    <span>健康医疗</span>
                </a>
                <a href="/meet/industries" id='sort_4' sort='4'>
                    <i class="iconfont">&#xe643;</i>
                    <span>艺术</span>
                </a>
                <a href="/meet/industries" id='sort_5' sort='5'>
                    <i class="iconfont">&#xe644;</i>
                    <span>餐饮</span>
                </a>
                <a href="/meet/industries" id='sort_6' sort='6'>
                    <i class="iconfont">&#xe645;</i>
                    <span>养生</span>
                </a>
                <a href="/meet/industries" id='sort_7' sort='7'>
                    <i class="iconfont">&#xe646;</i>
                    <span>保险</span>
                </a>
                <a href="/meet/industries" id='sort_8' sort='8'>
                    <i class="iconfont">&#xe647;</i>
                    <span>汽车</span>
                </a>
                <a href="/meet/moreIndustries">
                    <i class="iconfont">&#xe648;</i>
                    <span>更多</span>
                </a>
            </div>
        </div>
        <a href="javascript:void(0);" class="sele-r"></a>
    </div>
    <!--分类--end-->
    
    <div class="dk">
        <ul>
            <?php foreach($biggieAd as $k=>$v): ?>
            <li><a href="/meet/view/<?= $v['savant']['user_id'] ?>"><img src="<?= $v['url'] ?>"/><span><?= $v['savant']['meet_nums'] ? $v['savant']['meet_nums'] : 0; ?>人见过</span></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div id='biggie'></div>
</div>
<script type='text/html' id='biggie_tpl'>
    <section class="internet-v-info">
        <div class="innercon">
            <a href="/meet/view/{#id#}"><span class="head-img"><img src="{#avatar#}"/><i></i></span></a>
            <div class="vipinfo">
                <a href="/meet/view/{#id#}">
                    <h3>{#truename#}<span class="meetnum">{#meet_nums#}人见过</span></h3>
                    <span class="job">{#company#}&nbsp;&nbsp;{#position#}</span>
                </a>
                <div class="mark">
                    {#subjects#}
                </div>
            </div>
        </div>
    </section>
</script>
<script type='text/html' id='subTpl'>
    <a href="/meet/subject_detail/{#id#}">{#title#}</a>
</script>
<?=$this->element('footer');?>
<?php $this->start('script'); ?>
<script src="/mobile/js/loopScroll.js"></script>
<script src="/mobile/js/meet_index.js"></script>
<link rel="stylesheet" href="/mobile/font/font/iconfont.css" />
<script>
    $.util.dataToTpl('biggie', 'biggie_tpl',<?= $meetjson ?>, function (d) {
        d.avatar = d.avatar ? d.avatar : '/mobile/images/touxiang.jpg';
        d.subjects = $.util.dataToTpl('', 'subTpl', d.subjects);
        return d;
    });
    
    //轮播
    var loop = $.util.loopImg($('#imgList'), $('#imgList li'), $('#imgTab span'));
    $('.s-con').click(function () {
        $('.search').focus();
    });

    $('.search').focus(function () {
        location.href = "/meet/search";
    });
    
    $.util.searchHide();
    
    if($.util.isAPP)
    {
        $('#search').hide();
        LEMON.show.search('/meet/search');
    }
    
</script>
<?php $this->end('script');