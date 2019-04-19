<?php
$elements['rss']=<<<eot
<item>
<link>{entryurl}</link>
<title><![CDATA[{entrytitle}]]></title> 
<author>{entryauthor} &lt;{entryemail}&gt;</author>
<category><![CDATA[{entrycate}]]></category>
<pubDate>{entrytime}</pubDate> 
<guid>{entryurl}</guid> 
<description>
<![CDATA[ 
	{entrycontent}
]]>
</description>
</item>
eot;

$elements['rssbody']=<<<eot
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title><![CDATA[{blogname}]]></title> 
<link>{blogurl}/index.php</link> 
<description><![CDATA[{blogdesc}]]></description> 
<language>{bloglanguage}</language> 
<copyright><![CDATA[{blogname}]]></copyright>
{rssbody}
</channel>
</rss>
eot;
?>