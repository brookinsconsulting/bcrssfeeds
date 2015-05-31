BC RSS Feeds
===================

This extension implements a solution to provides a template operator and fetch functions that provide additional eZ Publish RSS feed functionality within templates.


Version
=======

* The current version of BC RSS Feeds is 0.1.0

* Last Major update: May 31, 2015


Copyright
=========

* BC RSS Feeds is copyright 1999 - 2016 Brookins Consulting, 2008 Kristof Coomans, 2008 Bertrand Dunogier and 1999 - 2015 eZ Systems AS.

* See: [COPYRIGHT.md](COPYRIGHT.md) for more information on the terms of the copyright and license


License
=======

BC RSS Feeds is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE) file.

BC RSS Feeds is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

BC RSS Feeds is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
BC RSS Feeds under certain conditions. The GNU GPL license
is distributed with the software, see the file doc/LICENSE.

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with BC RSS Feeds in doc/LICENSE.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using BC RSS Feeds under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


Requirements
============

The following requirements exists for using BC RSS Feeds extension:


### eZ Publish version

* Make sure you use eZ Publish version 5.x (required) or higher.

* Designed and tested with eZ Publish Community Project 2014.11


### PHP version

* Make sure you have PHP 5.x or higher.


Features
========

### Dependencies

* This solution depends on eZ Publish Legacy only


### Template operator

This solution overrides the following template operator:

* Template operator : `bcrssfeed`


### Fetch functions

This solution overrides the following template fetch functions:

* Fetch function: `list`

* Fetch function: `subtree_list`

* Fetch function: `has_export_by_node`

* Fetch function: `export_by_node`


Installation
============

### Extension Installation via Composer

Run the following command from your project root to install the extension:

    bash$ composer require brookinsconsulting/bcrssfeeds dev-master;


### Extension Activation

Activate this extension by adding the following to your `settings/override/site.ini.append.php`:

    [ExtensionSettings]
    # <snip existing active extensions list />
    ActiveExtensions[]=bcrssfeeds


### Regenerate kernel class override autoloads

Regenerate kernel class override autoloads (Required).

    php ./bin/php/ezpgenerateautoloads.php;


### Clear the caches

Clear eZ Publish Platform / eZ Publish Legacy caches (Required).

    php ./bin/php/ezcache.php --clear-all;


Usage
=====

The solution is configured to work virtually by default once properly installed.

You can use the template operators and fetch functions within your templates as needed.


### Fetch function 'subtree_list' example

You can edit your pagelayout.tpl template override and add the following code where desired:

    {def $rssfeedlist = fetch( 'rssfeeds', 'subtree_list', hash( 'node_id', $current_node.node_id, 'max_depth', 0 ) )
         $rssfeed = $rssfeedlist.0
         $has_rssfeed = is_object( $rssfeed )}

    {if $has_rssfeed}
    {def $rssfeed_uri = $rssfeed.access_url
         $rssfeed_alt_text = concat( "'", $current_node.name, '\' section\'s RSS Feed. Subscribe to this rss feed for notifications of new content to this secti\
    on')
         $rssfeed_url = concat( '/rss/feed/', $rssfeed_uri )}
    <div id="rssBanner">
    <a href={$rssfeed_url|ezurl} title="{$rssfeed_alt_text}"><img id="rssIconImage" src={"rss_feed_page_icon.png"|ezimage} alt="{$rssfeed_alt_text}" title="{$rs\
    sfeed_alt_text}" /></a>
    </div>
    {/if}

(optional) You can edit your website's css file and add the following styles:

    #rssBanner {
        position: relative;
        float: right;
        width: 32px;
        height: 32px;
    }

    img#rssIconImage {
        position: relative;
        float: right;
        width: 32px;
        height: 32px;
    }


### Template operator 'bcrssfeed' example

#### Template operator 'bcrssfeed' overrview

This operator is used to conveniently display RSS feeds on a website. The idea is that various sections / chapters of the project will show different RSS feeds.

If you consider this structure:

    /
    /News
    /News/Article1
    /News/Article2
    /Company/Article1
    /Company/Article1

What we want is:

* show the 'News' RSS feed when browsing inside the 'News' section
* show the 'Company' RSS feed when browsing inside the 'Company' section
* show the 'Default' RSS feed when browsing another section (content root, or any other module)

The operator will consider that only ONE feed will be shown at a time, and will directly output the <Link...> tag.


#### Template operator 'bcrssfeed' setup requirements

Create a content class named 'RSS Feed' (or anything you think is convenient), with at least 2 attributes, both text lines: title, and url, both mandatory.

Create a 'system' folder (e.g. a folder that won't show from the frontoffice), named for instance 'RSS Feeds'.


##### Template operator 'bcrssfeed' settings customization

Determine what content classes are supposed to be attached an RSS feed. Attached here means that when browsing below that node, a specific RSS feed will be offered.

For these classes, add a new attribute, named 'rss_feed', as an object relation. The selection method can be set to 'Dropdown list', with the default node set to the 'RSS Feeds' container created above.

Finally, override globally the file 'rssfeedoperator.ini', and set the ContentClasses directive to the list of the content classes chosen above. If you consider that both 'folder' and 'frontpage' can be attached RSS feed, configuration will look like this:

    [Settings]
    ContentClasses[]
    ContentClasses[]=frontpage
    ContentClasses[]=folder

##### Template operator 'bcrssfeed' usage example

First, the operator needs to be added to your pagelayout so that the RSS feed link is added. This is done like this:

    {rss_feed({rsfeed( is_set( $module_result.node_id)|choose( 0, $module_result.node_id ) )}

The expected parameter is the node ID to get RSS feed for. If we don't have a node (e.g. outside of the content module), we provide 0 as a parameter.

This piece of code should be placed inside a cache block for performances reason.

Let's say we want to show the RSS feed named 'Default' by default.

We first create a new object of class 'RSS Feed' in the 'RSS Feeds' folder. We use 'Default' as its title, and 'http://exemple.com/rss/feed/default' as the URL to the feed.

Then, we edit the root folder of the content tree, and in the rss_feed attribute, we choose the 'Default' object.

Now if we want to show the 'News' rss feed when browsing the 'News' subtree, we follow these two steps again: create a new 'RSS Feed' content object in the 'RSS Feeds' folder, setting the title to 'News', and the URL to 'http://exemple.com/rss/feed/news', then edit the 'News' content object, and pick the newly created 'News' RSS Feed object as the RSS Feed attribute or our folder / frontpage.


Troubleshooting
===============

### Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md)


### Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)

