<div class="wraper">
    <div class="h2">
    </div>
    <section>
        <ul class="d-list">
            <?php if($subjects): ?>
                <?php foreach($subjects as $subject): ?>
                    <li>
                        <a class="alink clearfix" href="/meet/subject-detail/<?=$subject->id?>/#list">
                            <h3><?=$subject->title?></h3>
                            <span><?= $user->truename ?> | <?= $user->company ?> | <?= $user->position ?></span>
                            <i class="iconfont">&#xe662</i>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>暂无话题</li>
            <?php endif; ?>
        </ul>
    </section>
</div>
<!--<div class="submitbtn c-width">
    <a href="/meet/subject"><img src="/mobile/images/add-s.png"/></a>
</div>-->

<script>
</script>