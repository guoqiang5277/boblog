========
本檔案分為簡繁兩部分，繁體使用者請往下拉。
========

=====================
Bo-Blog 2.1.1 安装说明

1、安装条件：
要安装Bo-Blog 2.1.1，您的服务器（或虚拟主机，下同）必须满足以下条件：
*PHP版本在 4.1.0 以上；
*支持MySQL，版本在 4.0.0 以上；
*支持session和cookie；
*没有强制添加的广告等会改变输出页面内容的限制。

要顺畅地使用Bo-Blog 2.1.1全部功能，您的服务器最好还应具备下面的条件：
*PHP版本在 4.3.0 以上；
*安装了GD库；
*安装了Zlib Function；

此外，下面的函数如果被禁用，将无法正常使用：
*opendir / readdir
*unlink
*fopen / fsockopen
*error_reporting
*chdir

2、全新安装：
2.1. 将Bo-Blog文件夹下的所有文件上传到服务器上。Unix类服务器请设置以下文件夹的属性为777或保证程序可读写：
  bak/
  data/
  temp/
  temp/openid/
  temp/openid/associations/
  temp/openid/nonces/
  temp/openid/temp/
  attachment/
  post/
警告：如果使用的是Windows服务器，建议不要安装在根目录下。
2.2. 执行 install/install.php。按照指示完成安装。
2.3. 如果您的服务器支持Gzip，请到后台blog设置中打开Gzip压缩。

3、文件组成
安装包里的文件夹作用如下：
*bo-blog - 完整的程序
*documents - 文档说明；
*tools - 一些小工具，详见内附文档。

4、更多信息
1. 可留意“检查更新”来发现新版本。
2. 官方网站：http://www.bo-blog.com
3. 讨论区： http://bbs.bo-blog.com


============
Bo-Blog 2.1.1 安裝說明

1、安裝條件：
要安裝Bo-Blog 2.1.1，您的伺服器（或虛擬主機，下同）必須滿足以下條件：
*PHP版本在 4.1.0 以上；
*支持MySQL，版本在 4.0.0 以上；
*支持session和cookie；
*沒有強制添加的廣告等會改變輸出頁面內容的限制。

要順暢地使用Bo-Blog 2.1.1全部功能，您的伺服器最好還應具備下面的條件：
*PHP版本在 4.3.0 以上；
*安裝了GD庫；
*安裝了Zlib Function；

此外，下面的函數如果被禁用，將無法正常使用：
*opendir / readdir
*unlink
*fopen / fsockopen
*error_reporting
*chdir

2、全新安裝：
2.1. 將Bo-Blog資料夾下的所有檔案上傳到伺服器上。Unix類伺服器請設定以下資料夾的內容為777或保證程式可讀寫：
  bak/
  data/
  temp/
  temp/openid/
  temp/openid/associations/
  temp/openid/nonces/
  temp/openid/temp/
  attachment/
  post/
2.2. 執行 install/install.php。按照指示完成安裝。
2.3. 如果您的伺服器支持Gzip，請到後台blog設定中開啟Gzip壓縮。

3、檔案組成
安裝套件裡的資料夾作用如下：
*bo-blog - 完整的程式
*documents - 文檔說明；
*tools - 一些小工具，詳見內附文檔。

4、更多資訊
1. 可留意“檢查更新”來發現新版本。
2. 官方網站：http://www.bo-blog.com
3. 討論區： http://bbs.bo-blog.com