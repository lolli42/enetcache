#
# Table structure for table 'tx_enetcache_content'
#
CREATE TABLE tx_enetcache_contentcache (
	id int(11) unsigned NOT NULL auto_increment,
	identifier varchar(250) DEFAULT '' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	content mediumblob,
	lifetime int(11) unsigned DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier)
) ENGINE=InnoDB;

#
# Table structure for table 'cachingframework_cache_pages_tags'
#
CREATE TABLE tx_enetcache_contentcache_tags (
	id int(11) unsigned NOT NULL auto_increment,
	identifier varchar(128) DEFAULT '' NOT NULL,
	tag varchar(128) DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier),
	KEY cache_tag (tag)
) ENGINE=InnoDB;
