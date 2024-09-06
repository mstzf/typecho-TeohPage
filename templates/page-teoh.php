<?php
/**
 * Teo插件
 *
 * @package TeoPlugins
 */

/**
 * 组件样式来自插件设置中获取的 html、css、js
 * 仅仅做了与handsome主题的兼容
 */
?>
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

$data = [
    'today_unique_visitors' => 0,
    'today_views' => 0,
    'yesterday_unique_visitors' => 0,
    'yesterday_views' => 0,
    'month_total_views' => 0,
    'total_total_views' => 0
];

// 检查插件是否启用，并更新数据
if (TeohVisit_Plugin::isPluginEnabled()) {
    $stats = TeohVisit_Plugin::getAllStats();
    $data = [
        'today_unique_visitors' => $stats['today']['unique_visitors'],
        'today_views' => $stats['today']['views'],
        'yesterday_unique_visitors' => $stats['yesterday']['unique_visitors'],
        'yesterday_views' => $stats['yesterday']['views'],
        'month_total_views' => $stats['month']['total_views'],
        'total_total_views' => $stats['total']['total_views']
    ];
}
// 输出 PHP 数组数据为 JSON 格式，以便在 JavaScript 中使用
$jsonData = json_encode($data);
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
<link rel="stylesheet" href="/usr/plugins/TeohPage/assets/css/about.css">
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
        <!--文章右侧边栏开始-->
        <?php $this->need('component/sidebar.php'); ?>
        <!--文章右侧边栏结束-->
    </div>
</main>

<script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/gsap/3.9.1/gsap.min.js"></script>
<script defer>
    const statisticElement = document.getElementById('statistic');
    if(statisticElement){
    	const targets = {
        	"今日人数": <?php echo $data['today_unique_visitors']; ?>,
        	"今日访问": <?php echo $data['today_views']; ?>,
        	"昨日人数": <?php echo $data['yesterday_unique_visitors']; ?>,
        	"昨日访问": <?php echo $data['yesterday_views']; ?>,
        	"本月访问": <?php echo $data['month_total_views']; ?>,
        	"总访问量": <?php echo $data['total_total_views']; ?>
    	};
    	// 动画时长（秒）
    	const duration = 2;

    	function animateNumbers(entries, observer) {
        	entries.forEach(entry => {
            	if (entry.isIntersecting) {
                	// 为所有目标数字元素添加动画
                	Object.keys(targets).forEach(id => {
                    	const el = document.getElementById(id);
                    	gsap.to(el, {
                        	innerText: targets[id],
                        	duration: duration,
                        	ease: "none",
                        	onUpdate: function () {
                            	el.innerText = Math.floor(el.innerText);
                        	}
                    	});
                	});
                	// Animate only once, so unobserve the element after the animation has started
                	observer.unobserve(statisticElement);
            	}
        	});
    	}

    	// 创建Intersection Observer实例
    	const observer = new IntersectionObserver(animateNumbers, {
        	threshold: 0.5 // 当statistic元素有50%可见时触发
    	});

    	observer.observe(statisticElement);
    }
</script>

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
