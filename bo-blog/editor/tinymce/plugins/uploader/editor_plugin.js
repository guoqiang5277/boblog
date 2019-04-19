/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('uploader', 'en,zh_tw_utf8,zh_cn_utf8');

// Plucin static class
var TinyMCE_UploaderPlugin = {
	getInfo : function() {
		return {
			longname : 'Uploader',
			author : 'Bo-Blog',
			authorurl : 'http://www.bo-blog.com',
			infourl : 'http://www.bo-blog.com',
			version : '1.0'
		};
	},

	/**
	 * Returns the HTML contents of the emotions control.
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "uploader":
				return tinyMCE.getButtonHTML(cn, 'lang_uploader_desc', '{$pluginurl}/images/uploader.gif', 'mceUploader');
		}

		return "";
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceUploader":
				var template = new Array();

				template['file'] = '../../../../admin.php?act=upload&useeditor=tinymce'; // Relative to theme
				template['width'] = 580;
				template['height'] = 200;

				tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});

				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('uploader', TinyMCE_UploaderPlugin);
