/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('quoteubb', 'en,zh_tw_utf8,zh_cn_utf8');

// Plucin static class
var TinyMCE_QuoteubbPlugin = {
	getInfo : function() {
		return {
			longname : 'Quote and Code',
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
			case "quoteubb":
				return tinyMCE.getButtonHTML(cn, 'lang_quoteubb_desc', '{$pluginurl}/images/quoteubb.gif', 'mceQuoteubb');
		}

		return "";
	},


	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		if (node == null)
			return;

		if (any_selection) {
			tinyMCE.switchClass(editor_id + '_quoteubb', 'mceButtonNormal');
			return true;
		}

		tinyMCE.switchClass(editor_id + '_quoteubb', 'mceButtonDisabled');

		return true;
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceQuoteubb":
				var anySelection = false;
				var inst = tinyMCE.getInstanceById(editor_id);
				var focusElm = inst.getFocusElement();
				var selectedText = inst.selection.getSelectedText();
				if (tinyMCE.selectedElement)
					anySelection = (selectedText && selectedText.length > 0);	
				
				if (anySelection) {
					var template = new Array();
					template['file'] = '../../plugins/quoteubb/quoteubb.htm'; // Relative to theme
					template['width'] = 450;
					template['height'] = 160;
					tinyMCE.openWindow(template, {editor_id : editor_id, anysel: selectedText, inline : "yes"});
					return true;
				}
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('quoteubb', TinyMCE_QuoteubbPlugin);
