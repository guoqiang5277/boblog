var realpath = "attachment/";

function generateUpload(attachid, filename) {
    filename = filename.replace(/\*/g, '.');
    filename = realpath + filename;
    console.log(filename);
    if (document.getElementById('ifautoaddubb').checked) {
        filename = autoattachubb(attachid, filename);
    }
    window.parent.tinymce.activeEditor.execCommand('mceInsertContent', false, filename);
    window.parent.tinymce.activeEditor.windowManager.close();
}

function autoattachubb(attachid, filename) {
    var finalresult;
    finalresult = "[file]" + attachid + "[/file]";
    var extindex = filename.lastIndexOf(".");
    if (extindex != -1) {
        var realext = filename.substring(extindex + 1).toLowerCase();
        if (realext == "gif" || realext == "jpg" || realext == "png" || realext == "bmp" || realext == "jpeg") {
            var antileechurl;
            antileechurl = attachid.replace(/\[attach\]/g, '');
            antileechurl = antileechurl.replace(/\[\/attach\]/g, '');
            antileechurl = "attachment.php?fid=" + antileechurl;
            finalresult = "<img src='" + antileechurl + "' alt='' border='0' class='insertimage' />";
        } else if (realext == "swf") {
            finalresult = "[swf=400,300]" + attachid + "[/swf]";
        } else if (realext == "wma" || realext == "mp3" || realext == "asf" || realext == "wmv") {
            finalresult = "[wmp=400,300]" + attachid + "[/wmp]";
        } else if (realext == "rm" || realext == "rmvb" || realext == "ra" || realext == "ram") {
            finalresult = "[real=400,300]" + attachid + "[/real]";
        } else if (realext == "htm" || realext == "html") {
            finalresult = "[url]" + attachid + "[/url]";
        } else if (realext == "zip" || realext == "rar") {
            finalresult = "[file]" + attachid + "[/file]";
        }
    }
    return finalresult;
}
