--
-- database changes in version 2.5.1
--

UPDATE gui_element SET e_closetag = '' WHERE e_element = 'img';

-- translations missing for bg,gr,nl
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Set language', 'Sprache auswählen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Set language', 'Selección del idioma');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Set language', 'Lingua');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Set language', 'Choisir une langue');


-- enlarge WFS metadata columns
ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_abstract TYPE text;
ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_title TYPE varchar(255);
ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_name TYPE varchar(255);
ALTER TABLE wfs_element ALTER COLUMN element_name TYPE varchar(255);
ALTER TABLE wfs_element ALTER COLUMN element_type TYPE varchar(255);
