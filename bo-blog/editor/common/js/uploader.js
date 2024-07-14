/**
 *  Bo-Blog Uploader
 *  Bo-Blog Uploader JavaScript Library
 *  公共部分
 *
 * */
function picPreview(filename, attachid) {
    //currentpicname=filename;
    var divdvs = "<div style='margin-top: 0px;  width: 400px !important; width: 320px; height: 165px; background-size: contain; background-repeat: no-repeat; background-position: 130px 20px !important; background-position: 0px 20px; background-image: url(" + filename + ");'>" + jslang[18] + "<input type='text' id='picalign' value='m' size=1 maxlength=1>(l-" + jslang[19] + ",m-" + jslang[20] + ",r-" + jslang[21] + ") <input type='button' value='" + jslang[22] + "' onclick=\"doinsertimage('" + attachid + "');\"></div>";

    document.getElementById('picp').innerHTML = divdvs;
}