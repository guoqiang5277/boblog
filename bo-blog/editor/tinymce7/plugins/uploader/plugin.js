/**
 * TinyMCE version 7.1.0 (2024-05-08)
 */

(function () {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const openLocalWindow = editor => {
      editor.windowManager.open({
        title: 'Local Window',
        body: {
          type: 'panel',
          items: [{
              type: 'htmlpanel',
              html: '<p>Local Window</p>'
            }]
        },
        buttons: [{
            type: 'cancel',
            name: 'close',
            text: 'Close'
          }]
      });
    };
    const openUrlWindow = editor => {
      editor.windowManager.openUrl({
        title: 'uploader manager',
        url: 'admin.php?act=upload&useeditor=tinymce7',
        width: 800,
        height: 280
      });
    };

    const register$1 = editor => {
      editor.addCommand('openLocalWindow', () => openLocalWindow(editor));
      editor.addCommand('openUrlWindow', () => openUrlWindow(editor));
    };

    const register = async editor => {
      editor.ui.registry.addButton('uploader', {
        tooltip: 'uploader manager',
        icon: 'uploader',
        onAction: () => {
          editor.execCommand('openUrlWindow');
        }
      });
    };

    var Plugin = () => {
      global.add('uploader', editor => {
        register$1(editor);
        register(editor);
      });
      global.requireLangPack('uploader', 'en,zh_CN,zh_TW');
    };

    Plugin();

})();
