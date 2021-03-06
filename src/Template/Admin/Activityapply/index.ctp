<?php $this->start('static') ?>   
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.jqgrid.css">
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.ace.css">
<?php $this->end() ?> 
<div class="col-xs-12">
    <form id="table-bar-form">
        <div class="table-bar form-inline">
            <a href="/admin/activityapply/add/<?= $activity_id ?>" class="btn btn-small btn-warning">
                <i class="icon icon-plus-sign"></i>添加
            </a>
            <div class="form-group">
                <label for="keywords">关键字</label>
                <input type="text" name="keywords" class="form-control" id="keywords" placeholder="主要报名人">
            </div>
            <div class="form-group">
                <label for="must_check">是否需要审核</label>
                <select name="must_check" class="form-control">
                    <option value="">全部</option>
                    <option value="1"<?php if (isset($do)): ?>selected="selected"<?php endif; ?>>需要</option>
                    <option value="0">不需要</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">是否审核</label>
                <select name="is_check" class="form-control">
                    <option value="">全部</option>
                    <option value="1">已审核</option>
                    <option value="0" <?php if (isset($do)): ?>selected="selected"<?php endif; ?>>未审核</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">是否付款</label>
                <select name="is_pay" class="form-control">
                    <option value="">全部</option>
                    <option value="1">已付款</option>
                    <option value="0">未付款</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">签到</label>
                <select name="is_sign" class="form-control">
                    <option value="-1">全部</option>
                    <option value="0">未签到</option>
                    <option value="1">已签到</option>
                </select>
            </div>
            <div class="form-group">
                <label for="keywords">时间</label>
                <input type="text" name="begin_time" class="form-control date_timepicker_start" id="keywords" placeholder="开始时间">
                <label for="keywords">到</label>
                <input type="text" name="end_time" class="form-control date_timepicker_end" id="keywords" placeholder="结束时间">
            </div>
            <div class="form-group">
                <label for="push">推送+站内信</label>
                <input type="checkbox" name="push" class="form-control" id="push" checked />
                <label for="text">短信</label>
                <input type="checkbox" name="text" class="form-control" id="text" />
                <label for="is_choose">反选</label>
                <input type="checkbox" name="is_choose" class="form-control" id="is_choose" />
            </div>
            <a onclick="doSearch();" class="btn btn-info"><i class="icon icon-search"></i>搜索/预览</a>
            <a onclick="doExport();" class="btn btn-info"><i class="icon icon-file-excel"></i> 导出</a>
            <a onclick="doPush();" class="btn btn-warning"><i class="icon icon-android"></i> 推送内容</a>
        </div>
    </form>
    <?php if (!isset($do)): ?>
        <div>
            <button type="button" class="btn btn-primary">报名数<span class="label label-badge"><?= $apply_nums ?></span></button>
            <button type="button" class="btn btn-warning">审核通过数<span class="label label-badge"><?= $check_nums ?></span></button>
            <button type="button" class="btn btn-danger">付款数<span class="label label-badge"><?= $pay_nums ?></span></button>
        </div>
    <?php endif; ?>
    <table id="list"><tr><td></td></tr></table> 
    <div id="pager"></div> 
