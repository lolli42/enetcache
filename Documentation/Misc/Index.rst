.. include:: ../Includes.txt

.. _misc:

Hints and Pitfalls
==================

Identifiers decisions
---------------------

The full identifier is hashed with md5() in enetcache. Main principle for the identifier of a cache entry is:
Keep it as small as possible but add as much as needed to create an unique identifier. Typical values that should be
considered to include to a identifier are:

- TS configuration, maybe merged with flexform values. Ideally stripped down to some values.
- Some plugin variables, if the plugin produces different content for given values.
- FE groups, if the plugin creates different content for different groups. Sometimes group membership itself is
  irrelevant and it's only “logged in content” and “non logged in content”. Then a boolean flag should be added to
  the identifier and only two different cache entries will be created for this plugin.
- Avoid to include the page ID and content-element ID in the identifier. This would force a single cache entry on
  every page with this plugin and negates a nice advantage of the plugin cache.

Tag decisions
-------------

Tags are important to be set right especially for long living cache entries. It's usually a good idea to add
“tablename”_”uid” as tags for every handled record. This will ensure the cache entry is dropped if those records are
changed in the BE. List pages should proably add “tablename”_”new”, too.  Those cache entries are dropped if new
records  are added to this table.

Configuration
=============

Extension Manager
-----------------

The extension manager provides a configuration option to set the default lifetime of a cache entry if none was given
in a set() call. Default lifetime is 86400 seconds (1 day). This is a fallback setting, we recommend to add
configuration settings to your plugins to set cachetimes on TypoScript and / or FlexForm level.

Scheduler task to drop cache entries tagged with given tags
-----------------------------------------------------------

The extension comes with a scheduler task which allows to drop cache entries tagged with a list of given tags on a
regular basis. This can be handy to for example invalidate cache entries every midnight. Just add the task and give
it a comma separated list of tags.

CLI script to drop cache entries tagged with given tags
-------------------------------------------------------

The cli script is very similar to the scheduler task above and allows to drop cache entries tagged with a list of
tags via a command line call. This can be handy if for example an extension displays data which is imported by a script.
The script can call the cli to drop cache entries after a re-import was finished.

Scheduler task garbage collection
---------------------------------

The core delivers a task called “Caching framework garbage collection”. It is a good idea to run it once in a while,
for example once at night. The task walks through all configured caches and calls the garbage collection if a cache
uses one of the chosen backends.

Known problems
==============

- Do not nest sub USER_INT objects within your cached USER objects. This will fail because needed information
  in $GLOBALS['TSFE']->config['INTincScript'] will not be given if content elements with sub-USER_INT's are retrieved
  from enetcache. If USER_INT's are needed  within cached plugins, this must be handled by the plugin itself, probably
  by building differential arrays of parts of TSFE->config at the beginning and end of the plugin, caching those
  information and injecting explicitly after successful get.
- enetcache is not aware of starttime / endtime handling of records. If this feature is used, the lifetime given to
  set() must be handled in the calling plugin.
- We are currently unsure if enetcache handles TS config.cache_clearAtMidnight correctly (probably not).
  If lifetime handling is done right, this TYOP3 core feature is not needed anyway.
- Automagic tag dropping is probably incomplete and/or broken in a workspace environment.

Links
=====

Core Caching Framework documentation:
https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/CachingFramework/Index.html
