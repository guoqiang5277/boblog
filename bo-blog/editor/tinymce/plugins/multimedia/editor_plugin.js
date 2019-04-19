/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('multimedia', 'en,zh_tw_utf8,zh_cn_utf8');

// Plucin static class
var TinyMCE_MultimediaPlugin = {
	getInfo : function() {
		return {
			longname : 'Multimedia',
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
			case "multimedia":
				return tinyMCE.getButtonHTML(cn, 'lang_multimedia_desc', '{$pluginurl}/images/multimedia.gif', 'mceMultimedia');
		}

		return "";
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceMultimedia":
				var template = new Array();

				template['file'] = '../../plugins/multimedia/multimedia.htm'; // Relative to theme
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
tinyMCE.addPlugin('multimedia', TinyMCE_MultimediaPlugin);
