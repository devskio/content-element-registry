#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
    tx_contentelementregistry_relations int(11) unsigned DEFAULT '0' NOT NULL
);


# Table structure for table 'tx_contentelementregistry_domain_model_relation'
#
CREATE TABLE tx_contentelementregistry_domain_model_relation (
	type varchar(255) DEFAULT '' NOT NULL,
	content_element int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text,
	media int(11) unsigned DEFAULT '0' NOT NULL,
    inline_relations int(11) unsigned DEFAULT '0' NOT NULL,
    self_relation int(11) unsigned DEFAULT '0' NOT NULL,
);
