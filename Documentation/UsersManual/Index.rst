.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _userManual:


.. only:: html

	This chapter describes how to use the extension.

.. toctree::
	:maxdepth: 5
	:titlesonly:

Basic caching knowledge Know-How
================================

Default caching since TYPO3 4.3
-------------------------------

The caching framework which is delivered by core since TYPO3 4.3 provides an API to store the result of an
expensive operation in different cache backends. The trick is to quickly calculate an unique cache identifier,
and ask the engine for a cache entry with this identifier. The cache engine will return the entry if there is such
a cache entry. If false, the expensive operation has to be done and the result is inserted in the cache.
Additionally, a lifetime can be assigned to the cache entry, together with a group of tags helping to drop specific
cache entries if they must be invalidated.

In case of the usual page cache TYPO3 does the following: If a page is requested in frontend TYPO3 first builds an
identifier out of all parameters that specify this unique page content (based on page-ID, FE user group memberships,
GET params, …), requests the caching framework with this identifier and returns the content if found. If not found,
it does all the page calculation, inserts a cache entry with this identifier and the calculated content, with a default
lifetime of one day and the tag “pageId_42” (42 is the requested page id) and finally returns the content. If later on
a backend user changes a record on this page, TYPO3 will invalidate the now “dirty” cache entry by dropping all cache
entries with the tag “pageId_42”.

One cache entry has exactly one identifier but can have multiple tags, and one tag can be assigned to multiple cache
entries. So, if TYPO3 has to calculate many different page cache entries for one page (eg. because of different user
groups) all of them will be tagged with “pageId_42”, so all of them will be dropped upon changing page records on this page.

How enetcache extends the page caching
--------------------------------------

Basic frontend cycle
....................

Enetcache basically breaks the page cache method down to a plugin element level. The page cache is still in effect,
but plugin content elements get their own cache for storing content. This is not done automatically, plugins must
actively get and set content to the engine and must provide proper tagging and lifetime handling.
The basic frontend handling is as follows:

- On page request TYPO3 does the usual page cache lookup and returns the cache entry if found.
- If not found TYPO3 instantiates all plugins on the page and calculates their content.
- If a plugin content element implements enetcache, it calculates an identifier and asks enetcache to get a cache entry
  with this identifier. If not found, it calculates the content and sets the cache entry together with an individual
  lifetime and a group of tags (usually at least the records used in the content, eg. tx_comments_comment_42), and
  returns the content to TYPO3.
- Enetcache injects all given tags by plugins as additional tags to page cache tags and sets the lifetime of the page
  cache entry to the lowest lifetime of all plugins using enetcache.
- TYPO3 returns the content.

On a page with two plugins, one with a long and one with a short lifetime, the following will happen: The page cache
invalidates after end of lifetime of the plugin with the shortest lifetime. On page request the plugin with the long
lifetime will be fetched from enetcache, while the other one will be recalculated. The new page cache entry will have
all tags of both plugins again and will be set to a lifetime of the short plugin.

Re-using cache entries between pages
....................................

This is useful if a plugin with identical configuration (same identifier) is used on many pages: If one page request
initiates the rendering and set() of a content element, the get() request of this plugin on another page will return
the content rendered for the first page.

Example: You want to display a tag cloud of search words that where issued to your search engine the last 24 hours.
You log given search words in some table and want to refresh your tag cloud every 20 minutes to show current results.
With usual TYPO3 caching you would need to set every page to a cache lifetime of 20 minutes, or make the tag cloud
a USER_INT which would at least triple your page render time on every page request. If you use a USER object your
tag cloud will show different results on different pages if new search words where inserted to your log table between
requests to the pages. With enetcache your tag cloud will be inserted to cache once, every request to other pages will
re-use the prior rendered content.

Automatic tag clearing in backend
.................................

It's good practice to store plugin element records on own storage folder pages. With tons of news records and some
categories you usually configure a pageTS for your news storage folder to drop caches of news frontend pages if you
change, add or delete news records. In worst case, all your list and detail frontend page caches instantly invalidate
if just one news record is changed. On a high traffic site this could easily bring a server system down to it's knees
if none of the following page requests are answered from cache.

To solve this problem enetcache hooks into datahandler to do special cache clearing based on tags. If list and
detail pages are tagged with the news records used (eg. tx_news_42, tx_news_23), enetcache will drop tx_news_42 if
this record is changed, so all other detail page caches will not be invalidated.

By default enetcache drops the following tags:

- “tablename_uid”
- “tablename_command”: command is one of new, delete, undelete, move, copy.
- “tablename”

The command clearing is useful for list pages that need to be re-rendered if new records are added in the backend:
If a list page should display ten records and is tagged with “tablename_new”, and a new record is added on top,
this tag makes sure the cache entry is dropped and re-rendered on next request. Dropping “tablename” is a bit more
brutal, tagging cache entries with “tablename” should be used with care.

A second drop mechanism comes in handy if referenced records are added and cache entries must be invalidated: Imagine
a news system with news records and categories. A news can be assigned to one ore more categories (exactly like tx_news does).
A frontend page displays a list of all news of a category and is tagged with the used news ids (tx|news_4711) and the
category (tx_news_cat_23). If a new news record is added to category 23, the cache entry of this list must be invalidated.
Enetcache loads the TCA of the new news item, searches for relations (foreign_table), makes a look up which relations
exist and drops the tablename_uid tag of the foreign record. In this case the tag “tx_news_cat_23” would be automatically
dropped. It will not drop tx_news_cat_24 if the news handled is not assigned to this category. We call this “forward dropping”.

For special cases it's easily possible to hook into datahandler and do additional tag dropping.

API definition
==============

class PluginCache
.................

This singleton class can be used in FE, BE, by eID controllers and even by scheduler tasks.
Usually called by GeneralUtility::makeInstance(PluginCache::class)->method();

+-----------------------------------------+----------------------------------------------------+
| Method                                  | Description                                        |
+=========================================+====================================================+
| get(array $identifier, $respectNoCache) | Get an entry with given identifier from cache.     |
|                                         | Returns either data entry, or FALSE on cache miss. |
+-----------------------------------------+----------------------------------------------------+
| set(array $identifier, $data,           | Set an entry in enetcache.                         |
|   array $tags, $lifetime,               |                                                    |
|   $respectNoCache)                      |                                                    |
+-----------------------------------------+----------------------------------------------------+
| drop(array $tags)                       | Drop all cache entries in page cache and enetcache |
|                                         |  which are tagged with one of the tags.            |
+-----------------------------------------+----------------------------------------------------+
| flush()                                 | Delete enetcache entries. This is rarely used,     |
|                                         |  page cache is not flushed!                        |
+-----------------------------------------+----------------------------------------------------+

