<?PHP
if (!defined('VALIDREQUEST')) die ('Access Denied.');
addbar('header', array('index', 'alltags', 'guestbook', 'togglesidebar', 'viewlinks', 'archivelink', 'starred'));
addbar('sidebar', array('category', 'calendar', 'statistics', 'search', 'entries', 'replies', 'columnbreak', 'link', 'archive', 'misc'));
addbar('footer', array('copyright'));