</div>
<?php $this->start('script'); ?>
<script src="/wpadmin/lib/jqgrid/js/jquery.jqGrid.min.js"></script>
<script src="/wpadmin/lib/jqgrid/js/i18n/grid.locale-cn.js"></script>
<script>
    window.select = [];
                $(function () {
                    $('#main-content').bind('resize', function () {
                        $("#list").setGridWidth($('#main-content').width() - 40);
                    });
                    $(document).keypress(function (e) {
                        if (e.which == 13) {
                            doSearch();
                        }
                    });
                    $.zui.store.pageClear(); //刷新页面缓存清除
                    $("#list").jqGrid({
                        url: "/admin/activityapply/getDataList/<?= $id ?><?php if (isset($do)): ?>?do=check<?php endif; ?>",
                        datatype: "json",
                        mtype: "POST",
                        colNames:
                                ['用户', '公司', '职位', '报名活动', '多人同行', '提交时间', '注册时间', '是否需审核', '审核状态', '审核不通过理由', '报名状态', '付款', '操作人', '是否置顶', '是否签到', '操作'],
                        colModel: [
                            {name: 'name', editable: true, align: 'center', formatter: function(cell, opt, row){
                                return cell ? cell : row.user.truename;
                            }},
                            {name: 'company', editable: true, align: 'center', formatter: function(cell, opt, row){
                                return cell ? cell : row.user.company;
                            }},
                            {name: 'position', editable: true, align: 'center', formatter: function(cell, opt, row){
                                return cell ? cell : row.user.position;
                            }},
                            {name: 'activity.title', editable: true, align: 'center'},
                            {name: 'Companions', editable: true, align: 'center', formatter: function(cell, opt, row){
                                    return row.companions.length ? '<a href="javascript:void(0)" onclick="showCompanions('+row.id+')">是，点击查看详情</a>' : '否';
                            }},
                            {name: 'create_time', editable: true, align: 'center'},
                            {name: 'user.create_time', editable: true, align: 'center', formatter: function(cell, opt, row){
                                if(row.other_user){
                                    return row.other_user.create_time;
                                } else if(row.user) {
                                    return cell;
                                } else {
                                    return '';
                                }
                            }},
                            {name: 'activity.must_check', editable: true, align: 'center', formatter: function (cellvalue, options, rowObject) {
                                    if (cellvalue == '1') {
                                        return '是';
                                    } else {
                                        return '否';
                                    }
                                }},
                            {name: 'is_check', editable: true, align: 'center', formatter: function (cellvalue, options, rowObject) {
                                    if (rowObject.activity.must_check == '1') {
                                        switch (cellvalue) {
                                            case 0:
                                                return '未审核';
                                            case 1:
                                                return '审核通过';
                                            case 2:
                                                return '审核不通过';
                                        }
                                    } else {
                                        return '无需审核';
                                    }
                                }},
                            {name: 'reason', editable: true, align: 'center'},
                            {name: 'is_pass', editable: true, align: 'center', formatter: function (cellvalue, options, rowObject) {
                                    switch (cellvalue) {
                                        case 1:
                                            return '<button onClick="pass(' + rowObject.id + ')" class="btn btn-mini"><i class="icon icon-check-circle"></i> 通过</button>';
                                        case 0:
                                            return '<button onClick="pass(' + rowObject.id + ')" class="btn btn-mini"><i class="icon icon-remove-circle"></i><i style="color:red"> 未通过</i></button>';
                                    }
                                }},
                            {name: 'activity.apply_fee', editable: true, align: 'center', formatter: function (cellvalue, options, rowObject) {
                                    if (rowObject.is_pass && cellvalue > 0) {
                                        return '已付款';
                                    } else {
                                        return '未付款';
                                    }
                                }},
                            {name: 'check_man', editable: true, align: 'center'},
                            {name: 'is_top', editable: true, align: 'center', formatter: topFormatter},
                            {name: 'is_sign', editable: true, align: 'center', formatter: signFormatter},
                            {name: 'actionBtn', align: 'center', viewable: false, sortable: false, formatter: actionFormatter}],
//                        pager: "#pager",
                        rowNum: 1000,
                        //rowList: [10,1000],
                        sortname: "id",
                        sortorder: "desc",
//                        reccount:1000,
                        viewrecords: true,
                        gridview: true,
                        autoencode: true,
                        caption: '',
                        autowidth: true,
                        height: 'auto',
                        rownumbers: true,
                        multiselect: true, // 多选支持
                        fixed: true,
                        jsonReader: {
                            root: "rows",
                            page: "page",
                            total: "total",
                            records: "records",
                            repeatitems: false,
                            id: "id"
                        },
                    }).navGrid('#pager', {edit: false, add: false, del: false, view: true});
                });

                function passFormatter(cellvalue, options, rowObject) {
                    if (rowObject.is_pass == 0)
                    {
                        response = '未通过审核';
                    } else if (rowObject.is_pass == 1)
                    {
                        response = '已通过审核';
                    }
                    return response;
                }

                function topFormatter(cellvalue, options, rowObject) {
                    if (rowObject.is_top == 0)
                    {
                        response = '否';
                    } else if (rowObject.is_top == 1)
                    {
                        response = '是';
                    }
                    return response;
                }

                function signFormatter(cellvalue, options, rowObject) {
                    if (rowObject.is_sign == 0)
                    {
                        response = '否';
                    } else if (rowObject.is_sign == 1)
                    {
                        response = '是';
                    }
                    return response;
                }

                function actionFormatter(cellvalue, options, rowObject) {
                    response = ''; // '<a title="删除" href="javascript:void(0)" onClick="delRecord(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-trash"></i> </a>';
//                    if (rowObject.is_top == 0) {
//                        response += '<a title="置顶" href="javascript:void(0)" onClick="topit(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn ">置顶</a>';
//                    } else {
//                        response += '<a title="取消置顶" href="javascript:void(0)" onClick="untop(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn ">取消置顶</a>';
//                    }
                    if (rowObject.activity.must_check == 1 && rowObject.is_check == 0 && rowObject.is_pass != 1) {
                        response += '<a title="审核通过" onClick="check(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-check"></i> </a>';
                        response += '<a title="审核不通过" onClick="uncheck(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-remove-circle"></i> </a>';
                    }else if (rowObject.is_pass == 1 && rowObject.activity.must_check == 1){
                        response += '<a title="改为未通过" onClick="resue(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-undo"></i> </a>';
                    }
                    return response;
                }

                function delRecord(id) {
                    layer.confirm('确定删除？', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: {id: id},
                            dataType: 'json',
                            url: '/admin/activityapply/delete',
                            success: function (res) {
                                layer.msg(res.msg);
                                if (res.status) {
                                    $('#list').trigger('reloadGrid');
                                }
                            }
                        })
                    }, function () {
                    });
                }

                function topit(id) {
                    layer.confirm('确定置顶？', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: '',
                            dataType: 'json',
                            url: '/admin/activityapply/top/' + id,
                            success: function (res) {
                                if (res.status) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        $('#list').trigger('reloadGrid');
                                    }, 2000);
                                }
                            }
                        })
                    }, function () {
                    });
                }

                function untop(id) {
                    layer.confirm('确定取消置顶？', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: '',
                            dataType: 'json',
                            url: '/admin/activityapply/untop/' + id,
                            success: function (res) {
                                if (res.status) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        $('#list').trigger('reloadGrid');
                                    }, 2000);
                                }
                            }
                        })
                    }, function () {
                    });
                }

                function check(id) {
                    layer.confirm('确定通过审核？', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: '',
                            dataType: 'json',
                            url: '/admin/activityapply/check/' + id,
                            success: function (res) {
                                if (res.status) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        $('#list').trigger('reloadGrid');
                                    }, 2000);
                                }
                            }
                        });
                    }, function () {
                    });
                }
                function resue(id) {
                    layer.confirm('确定撤销？（已发送的消息和短信无效）', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: '',
                            dataType: 'json',
                            url: '/admin/activityapply/resue/' + id,
                            success: function (res) {
                                if (res.status) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        $('#list').trigger('reloadGrid');
                                    }, 2000);
                                }
                            }
                        });
                    }, function () {
                    });
                }
                
                function uncheck(id) {
                    //需要引入layer.ext.js文件
                    layer.prompt({
                        title: '请输入理由（只有主要报名人才会收到）',
                        btn: ['确认', '取消'], //按钮
                        formType: 0, // input.type 0:text,1:password,2:textarea
                    }, function (pass) {
                        var msg = {};
                        msg.reason = pass;
                        $.ajax({
                            type: 'post',
                            data: msg,
                            dataType: 'json',
                            url: '/admin/activityapply/uncheck/' + id,
                            success: function (res) {
                                layer.msg(res.msg);
                                if (res.status) {
                                    $('#list').trigger('reloadGrid');
                                }
                            }
                        });
                    }, function () {
                    });
                }

                function doSearch() {
                    //搜索
                    var postData = $('#table-bar-form').serializeArray();
                    var data = {};
                    $.each(postData, function (i, n) {
                        data[n.name] = n.value;
                    });
                    $.zui.store.pageSet('searchData', data); //本地存储查询参数 供导出操作等调用
                    $("#list").jqGrid('setGridParam', {
                        postData: data
                    }).trigger("reloadGrid");
                }

                function doExport() {
                    layer.msg('正在加班加点修改……');return;
                    //导出excel
                    var sortColumnName = $("#list").jqGrid('getGridParam', 'sortname');
                    var sortOrder = $("#list").jqGrid('getGridParam', 'sortorder');
                    var searchData = $.zui.store.pageGet('searchData') ? $.zui.store.pageGet('searchData') : {};
                    searchData['sidx'] = sortColumnName;
                    searchData['sort'] = sortOrder;
                    var searchQueryStr = $.param(searchData);
                    $("body").append("<iframe src='/admin/activityapply/exportExcel/<?= $id ?>?" + searchQueryStr + "' style='display: none;' ></iframe>");
                }

                function showCompanions(id) {
                    //查看明细
                    url = '/admin/activityapply/view/' + id;
                    layer.open({
                        type: 2,
                        title: '同行人',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['50%', '70%'],
                        skin: 'layui-layer-lan', //没有背景色
                        content: url
                    });
                }
                function doPush() {
                    layer.msg('正在加班加点修改……');return;
                    window.select = $("#list").jqGrid('getGridParam', 'selarrrow');
                    if(window.select.length == 0){
                        layer.alert('请选择至少一个对象');
                        return false;
                    }
                    var searchData = $.zui.store.pageGet('searchData') ? $.zui.store.pageGet('searchData') : {};
                    var searchQueryStr = $.param(searchData);
                    url = '/admin/activityapply/push/<?= $id ?>?' + searchQueryStr;
                    layer.open({
                        type: 2,
                        title: '查看详情',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['70%', '50%'],
                        content: url//iframe的url
                    });
                }
                
                function pass(id) {
                    $.ajax({
                        type: 'post',
                        data: {id: id},
                        dataType: 'json',
                        url: '/admin/activityapply/pass/'+id,
                        success: function (res) {
                            layer.msg(res.msg);
                            if (res.status) {
                                $('#list').trigger('reloadGrid');
                            }
                        }
                    });
                }
                
                // 反选操作
                $('#is_choose').click(function(){
                    var allRowIds = $('#list').jqGrid('getDataIDs');
                    for(var i=0;i<allRowIds.length;i++){
                        $('#list').jqGrid('setSelection', allRowIds[i]);
                    }
                });
</script>
<?php $this->end();
