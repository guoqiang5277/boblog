<?php

if (!defined('VALIDADMIN')) die ('Access Denied.');



/**
 * 获取编辑器列表
 *
 * 该函数用于生成一个包含所有可用编辑器的下拉选择列表。它通过遍历editor目录下的子目录来发现可用的编辑器。
 * 每个编辑器目录中如果存在config.php文件，则认为该编辑器是可用的，并从config.php中获取编辑器的显示名称。
 *
 * @param string $currenteditor 当前使用的编辑器名称，默认为'ubb'。
 * @return string 返回一个包含编辑器选择选项的HTML下拉列表。
 */
function editor_Html_getEditorList($currenteditor = 'ubb')
{
    global $lna;
    // 打开editor目录，以便遍历其中的子目录
    $editors = @opendir("editor");
    // 初始化编辑器选择列表的HTML代码
    $jeditorbody = '<select name="useeditor" id="useeditor" class="formselect">';
    // 遍历editor目录下的每个文件或目录
    while ($editor = @readdir($editors)) {
        // 排除当前目录和父目录，只处理子目录
        if ($editor != "." && $editor != ".." && is_dir("editor/{$editor}")) {
            // 如果编辑器目录下存在config.php文件，则认为该编辑器是可用的
            if (file_exists("editor/{$editor}/config.php")) {
                // 引入编辑器的配置文件，从中获取编辑器的显示名称等信息
                require_once "editor/{$editor}/config.php";
                // 如果配置文件中定义了$jeditor变量，则该编辑器信息有效
                if (isset($jeditor)) {
                    // 根据当前编辑器是否与当前使用的编辑器相同，决定是否选中该选项
                    if ($editor == $currenteditor) {
                        $jeditorbody .= "<option value=\"{$editor}\" selected=\"selected\">{$jeditor["displayname"]}</option>";
                    } else {
                        $jeditorbody .= "<option value=\"{$editor}\">{$jeditor["displayname"]}</option>";
                    }
                }
            }
        }
    }

    // 完成编辑器选择列表的HTML代码，并关闭目录句柄
    $jeditorbody .= '</select>';
    @closedir($editors);

    // 返回生成的编辑器选择列表的HTML代码
    return $jeditorbody;
}

function editor_Html_getEditorListString()
{
    global $lna;
    // 打开editor目录，以便遍历其中的子目录
    $editors = @opendir("editor");
    // 初始化编辑器选择列表的HTML代码
    $jeditorbody = "editortype|{$lna[567]}|";
    // 遍历editor目录下的每个文件或目录
    while ($editor = @readdir($editors)) {
        // 排除当前目录和父目录，只处理子目录
        if ($editor != "." && $editor != ".." && is_dir("editor/{$editor}")) {
            // 如果编辑器目录下存在config.php文件，则认为该编辑器是可用的
            if (file_exists("editor/{$editor}/config.php")) {
                // 引入编辑器的配置文件，从中获取编辑器的显示名称等信息
                require_once "editor/{$editor}/config.php";
                // 如果配置文件中定义了$jeditor变量，则该编辑器信息有效
                if (isset($jeditor)) {
                    $jeditorbody.="$editor>>{$jeditor["displayname"]}<<";
                }
            }
        }
    }
    // 使用 substr() 函数移除末尾的两个字符<<
    $jeditorbody = substr($jeditorbody, 0, -2);
    @closedir($editors);
    // 返回生成的编辑器选择列表的HTML代码
    return $jeditorbody;
}
