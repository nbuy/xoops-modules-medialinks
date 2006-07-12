# medialinks module SQL schema
# $Id: mysql.sql,v 1.1 2006/07/12 16:27:25 nobu Exp $

# main contents table
CREATE TABLE medialinks (
    mid		integer NOT NULL PRIMARY KEY auto_increment,
    title	varchar(128) NOT NULL default '',
    description	text NOT NULL default '',
    style	char(1) NOT NULL default 'b',  -- 'h':html, 'b':bb, 'n': none
    status      enum('W','N','X') NOT NULL default 'N', -- Wait, Norm, (X)del
    ctime	integer NOT NULL default 0,
    mtime	integer NOT NULL default 0,
    weight	integer NOT NULL default 1,
    poster	integer NOT NULL default 0,
    hits	integer NOT NULL default 0
);

# attachement media url
CREATE TABLE medialinks_attach (
    linkid	integer NOT NULL PRIMARY KEY auto_increment,
    midref	integer NOT NULL,
    name	varchar(128) NOT NULL default '',
    url		varchar(255) NOT NULL default '',
    ltype	char(1) NOT NULL default 'A', -- m:Media, a:Attachment
    weight	integer NOT NULL default 1,
    hits	integer NOT NULL default 0,
    KEY (midref)
);

# keywords atom and structure
CREATE TABLE medialinks_keys (
    keyid	integer NOT NULL PRIMARY KEY auto_increment,
    parent	integer NOT NULL default 0,
    relay	integer NOT NULL default 0,
    nodetype	int(1)  NOT NULL default 0,
    name	varchar(40) NOT NULL default '',
    weight	integer NOT NULL default 1,
    description	text
);

# relation between main contents and keywords
CREATE TABLE medialinks_relation (
    keyref	integer NOT NULL,
    midref	integer NOT NULL,
    KEY (keyref, midref)
);

# additional feilds defuntion
CREATE TABLE medialinks_fields (
    fid		int(4) NOT NULL PRIMARY KEY auto_increment,
    name	varchar(15) NOT NULL default '',
    label	varchar(40) NOT NULL default '',
    type	varchar(16) NOT NULL default '',
    def		varchar(60) NOT NULL default '',
    weight	integer NOT NULL default 1
);
