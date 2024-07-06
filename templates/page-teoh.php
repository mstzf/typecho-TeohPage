<?php
// 获取插件配置
$options = Typecho_Widget::widget('Widget_Options')->plugin('TeohPage');
$html = $options->html;
$css = $options->css;
$cssCode = $options->cssCode;
$js = $options->js;
$jsCode = $options->jsCode;
$showHeader = $options->showHeader;
$showSidebar = $options->showSidebar;
$showFooter = $options->showFooter;
$showComments = $options->showComments;
$showContent = $options->showContent;
?>
<?php if (!defined('__TYPECHO_ROOT_DIR__'))
    exit; ?>
<?php if ($showHeader): ?>
    <?php $this->need('component/header.php'); ?>
<?php endif; ?>
<?php if ($showSidebar): ?>
    <!-- aside -->
    <?php $this->need('component/aside.php'); ?>
    <!-- / aside -->
<?php endif; ?>

<!-- 插入自定义 CSS 链接 -->
<?php if ($css): ?>
    <?php $cssLinks = explode("\n", $css); ?>
    <?php foreach ($cssLinks as $cssLink): ?>
        <link rel="stylesheet" href="<?php echo trim($cssLink); ?>">
    <?php endforeach; ?>
<?php endif; ?>


<!-- 插入自定义 CSS 代码 -->

<style>
    <?php if (!$showFooter): ?>
        .app-footer {
            display: none;
        }

    <?php endif; ?>
    <?php if ($cssCode): ?>
        <?php echo $cssCode; ?>
    <?php endif; ?>
</style>


<a class="off-screen-toggle hide"></a>
<main class="app-content-body <?php echo Content::returnPageAnimateClass($this); ?>">
    <div class="hbox hbox-auto-xs hbox-auto-sm">
        <!--文章-->
        <div class="col center-part gpu-speed" id="post-panel">
            <!--标题下的一排功能信息图标：作者/时间/浏览次数/评论数/分类-->
            <?php echo Content::exportPostPageHeader($this, $this->user->hasLogin(), true); ?>
            <div class="wrapper-md">
                <?php Content::BreadcrumbNavigation($this, $this->options->rootUrl); ?>
                <!--博客文章样式 begin with .blog-post-->
                <div id="postpage" class="blog-post">
                    <!-- 编辑的独立页面内容 -->
                    <?php if ($showContent): ?>
                        <article class="single-post panel">
                            <!--文章页面的头图-->
                            <?php echo Content::exportHeaderImg($this); ?>
                            <!--文章内容-->
                            <div class="wrapper-lg" id="post-content">
                                <div class="post-content" id="post-content" style="display: flow-root">
                                    <?php Typecho_Plugin::factory('page-teoh.php')->navBar(); ?>
                                </div>
                                <?php Content::postContentHtml(
                                    $this,
                                    $this->user->hasLogin()
                                ); ?>
                                <?php Content::pageFooter($this->options, $this) ?>
                            </div>
                        </article>
                    <?php endif; ?>
                    <!-- 插入自定义 HTML -->
                    <div class="custom-page-content">
                        <?php echo $html; ?>
                    </div>
                </div>
                <?php if ($showComments): ?>
                    <?php $this->need('component/comments.php') ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($showSidebar): ?>
            <!--文章右侧边栏开始-->
            <?php $this->need('component/sidebar.php'); ?>
            <!--文章右侧边栏结束-->
        <?php endif; ?>
    </div>
</main>


<?php $this->need('component/footer.php'); ?>

<!-- 插入自定义 JavaScript 链接 -->
<?php if ($js): ?>
    <?php $jsLinks = explode("\n", $js); ?>
    <?php foreach ($jsLinks as $jsLink): ?>
        <script src="<?php echo trim($jsLink); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- 插入自定义 JavaScript 代码 -->
<?php if ($jsCode): ?>
    <script>
        <?php echo $jsCode; ?>
    </script>
<?php endif; ?>