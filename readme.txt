=== Twitter Follow Friday ===
Contributors: ufunk
Donate link: http://www.fabien-bouchard.com/wp-followfriday/wp-followfriday-for-wordpresstwitter/
Tags: twitter, followfriday, recommendations, widget, tweet
Requires at least: 2.8.4
Tested up to: 2.8.4
Stable tag: 0.3

A Worpress plugin for Twitter recommendations aka FollowFriday, select them in the admin panel and display them in a widget, a post or a page !

== Description ==

WP-FollowFriday Wordpress plugin working with Twitter API. It allow you to select your recommendations into your following and to display them in a widget, a post, a page or in your wordpress template. You can use as a #FollowFriday to display on your blog or website.
You can now make as much different recommendation lists as you want and choose which you want to be displayed in your widget and in your page and post !
The displayed code is entirely customizable, you can display the ScreenName, the picture or the link of every recommendation in your site or blog format and style !

== Installation ==

This section describes how to install the plugin and get it working.

INSTALLATION

1. Donwload the .zip archive and extract it. Then put the wpfollowfriday folder in your "/wp-content/plugins"  folder.
1. In your admin panel, activate theplugin
1. Open the "Follow Friday Settings"  tab in your admin panel, and enter your Twitter user name (ScreenName).
1. You can modify the display of your recommendation by changing the code in the "Html code pattern"  field (using the %screenName% and %urlAvatar% tags) or by adding code in the "Html code before"  et "Html code after"  fields.
1. You can add or delete recommendation list, and change their display order.

HOW TO USE

2. Open the "Manage Recommandations"  tab in your admin panel. Display your Twitter following checking the  "Display your following list to add recommendations"  box. Then you just have to select which following you want to recommend, then choose in which list you want to add them and submit.
2. The displaying of your recommendation can be done in three way :
* Display your recommendations using either the widget (select the displayed list in Follow Friday Settings)
* Using the ||-WPFOLLOWFRIDAY-|| tag in your posts or pages (for all lists)
* Using the ||-WPFF:{lists_id}:WPFF-|| tag in your posts or pages (for selected lists) example : ||-WPFF:1:WPFF-|| or ||-WPFF:2,5,4:WPFF-||
* Using the <?php wpfollowfriday_write(); ?> tag in your template . 


== Frequently Asked Questions ==

= When I want to display my Twitter followings, I always receive a "Connection Fail due to Twitter server... please try again later" message =

If you have a mutualized Hosting, sometimes your server can't receive data from Twitter API due to the Twitter limitation.

== Screenshots ==

1. An exemple of the use of FollowFriday, displayed in a post.

== Changelog ==

= 0.3 = You can now make as much different recommendation lists as you want and choose which you want to be displayed in your widget and in your page and post ! + some bug correction 
= 0.2 = Possibility to display your followfriday in your sidebar using the included Widget, in a page or in a post using the ||-WPFOLLOWFRIDAY-|| tag inside them, in your wordpress template using the <?php wpfollowfriday_write(); ?> tag
