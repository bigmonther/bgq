<script src="/wpadmin/js/jquery.js"></script>
<script src="/wpadmin/lib/layer/layer.js"></script>
<script src="/wpadmin/lib/layer/extend/layer.ext.js"></script>
<link href="/wpadmin/lib/zui/css/zui.min.css" rel="stylesheet">
<link href="/wpadmin/lib/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
<link href="/wpadmin/css/base.css" rel="stylesheet">
<div class="work-copy">
    <form action="" method="post" class="form-horizontal">
        <div class="form-group">
            <label class="col-md-2 control-label"></label>
            <div class="col-md-8"></div>
        </div>
        <?php if($content): ?>
        <div class="form-group">
            <label class="col-md-2 control-label">标签</label>
            <div class="col-md-8"><input value="<?= $content->name ?>" class="form-control" readonly></div>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label class="col-md-2 control-label">推送标题</label>
            <div class="col-md-8"><input type="text" name="title" class="form-control"/></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">推送(短信)内容</label>
            <div class="col-md-8"><textarea name="content" class="form-control"></textarea></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">推送链接（可选）</label>
            <div class="col-md-8"><input type="text" name="url" class="form-control"/></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">备注（记录用）</label>
            <div class="col-md-8"><input type="text" name="remark" class="form-control"/></div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label"></label>
            <div class="col-md-8" style="color: red;">推送营销类短信的时候，需要短信内容最后加上“退订回T”字样，如果最后是链接，请注意加上空格分开！</div>
        </div>
        <input type="hidden" name="industry_id" value=""/>
        <input type="hidden" name="push" value=""/>
        <input type="hidden" name="text" value=""/>
        <input type="hidden" name="keyword" value=""/>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <input type='text' id='push' class='btn btn-primary' value='保存' data-loading='稍候...' readonly /> 
            </div>
        </div>
    </form>
</div>
<script src="/wpadmin/js/jquery.js"></script>
<!-- ZUI Javascript组件 -->
<script src="/wpadmin/lib/zui/js/zui.min.plus.js"></script>
<script src="/wpadmin/lib/datetimepicker/jquery.datetimepicker.js"></script>
<script src="/wpadmin/js/global.js"></script>
<script>
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    $('#push').on('click', function () {
        $('input[name="keyword"]').val(parent.$('input[name="keyword"]').val());
        $('input[name="industry_id"]').val(parent.$('#select-industry').val());
        $('input[name="push"]').val(parent.$('input[name="push"]').get(0).checked);
        $('input[name="text"]').val(parent.$('input[name="text"]').get(0).checked);
        if ($('input[name="title"]').val() == '') {
            layer.alert('请输入推送标题');
            return false;
        }
        if ($('input[name="content"]').val() == '') {
            layer.alert('请输入推送内容');
            return false;
        }
        if ($('input[name="remark"]').val() == '') {
            layer.alert('请输入备注内容');
            return false;
        }
        var form = $('form').serialize();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: form,
            url: "/admin/push/do-push",
            success: function (res) {
                parent.layer.alert('保存成功');
                parent.layer.close(index);
            }
        });

    });
</script>