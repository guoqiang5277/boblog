/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('filedownload', 'en,zh_tw_utf8,zh_cn_utf8');

// Plucin static class
var TinyMCE_FiledownloadPlugin = {
	getInfo : function() {
		return {
			longname : 'File download',
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
			case "filedownload":
				return tinyMCE.getButtonHTML(cn, 'lang_filedownload_desc', '{$pluginurl}/images/filedownload.gif', 'mceFiledownload');
		}

		return "";
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceFiledownload":
				var template = new Array();

				template['file'] = '../../plugins/filedownload/filedownload.htm'; // Relative to theme
				template['width'] = 450;
				template['height'] = 160;

				tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});

				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('filedownload', TinyMCE_FiledownloadPlugin);
