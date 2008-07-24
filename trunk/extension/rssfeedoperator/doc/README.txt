Template operator: rss_feed
===========================

This operator is used to conveniently display RSS feeds on a website. The idea
is that various sections / chapters of the project will show different RSS feeds.

If you consider this structure:

/
/News
/News/Article1
/News/Article2
/Company/Article1
/Company/Article1

What we want is:
 - show the 'News' RSS feed when browsing inside the 'News' section
 - show the 'Company' RSS feed when browsing inside the 'Company' section
 - show the 'Default' RSS feed when browsing another section (content root, or any other module)

The operator will consider that only ONE feed will be shown at a time,
and will directly output the '<Link...> tag.

INSTALLATION
------------
Extract the archive to the 'extension' folder of your eZ publish instance, and
enable it by adding ActiveExtension[]=rssfeedoperator to your site.ini file.

Create a content class named 'RSS Feed' (or anything you think is convenient),
with at least 2 attributes, both text lines: title, and url, both mandatory.

Create a 'system' folder (e.g. a folder that won't show from the frontoffice), named
for instance 'RSS Feeds'.

Determine what content classes are supposed to be attached an RSS feed. Attached
here means that when browsing below that node, a specific RSS feed will be offered.

For these classes, add a new attribute, named 'rss_feed', as an object relation.
The selection method can be set to 'Dropdown list', with the default node set to
the 'RSS Feeds' container created above.
Finally, override globally the file 'rssfeedoperator.ini', and set the
ContentClasses directive to the list of the content classes chosen above. If you
consider that both 'folder' and 'frontpage' can be attached RSS feed, configuration
will look like this:

[Settings]
ContentClasses[]
ContentClasses[]=frontpage
ContentClasses[]=folder

USAGE
-----
First, the operator needs to be added to your pagelayout so that the RSS feed
link is added. This is done like this:
{rss_feed({rsfeed( is_set( $module_result.node_id)|choose( 0, $module_result.node_id ) )}
The expected parameter is the node ID to get RSS feed for. If we don't have a node
(e.g. outside of the content module), we provide 0 as a parameter.
This piece of code should be placed inside a cache block for performances reason.

Let's say we want to show the RSS feed named 'Default' by default.
We first create a new object of class 'RSS Feed' in the 'RSS Feeds' folder. We use
'Default' as its title, and 'http://exemple.com/rss/feed/default' as the URL to the feed.

Then, we edit the root folder of the content tree, and in the rss_feed attribute,
we choose the 'Default' object.

Now if we want to show the 'News' rss feed when browsing the 'News' subtree, we follow
these two steps again: create a new 'RSS Feed' content object in the 'RSS Feeds' folder,
setting the title to 'News', and the URL to 'http://exemple.com/rss/feed/news',
then edit the 'News' content object, and pick the newly created 'News' RSS Feed object
as the RSS Feed attribute or our folder / frontpage.