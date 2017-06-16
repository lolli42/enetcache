.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. _introduction:

Introduction
============

.. only:: html

	This chapter gives you a basic introduction about the TYPO3 CMS extension "*enetcache*".

.. toctree::
	:maxdepth: 5
	:titlesonly:

Target Audience
---------------

This extension is intended for developers who need a caching solution in TYPO3 for dynamic high traffic sites.
This manual focuses on implementing enetcache in your own extensions. Hints are given about proper tagging and
identifier building that need to be taken into consideration to receive a working implementation.
It also explains how a wrapper to existing plugins could look like, would you wish to implement this caching solution
for the caching of plugin content elements, all without changing much code of the wrapped extension.
This manual tries to be as descriptive as possible and explains all problems and pitfalls you have to think about
if you decide to use enetcache.

What does it do?
----------------

This extension provides an API for frontend plugins to realize caching on a plugin element level.
It's based on the caching framework included since TYPO3 4.3. This extension fills a gap between USER and USER_INT plugins
by enabling plugins to cache their own content for a given lifetime and using this cache entry to save the computing
resources if other parts of the page have to be re-rendered.

This is especially useful if pages have elements that need to be re-rendered often (like “most clicked hotlists”)
while other elements on the page have a much longer lifetime and therefore clog the processor unnecessarily, when
rendered over and over again along with the other elements. Especially with highly dynamic, heavy traffic pages this
kills dearly needed server resources unnecessarily, slowing down vital performance.

Enetcache implements a mostly automatic clearing of cache elements if records are changed in the backend. If implemented
correctly, editors will not even recognize that this extension is in use and will never need to manually clear caches.

Features
--------

- API for frontend plugins to easily get, set and drop entries in the cache engine. The usual TYPO3 page cache is handled transparently by enetcache.
- Flexible lifetime handling of cache entries.
- Cached plugin content can be re-used on different pages.
- Automagic clearing of dirty cache entries if records have been changed in the backend.
- Hooks into “Clear all cache” to enable administrators to manually clear content element caches together with the regular page cache.
- Hooks in it's main frontend class allow other extensions to manipulate the cache handling.

See also
--------

- The link section at the end of this document for further documentation.
- enetcache is pretty well documented inline. Please take a look to the source.

Thanks
------

This extension was developed by Michael Knabe and Christian Kuhn for e-net Consulting in Hamburg.
Thanks to e-net Consulting for giving us enough time to develop a clean and releasable version.