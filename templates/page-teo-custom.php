<?php
/**
 * Teo自定义
 *
 * @package TeoLeaveWord
 */

/**
 * 
 * 仅仅做了与handsome主题的兼容
 */
?>
<?php
    // 获取插件配置
    $html = "";
    $css = "";
    $cssCode = "";
    $js = "";
    $jsCode = "";
    $showHeader = 1;
    $showSidebar = 1;
    $showFooter = 1;
    $showComments = 1;
    $showContent = 1;


    if($this->fields->jsCode!=null) {
        $jsCode = $this->fields->jsCode;
    }

    if($this->fields->cssLin!=null) {
        $css = $this->fields->cssLink;
    }
    if($this->fields->cssCode!=null) {
        $cssCode = $this->fields->cssCode;
    }
    if($this->fields->jsLink!=null) {
        $js = $this->fields->jsLink;
    }
    if($this->fields->show_sidebar != null) {
        $showSidebar = $this->fields->show_sidebar;
    }
    if ($this->fields->show_footer!=null) {
        $showFooter = $this->fields->show_footer ==='1';
    }
    if($this->fields->show_comments!=null) {
        $showComments = $this->fields->show_comments;
    }
    if($this->fields->show_content!=null) {
        $showContent = $this->fields->show_content;
    }
    if($this->fields->custom_html!=null) {
        $html = $this->fields->custom_html;
    }
    $data = [
        'today_unique_visitors' => 0,
        'today_views' => 0,
        'yesterday_unique_visitors' => 0,
        'yesterday_views' => 0,
        'month_total_views' => 0,
        'total_total_views' => 0
    ];
?>

<?php if (!defined('__TYPECHO_ROOT_DIR__'))
    exit; ?>
<?php if ($showHeader): ?>
    <?php $this->need('component/header.php'); ?>
<?php endif; ?>
<!-- aside -->
<?php $this->need('component/aside.php'); ?>
<!-- / aside -->

<!-- 插入自定义 CSS 链接 -->
<?php if ($css): ?>
    <?php $cssLinks = explode("\n", $css); ?>
    <?php foreach ($cssLinks as $cssLink): ?>
        <link rel="stylesheet" href="<?php echo trim($cssLink); ?>">
    <?php endforeach; ?>
<?php endif; ?>

<!-- 插入自定义 CSS 代码 -->

<style>
    <?php if (!$showSidebar): ?>
        /* 隐藏侧边栏 */
        .app-aside {
            display: none !important;
        }

        .asideBar {
            display: none !important;
        }

        /* 修改页面左边距 */
        .app-content,
        .app-footer {
            margin-left: 0 !important;
        }

    <?php endif; ?>
    /* 隐藏页脚 */
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
                <!-- <?php Content::BreadcrumbNavigation($this, $this->options->rootUrl); ?> -->
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
                                    <?php Typecho_Plugin::factory('page-custom-teoh.php')->navBar(); ?>
                                </div>
                                <?php Content::postContentHtml(
                                    $this,
                                    $this->user->hasLogin()
                                ); ?>
                                <!-- 插入自定义 HTML -->
                                <div class="custom-page-content">
                                    <?php echo $html; ?>
                                </div>
                                <?php if ($showComments): ?>
                                    <?php $this->need('component/comments.php') ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <!--文章右侧边栏开始-->
        <?php $this->need('component/sidebar.php'); ?>
        <!--文章右侧边栏结束-->
    </div>
</main>


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

<?php $this->need('component/footer.php'); ?>
