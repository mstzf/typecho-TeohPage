<?php
/**
 * TeohPage Plugin
 *
 * @package TeohPage
 * @author TeohZY
 * @version 1.0.0
 * @link https://blog.teohzy.com
 */

class TeohPage_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        self::copyTemplateToTheme();
        Typecho_Plugin::factory('admin/write-page.php')->bottom = array('TeohPage_Plugin', 'addTemplateOption');
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('TeohPage_Plugin', 'applyTemplate');
        return _t('插件已激活');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
        $themeDir = __DIR__ . '/../../themes/' . Helper::options()->theme;
        $templateFile = $themeDir . '/page-teoh.php';
        if (file_exists($templateFile)) {
            unlink($templateFile);
        }
        return _t('插件已禁用');
    }

    /**
     * 获取插件配置面板
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $html = new Typecho_Widget_Helper_Form_Element_Textarea('html', NULL, '', _t('HTML 内容'), _t('输入自定义页面的 HTML 内容'));
        $form->addInput($html);

        $css = new Typecho_Widget_Helper_Form_Element_Textarea('css', NULL, '', _t('CSS 链接'), _t('输入自定义页面的 CSS 链接，每行一个'));
        $form->addInput($css);

        $cssCode = new Typecho_Widget_Helper_Form_Element_Textarea('cssCode', NULL, '', _t('CSS 代码'), _t('输入自定义页面的 CSS 代码'));
        $form->addInput($cssCode);

        $js = new Typecho_Widget_Helper_Form_Element_Textarea('js', NULL, '', _t('JavaScript 链接'), _t('输入自定义页面的 JavaScript 链接，每行一个'));
        $form->addInput($js);

        $jsCode = new Typecho_Widget_Helper_Form_Element_Textarea('jsCode', NULL, '', _t('JavaScript 代码'), _t('输入自定义页面的 JavaScript 代码'));
        $form->addInput($jsCode);
        $showHeader = new Typecho_Widget_Helper_Form_Element_Radio('showHeader', array('1' => '显示', '0' => '隐藏'), '1', _t('是否显示头部'));
        $form->addInput($showHeader);

        $showSidebar = new Typecho_Widget_Helper_Form_Element_Radio('showSidebar', array('1' => '显示', '0' => '隐藏'), '1', _t('是否显示侧边栏'));
        $form->addInput($showSidebar);

        $showFooter = new Typecho_Widget_Helper_Form_Element_Radio('showFooter', array('1' => '显示', '0' => '隐藏'), '1', _t('是否显示页脚'));
        $form->addInput($showFooter);

        $showComments = new Typecho_Widget_Helper_Form_Element_Radio('showComments', array('1' => '显示', '0' => '隐藏'), '1', _t('是否显示评论'));
        $form->addInput($showComments);

        $showContent = new Typecho_Widget_Helper_Form_Element_Radio('showContent', array('1' => '显示', '0' => '隐藏'), '1', _t('是否显示文章内容'));
        $form->addInput($showContent);

        // 添加备份和恢复按钮
        $backupButton = new Typecho_Widget_Helper_Form_Element_Submit();
        $backupButton->value(_t('备份配置'));

        $backupButton->input->setAttribute('class', 'btn btn-s btn-warn btn-operate');
        $backupButton->input->setAttribute('formaction', Typecho_Common::url('/options-plugin.php?config=TeohPage&action=backup', Helper::options()->adminUrl));
        $form->addInput($backupButton);


        $restoreButton = new Typecho_Widget_Helper_Form_Element_Submit();
        $restoreButton->value(_t('恢复配置'));

        $restoreButton->input->setAttribute('class', 'btn btn-s btn-warn btn-operate');
        $restoreButton->input->setAttribute('formaction', Typecho_Common::url('/options-plugin.php?config=TeohPage&action=restore', Helper::options()->adminUrl));
        $form->addInput($restoreButton);
        // 检查是否有备份文件


    }

    /**
     * 个人用户的配置面板
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 在页面编辑界面添加模板选项
     */
    public static function addTemplateOption()
    {
        ?>
        <script>
            $(document).ready(function () {
                var templateOption = '<option value="page-teoh">Teoh界面</option>';
                $('#template').append(templateOption);
            });
        </script>
        <?php
    }

    /**
     * 应用自定义模板
     */
    public static function applyTemplate($archive)
    {
        if ($archive->is('page') && $archive->template == 'page-teoh') {
            $archive->setThemeFile('page-teoh.php');
        }
    }

    /**
     * 复制模板文件到主题目录
     */
    private static function copyTemplateToTheme()
    {
        $themeDir = __DIR__ . '/../../themes/' . Helper::options()->theme;
        $templateFile = $themeDir . '/page-teoh.php';
        if (!file_exists($templateFile)) {
            copy(__DIR__ . '/templates/page-teoh.php', $templateFile);
        }
    }

    /**
     * 备份配置
     */
    public static function backupConfig()
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('TeohPage');
        $config = array(
            'html' => $options->html,
            'css' => $options->css,
            'cssCode' => $options->cssCode,
            'js' => $options->js,
            'jsCode' => $options->jsCode,
            'showHeader' => $options->showHeader,
            'showSidebar' => $options->showSidebar,
            'showFooter' => $options->showFooter,
            'showComments' => $options->showComments,
            'showContent' => $options->showContent,
        );
        $jsonConfig = json_encode($config);

        // 调试信息
        $backupFilePath = __DIR__ . '/config_backup.json';


        $result = file_put_contents($backupFilePath, $jsonConfig);

        if ($result === false) {
            Typecho_Widget::widget('Widget_Notice')->set(_t("备份文件写入失败！"), 'error');
        } else {
            Typecho_Widget::widget('Widget_Notice')->set(_t("配置已成功备份！"), 'success');
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * 恢复配置
     */
    public static function restoreConfig()
    {
        $backupFile = __DIR__ . '/config_backup.json';
        if (file_exists($backupFile)) {
            $jsonConfig = file_get_contents($backupFile);
            $config = json_decode($jsonConfig, true);
            if ($config) {
                Helper::configPlugin('TeohPage', $config);
                Typecho_Widget::widget('Widget_Notice')->set(_t("配置已成功恢复！"), 'success');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                Typecho_Widget::widget('Widget_Notice')->set(_t("配置文件格式错误！"), 'error');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } else {
            Typecho_Widget::widget('Widget_Notice')->set(_t("没有找到备份文件！"), 'error');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}

// 处理备份和恢复请求
if (isset($_GET['action'])) {
    $action=$_GET['action'];
    if ($_GET['action'] == 'backup') {
        TeohPage_Plugin::backupConfig();
    } elseif ($_GET['action'] == 'restore') {
        TeohPage_Plugin::restoreConfig();
    }
}