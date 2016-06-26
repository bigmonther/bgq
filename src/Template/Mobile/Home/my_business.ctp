<header>
    <div class='inner'>
        <a href='#this' class='toback'></a>
        <h1>
            擅长业务
        </h1>
    </div>
</header>
<div class="wraper">
    <div class="my-good-b">
        <textarea id="goodat" name="goodat"><?=$user->goodat?></textarea>
    </div>
    <a href="#this" id="submit" class="nextstep">保存</a>
</div>

<?php $this->start('script') ?>
<script>
    $('#submit').on('tap',function(){
        var goodat = $('#goodat').val();
        if(!goodat){
            $.util.alert('输入不可为空');
            return false;
        }
        $.util.ajax({
            data:{'goodat':goodat},
            func:function(res){
                $.util.alert(res.msg);
                if(res.status){
                    setTimeout(function(){
                        window.location.href = '/home/edit-userinfo';
                    },1500);
                }
            }
        });
    });
</script>
<?php $this->end('script'); ?>