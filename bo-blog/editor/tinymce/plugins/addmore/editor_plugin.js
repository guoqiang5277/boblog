tinyMCE.importPluginLanguagePack('addmore', 'en,zh_cn_utf8,zh_tw_utf8');

var TinyMCE_AddmorePlugin = {
	getInfo : function() {
		return {
			longname : 'Insert separator/newpage',
			author : 'Bo-Blog',
			authorurl : 'http://www.bo-blog.com',
			infourl : 'http://www.bo-blog.com',
			version : '1.0'
		};
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "insertseparator":
				return tinyMCE.getButtonHTML(cn, 'lang_addmore_desc', '{$pluginurl}/images/more.gif', 'mceAddseparator');

			case "insertnewpage":
				return tinyMCE.getButtonHTML(cn, 'lang_newpage_desc', '{$pluginurl}/images/page.gif', 'mceAddnewpage');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceAddseparator":
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '[separator]');
				return true;

			case "mceAddnewpage":
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '[newpage]');
				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

tinyMCE.addPlugin("addmore", TinyMCE_AddmorePlugin);
