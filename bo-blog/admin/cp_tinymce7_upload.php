<?php
/***
 *  用来处理图片上传的文件，上传的图片会保存在目录下面
 */
define('VALIDADMIN', 1);
require_once "../global.php";
$urlParts = parse_url($config['blogurl']);
$baseURL = $urlParts['scheme'] . '://' . $urlParts['host'];
$accepted_origins = array("http://localhost", "http://192.168.1.1", $baseURL);
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Same-origin requests won't set an origin. If the origin is set, it must be valid.
    // 同源请求不会设置Origin。如果设置了Origin，它必须是有效的。
    // 忽略 端口号
    $origin = $_SERVER['HTTP_ORIGIN'];
    $origin = preg_replace('/:\d+$/', '', $origin);

    if (in_array($origin, $accepted_origins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
        header("HTTP/1.1 403 Origin Denied". $_SERVER['HTTP_ORIGIN']);
        return;
    }
}
// 不要尝试在OPTIONS请求上处理上传
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    return;
}
// 检测 是否登录，是否有权限
//print_r($permission);
if ($permission['Upload'] == 0) {
    //echo "004";
    header("HTTP/1.1 403 Forbidden 002");
    return;
}


reset($_FILES);
$temp = current($_FILES);

if (is_uploaded_file($temp['tmp_name'])) {
    /*
     * If your script needs to receive cookies, set images_upload_credentials : true in
     * the configuration and enable the following two headers.
     * */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/[^\p{L}\p{N}\s\-_~,;:\[\]\(\).]|(\.{2,})/u", $temp['name'])) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // 获取图片基本信息
    $upload_filename = urlencode(str_replace('+', ' ', $temp['name']));
    $upload_file_size = $temp['size'];
    $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
    $upload_file=$temp['tmp_name'];

    // Verify extension
//    if (!in_array($ext, array("gif", "jpg", "png"))) {
//        header("HTTP/1.1 400 Invalid extension.");
//        return;
//    }
    // 检查文件扩展名是否符合要求
    $permission['AllowedTypes'] = @explode(' ', $permission['AllowedTypes']);
    if (@!in_array($ext, $permission['AllowedTypes'])) {
        header("HTTP/1.1 400 {$lna[420]} .{$ext}.");
        return;
    }
    // 检查图片大小
    if ($upload_file_size > $permission['MaxSize'] * 1024){
        header("HTTP/1.1 400 {$lna[421]} ( {$permission['MaxSize']} KB).");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    // 组合成相对路径
    if ($mbcon['uploadfolders'] == '1') {
        $targetfolder_ym = date("Ym") . '/';
        $targetfolder = "../attachment/{$targetfolder_ym}";
        if (!is_dir($targetfolder)) {
            $mktargetfolder = @mkdir($targetfolder, 0777);
            if (!$mktargetfolder) {
                header("HTTP/1.1 500 Server Error 001");
                return;
            };
        }
    } else {
        $targetfolder_ym = '';
        $targetfolder = '../attachment';
    }

    //Change name
    $original_uploadname = $upload_filename;
    $upload_filename = time() . '_' . rand(1000, 9999) . substr(md5($original_uploadname), 0, 4) . '.' . $ext;
    if (!move_uploaded_file ($upload_file,"{$targetfolder}/{$upload_filename}")){
        header("HTTP/1.1 500 Server Error 002 {$targetfolder}");
        return;
    }
    @chmod("{$targetfolder}/{$upload_filename}", 0755);
    //Add watermark
    if ($mbcon['wmenable']=='1') {
        $imgext_watermark=array('jpg', 'gif', 'png');
        $lang_wm=explode('|', $lna[999]);
        if (in_array($ext, $imgext_watermark)) {
            unset($watermark_result);
            $watermark_result=create_watermark("{$targetfolder}/{$upload_filename}");
            if (!$watermark_result) $watermark_result="<br>({$lang_wm[0]}: {$lang_wm[8]}{$watermark_err})";
            else $watermark_result="<br>({$lang_wm[0]}: {$watermark_result})";
        } else $watermark_result='';
    } else $watermark_result='';
    $blog->query("INSERT INTO `{$db_prefix}upload` (fid,filepath,originalname,uploadtime,uploaduser) VALUES (null, \"attachment/{$targetfolder_ym}{$upload_filename}\", \"{$original_uploadname}\", {$nowtime['timestamp']}, {$userdetail['userid']})");
    $currentid=$blog->db_insert_id();
    $baseurl = "attachment.php?fid={$currentid}";
    echo json_encode(array('location' => $baseurl));
} else {
    header("HTTP/1.1 500 Server Error 004");
    return;
}





