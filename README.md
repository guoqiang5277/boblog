# boblog
  原作者已经停止开发，终于版本2.1.1 原网站已经下架。<br>
  https://www.bo-blog.com/index.html<br>
  该网址是原作者准备替代boblog的项目，也好久没有开发了。<br>
  我的小站，http://bb.y48.net 欢迎来踩~~ <br>
  报问题地址：https://github.com/guoqiang5277/boblog/issues ，看到后有时间会更新
*** 
  个人比较喜欢旧的风格<br>
  但是随着时间的推移以及技术的革新，旧的采用的内容也逐渐跟不上步伐了<br>
  希望能够修修补补，可以进一步使用吧~~<br>
  有兴趣的 也可以一起~~ <br>
***
# 【原因】

  前几天在一个波波的遗留群里面，里面除了广告之外，已经沉寂了很多年的群，突然一个叫**小白**的网友4-15问，波波是否可以支持php7，然后就有**Yeyo**的网友加入了讨论，大家随便聊聊，感叹了一下。<br>
  其实那么多年，找了很多博客的模板，一直都没有找到合适的，不知道是不是自己太挑了，没有碰到自己合适用的。<br>
  隔天，就自己找到了波波的2.1.1 老版本，安装了一下，中间碰到了一些问题。也在这个群里聊聊，问了问，也感谢**小白**和**Yeyo**。<br><br><br><br>

***
# [文档说明]
* 文件编码格式：UTF-8, 无BOM, Unix(LF)
* 代码缩进：4个空格
* 
* 禁止使用Tab字符, 
* 禁止使用GBK等非UTF-8编码
* 禁止使用Windows风格的换行符(CRLF), 只允许Unix风格的换行符(LF)
***
# [修改文档]
* 数据库编码格式修改为utf8mb4
```sql
-- 切换到你的数据库
USE your_database_name;

-- 将数据库字符集设置为 utf8mb4
ALTER DATABASE your_database_name CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 将表字符集设置为 utf8mb4
ALTER TABLE boblog_blogs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 确保特定列的字符集也是 utf8mb4：
-- 将 content 列的字符集设置为 utf8mb4
ALTER TABLE boblog_blogs MODIFY content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

***
# [插件制作文档]
* 已丢失
# [模板制作文档]
* 已丢失
