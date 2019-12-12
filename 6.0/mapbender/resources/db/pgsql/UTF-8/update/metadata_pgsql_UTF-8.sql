--Things to be done for merging with geoportal.rlp***
--Adoption of the group table to allow contact information for groups
alter table mb_group add column mb_group_title character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_ext_id bigint;
alter table mb_group add column mb_group_address character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_postcode character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_city character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_stateorprovince character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_country character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_voicetelephone character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_facsimiletelephone character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_email character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_logo_path text NOT NULL DEFAULT ''::character varying;
alter table mb_user_mb_group add column mb_user_mb_group_type integer;

--Adoption for adminsitration of conformities in the mapbender database***
--searchable
ALTER TABLE layer ADD COLUMN layer_searchable integer;
ALTER TABLE layer ALTER COLUMN layer_searchable SET STORAGE PLAIN;
ALTER TABLE layer ALTER COLUMN layer_searchable SET DEFAULT 1;

-- Column: featuretype_searchable

-- ALTER TABLE wfs_featuretype DROP COLUMN featuretype_searchable;

--ALTER TABLE wfs_featuretype ADD COLUMN featuretype_searchable integer;
--ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_searchable SET STORAGE PLAIN;
--ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_searchable SET DEFAULT 1;


ALTER TABLE wfs_featuretype ADD COLUMN featuretype_searchable integer;


-- Column: wms_timestamp_create
ALTER TABLE wms ADD COLUMN wms_timestamp_create integer;
ALTER TABLE wms ALTER COLUMN wms_timestamp_create SET STORAGE PLAIN;

--network accessibility
ALTER TABLE wms ADD COLUMN wms_network_access integer;
ALTER TABLE wms ALTER COLUMN wms_network_access SET STORAGE PLAIN;


-- Column: wfs_timestamp_create

-- ALTER TABLE wfs DROP COLUMN wfs_timestamp_create;
ALTER TABLE wfs ADD COLUMN wfs_timestamp_create integer;
ALTER TABLE wfs ALTER COLUMN wfs_timestamp_create SET STORAGE PLAIN;

--network accessibility
ALTER TABLE wfs ADD COLUMN wfs_network_access integer;
ALTER TABLE wfs ALTER COLUMN wfs_network_access SET STORAGE PLAIN;



--table for inspire metadata add on fields - for layer and featuretypes
-- Table: inspire_md_data
CREATE TABLE inspire_md_data
(
  data_id serial NOT NULL,
  data_time_begin integer, --timestamp
  data_time_end integer, --timestamp
  data_lineage text,
  data_spatial_res_value varchar(255), --
  data_spatial_res_type integer, --look up types like 1:equivalentScale, 2:Distance - see guidance paper for metadata
  CONSTRAINT data_id_pkey PRIMARY KEY (data_id)
);
--ALTER TABLE inspire_md_data OWNER TO "postgres";


--ok
--Conformity is a metadata information which is defined for spatial data services and spatial data sets - the information about the conformity should be generated automatically by the service registry
--mainly the available metadata information should be tested. This can be done when resitrating the service and when edit the service metadata.
-- There should be a list of available specifications (ir, or regulations) wherefor the services can be tested. This list have to be stored in the database.  


-- Table: conformity
CREATE TABLE conformity
(
  conformity_id serial NOT NULL,
  conformity_key varchar(255),
  fkey_spec_class_key character varying(255), 
  conformity_code_en character varying(255),
  conformity_code_fr character varying(255),
  conformity_code_de character varying(255),
  conformity_symbol character varying(255),
  conformity_description_de text,
  CONSTRAINT conformity_pkey PRIMARY KEY (conformity_id)
);

--ALTER TABLE conformity OWNER TO "postgres";
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','1','conformant','Konform','','','Die Ressource stimmt mit der angegebenen Spezifikation in vollem Umfang überein.');
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','2','notConformant','Nicht konform','','','Die Ressource stimmt mit der angegebenen Spezifikation nicht überein.');
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','3','notEvaluated','Nicht überprüft','','','Die Übereinstimmung ist nicht überprüft worden.');


-- Table spec_classification: 
CREATE TABLE spec_classification
(
  spec_class_id serial NOT NULL,
 -- spec_key varchar(5) NOT NULL,
  spec_class_key character varying(255) UNIQUE,
  spec_class_code_de character varying(255),
  spec_class_code_en character varying(255),
  spec_class_code_fr character varying(255),
  spec_class_description_en text,
  spec_class_description_de text,
  spec_class_description_fr text,
  spec_class_timestamp integer,
  CONSTRAINT spec_class_id_pkey PRIMARY KEY (spec_class_id)

);


INSERT INTO spec_classification (spec_class_key, spec_class_code_de, spec_class_description_de) VALUES ('inspire','INSPIRE','Klasse der Inspire Spezifikationen/Regulations');

-- specification table:
CREATE TABLE spec
(
  spec_id serial NOT NULL,
  spec_key varchar(50) NOT NULL,
  spec_code_en character varying,
  spec_code_de character varying,
  spec_code_fr character varying,
  spec_link_en character varying,
  spec_link_de character varying,
  spec_link_fr character varying,
  spec_description_en text,
  spec_description_de text,
  spec_description_fr text,
  fkey_spec_class_key character varying(255), 
  spec_timestamp integer,
  CONSTRAINT spec_id_pkey PRIMARY KEY (spec_id),
  CONSTRAINT spec_spec_class_fkey FOREIGN KEY (fkey_spec_class_key)
      REFERENCES spec_classification (spec_class_key) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE

);


INSERT INTO spec (spec_key, spec_code_en, spec_link_en, spec_description_en, fkey_spec_class_key, spec_timestamp) VALUES ('ir_interop','INSPIRE Implementing rules laying down technical arrangements','http://www.geoportal.rlp.de/','INSPIRE Implementing rules laying down technical arrangements for the interoperability and harmonisation of orthoimagery','inspire',extract(epoch FROM (to_timestamp('2011-05-15','YYYY-MM-DD'))));

--conformity relation table:
-- Table: conformity_relation
CREATE TABLE conformity_relation
(
  relation_id serial NOT NULL,
  fkey_wms_id integer,
  fkey_wfs_id integer,
  fkey_inspire_md_id integer,
  fkey_conformity_id integer,
  fkey_spec_id integer,
  CONSTRAINT relation_id_pkey PRIMARY KEY (relation_id),
  CONSTRAINT conformity_relation_wms_id_fkey FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_wfs_id_fkey FOREIGN KEY (fkey_wfs_id)
      REFERENCES wfs (wfs_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_inspire_md_fkey FOREIGN KEY (fkey_inspire_md_id)
      REFERENCES inspire_md_data (data_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_conformity_fkey FOREIGN KEY (fkey_conformity_id)
      REFERENCES conformity (conformity_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_spec_fkey FOREIGN KEY (fkey_spec_id)
      REFERENCES spec (spec_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: custom_category
-- Table: custom_category
CREATE TABLE custom_category
(
  custom_category_id serial NOT NULL,
  custom_category_key varchar(5) NOT NULL,
  custom_category_code_en character varying(255),
  custom_category_code_de character varying(255),
  custom_category_code_fr character varying(255),
  custom_category_symbol character varying(255),
  custom_category_description_de text,
  CONSTRAINT custom_category_pkey PRIMARY KEY (custom_category_id)
);


INSERT INTO custom_category (custom_category_key, custom_category_code_en, custom_category_code_de, custom_category_code_fr, custom_category_symbol, custom_category_description_de) VALUES ('dc1','dummy category','Dummy Kategorie','','','Demo Kategorie zur Klassifizierung von Mapbender Registry Inhalten');


-- Table: layer_custom_category
CREATE TABLE layer_custom_category
(
  fkey_layer_id integer NOT NULL,
  fkey_custom_category_id integer NOT NULL,
  CONSTRAINT layer_custom_category_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: wfs_featuretype_custom_category
CREATE TABLE wfs_featuretype_custom_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_custom_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_custom_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_custom_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  custom_cat_string  TEXT;
  custom_cat_record  RECORD;

BEGIN
custom_cat_string := '';

FOR custom_cat_record IN SELECT layer_custom_category.fkey_custom_category_id from layer_custom_category WHERE layer_custom_category.fkey_layer_id=$1  LOOP
custom_cat_string := custom_cat_string || '{' ||custom_cat_record.fkey_custom_category_id || '}';
END LOOP ;
  
RETURN custom_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_custom_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
  custom_cat_string  TEXT;
  custom_cat_record  RECORD;

BEGIN
custom_cat_string := '';

FOR custom_cat_record IN SELECT wfs_featuretype_custom_category.fkey_custom_category_id from wfs_featuretype_custom_category WHERE wfs_featuretype_custom_category.fkey_featuretype_id=$1  LOOP
custom_cat_string := custom_cat_string || '{' ||custom_cat_record.fkey_custom_category_id || '}';
END LOOP ;
  
RETURN custom_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

-- Table: inspire_category
-- Table: inspire_category
CREATE TABLE inspire_category
(
  inspire_category_id serial NOT NULL,
  inspire_category_key varchar(5) NOT NULL,
  inspire_category_code_en character varying(255),
  inspire_category_code_de character varying(255),
  inspire_category_code_fr character varying(255),
  inspire_category_symbol character varying(255),
  inspire_category_description_de text,
  CONSTRAINT inspire_category_pkey PRIMARY KEY (inspire_category_id)
);


INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.1','Coordinate reference systems','Koordinatenreferenzsysteme','','','Systeme zur eindeutigen räumlichen Referenzierung von Geodaten anhand eines Koordinatensatzes (x, y, z) und/oder Angaben zu Breite, Länge und Höhe auf der Grundlage eines geodätischen horizontalen und vertikalen Datums.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.2','Geographical grid systems','Geografische Gittersysteme','','','Harmonisiertes Gittersystem mit Mehrfachauflösung, gemeinsamem Ursprungspunkt und standardisierter Lokalisierung und Größe der Gitterzellen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.3','Geographical names','Geografische Bezeichnungen','','','Namen von Gebieten, Regionen, Orten, Großstädten, Vororten, Städten oder Siedlungen sowie jedes geografische oder topografische Merkmal von öffentlichem oder historischem Interesse.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.4','Administrative units','Verwaltungseinheiten','','','Lokale, regionale und nationale Verwaltungseinheiten, die die Gebiete abgrenzen, in denen die Mitgliedstaaten Hoheitsbefugnisse haben und/oder ausüben und die durch Verwaltungsgrenzen voneinander getrennt sind.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.5','Addresses','Adressen','','','Lokalisierung von Grundstücken anhand von Adressdaten, in der Regel Straßenname, Hausnummer und Postleitzahl.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.6','Cadastral parcels','Flurstücke/Grundstücke (Katasterparzellen)','','','Gebiete, die anhand des Grundbuchs oder gleichwertiger Verzeichnisse bestimmt werden. ');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.7','Transport networks','Verkehrsnetze','','','Verkehrsnetze und zugehörige Infrastruktureinrichtungen für Straßen-, Schienen- und Luftverkehr sowie Schifffahrt. Umfasst auch die Verbindungen zwischen den verschiedenen Netzen. Umfasst auch das transeuropäische Verkehrsnetz im Sinne der Entscheidung Nr. 1692/96/EG des Europäischen Parlaments und des Rates vom 23. Juli 1996 über gemeinschaftliche Leitlinien für den Aufbau eines transeuropäischen Verkehrsnetzes und künftiger Überarbeitungen dieser Entscheidung. ');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.8','Hydrography','Gewässernetz','','','Elemente des Gewässernetzes, einschließlich Meeresgebieten und allen sonstigen Wasserkörpern und hiermit verbundenen Teilsystemen, darunter Einzugsgebiete und Teileinzugsgebiete. Gegebenenfalls gemäß den Definitionen der Richtlinie 2000/60/EG des Europäischen Parlaments und des Rates vom 23. Oktober 2000 zur Schaffung eines Ordnungsrahmens für Maßnahmen der Gemeinschaft im Bereich der Wasserpolitik und in Form von Netzen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.9','Protected sites','Schutzgebiete','','','Gebiete, die im Rahmen des internationalen und des gemeinschaftlichen Rechts sowie des Rechts der Mitgliedstaaten ausgewiesen sind oder verwaltet werden, um spezifische Erhaltungsziele zu erreichen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.1','Elevation','Höhe','','','Digitale Höhenmodelle für Land-, Eis- und Meeresflächen. Dazu gehören Geländemodell, Tiefenmessung und Küstenlinie.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.2','Land cover','Bodenbedeckung','','','Physische und biologische Bedeckung der Erdoberfläche, einschließlich künstlicher Flächen, landwirtschaftlicher Flächen, Wäldern, natürlicher (naturnaher) Gebiete, Feuchtgebieten und Wasserkörpern.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.3','Orthoimagery','Orthofotografie','','','Georeferenzierte Bilddaten der Erdoberfläche von satelliten- oder luftfahrzeuggestützten Sensoren.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.4','Geology','Geologie','','','Geologische Beschreibung anhand von Zusammensetzung und Struktur. Dies umfasst auch Grundgestein, Grundwasserleiter und Geomorphologie.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.1','Statistical units','Statistische Einheiten','','','Einheiten für die Verbreitung oder Verwendung statistischer Daten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.2','Buildings','Gebäude','','','Geografischer Standort von Gebäuden.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.3','Soil','Boden','','','Beschreibung von Boden und Unterboden anhand von Tiefe, Textur, Struktur und Gehalt an Teilchen sowie organischem Material, Steinigkeit, Erosion, gegebenenfalls durchschnittliches Gefälle und erwartete Wasserspeicherkapazität.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.4','Land use','Bodennutzung','','','Beschreibung von Gebieten anhand ihrer derzeitigen und geplanten künftigen Funktion oder ihres sozioökonomischen Zwecks (z. B. Wohn-, Industrie- oder Gewerbegebiete, land- oder forstwirtschaftliche Flächen, Freizeitgebiete).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.5','Human health and safety','Gesundheit und Sicherheit','','','Geografische Verteilung verstärkt auftretender pathologischer Befunde (Allergien, Krebserkrankungen, Erkrankungen der Atemwege usw.), Informationen über Auswirkungen auf die Gesundheit (Biomarker, Rückgang der Fruchtbarkeit, Epidemien) oder auf das Wohlbefinden (Ermüdung, Stress usw.) der Menschen in unmittelbarem Zusammenhang mit der Umweltqualität (Luftverschmutzung, Chemikalien, Abbau der Ozonschicht, Lärm usw.) oder in mittelbarem Zusammenhang mit der Umweltqualität (Nahrung, genetisch veränderte Organismen usw.).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.6','Utility and governmental services','Versorgungswirtschaft und staatliche Dienste','','','Versorgungseinrichtungen wie Abwasser- und Abfallentsorgung, Energieversorgung und Wasserversorgung; staatliche Verwaltungs- und Sozialdienste wie öffentliche Verwaltung, Katastrophenschutz, Schulen und Krankenhäuser.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.7','Environmental monitoring facilities','Umweltüberwachung','','','Standort und Betrieb von Umweltüberwachungseinrichtungen einschließlich Beobachtung und Messung von Schadstoffen, des Zustands von Umweltmedien und anderen Parametern des Ökosystems (Artenvielfalt, ökologischer Zustand der Vegetation usw.) durch oder im Auftrag von öffentlichen Behörden.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.8','Production and industrial facilities','Produktions- und Industrieanlagen','','','Standorte für industrielle Produktion, einschließlich durch die Richtlinie 96/61/EG des Rates vom 24. September 1996 über die integrierte Vermeidung und Verminderung der Umweltverschmutzung erfasste Anlagen und Einrichtungen zur Wasserentnahme sowie Bergbau- und Lagerstandorte.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.9','Agricultural and aquaculture facilities','Landwirtschaftliche Anlagen und Aquakulturanlagen','','','Landwirtschaftliche Anlagen und Produktionsstätten (einschließlich Bewässerungssystemen, Gewächshäusern und Ställen).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.10','Population distribution — demography','Verteilung der Bevölkerung — Demografie','','','Geografische Verteilung der Bevölkerung, einschließlich Bevölkerungsmerkmalen und Tätigkeitsebenen, zusammengefasst nach Gitter, Region, Verwaltungseinheit oder sonstigen analytischen Einheiten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.11','Area management/restriction/regulation zones and reporting units','Bewirtschaftungsgebiete/Schutzgebiete/geregelte Gebiete und Berichterstattungseinheiten','','','Auf internationaler, europäischer, nationaler, regionaler und lokaler Ebene bewirtschaftete, geregelte oder zu Zwecken der Berichterstattung herangezogene Gebiete. Dazu zählen Deponien, Trinkwasserschutzgebiete, nitratempfindliche Gebiete, geregelte Fahrwasser auf See oder auf großen Binnengewässern, Gebiete für die Abfallverklappung, Lärmschutzgebiete, für Exploration und Bergbau ausgewiesene Gebiete, Flussgebietseinheiten, entsprechende Berichterstattungseinheiten und Gebiete des Küstenzonenmanagements.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.12','Natural risk zones','Gebiete mit naturbedingten Risiken','','','Gefährdete Gebiete, eingestuft nach naturbedingten Risiken (sämtliche atmosphärischen, hydrologischen, seismischen, vulkanischen Phänomene sowie Naturfeuer, die aufgrund ihres örtlichen Auftretens sowie ihrer Schwere und Häufigkeit signifikante Auswirkungen auf die Gesellschaft haben können), z. B. Überschwemmungen, Erdrutsche und Bodensenkungen, Lawinen, Waldbrände, Erdbeben oder Vulkanausbrüche.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.13','Atmospheric conditions','Atmosphärische Bedingungen','','','Physikalische Bedingungen in der Atmosphäre. Dazu zählen Geodaten auf der Grundlage von Messungen, Modellen oder einer Kombination aus beiden sowie Angabe der Messstandorte.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.14','Meteorological geographical features','Meteorologisch-geografische Kennwerte','','','Witterungsbedingungen und deren Messung; Niederschlag, Temperatur, Gesamtverdunstung (Evapotranspiration), Windgeschwindigkeit und Windrichtung.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.15','Oceanographic geographical features','Ozeanografisch-geografische Kennwerte','','','Physikalische Bedingungen der Ozeane (Strömungsverhältnisse, Salinität, Wellenhöhe usw.).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.16','Sea regions','Meeresregionen','','','Physikalische Bedingungen von Meeren und salzhaltigen Gewässern, aufgeteilt nach Regionen und Teilregionen mit gemeinsamen Merkmalen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.17','Bio-geographical regions','Biogeografische Regionen','','','Gebiete mit relativ homogenen ökologischen Bedingungen und gemeinsamen Merkmalen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.18','Habitats and biotopes','Lebensräume und Biotope','','','Geografische Gebiete mit spezifischen ökologischen Bedingungen, Prozessen, Strukturen und (lebensunterstützenden) Funktionen als physische Grundlage für dort lebende Organismen. Dies umfasst auch durch geografische, abiotische und biotische Merkmale gekennzeichnete natürliche oder naturnahe terrestrische und aquatische Gebiete.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.19','Species distribution','Verteilung der Arten','','','Geografische Verteilung des Auftretens von Tier- und Pflanzenarten, zusammengefasst in Gittern, Region, Verwaltungseinheit oder sonstigen analytischen Einheiten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.20','Energy resources','Energiequellen','','','Energiequellen wie Kohlenwasserstoffe, Wasserkraft, Bioenergie, Sonnen- und Windenergie usw., gegebenenfalls mit Tiefen- bzw. Höhenangaben zur Ausdehnung der Energiequelle.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.21','Mineral resources','Mineralische Bodenschätze','','','Mineralische Bodenschätze wie Metallerze, Industrieminerale usw., gegebenenfalls mit Tiefen- bzw. Höhenangaben zur Ausdehnung der Bodenschätze.');


-- Table: layer_inspire_category
CREATE TABLE layer_inspire_category
(
  fkey_layer_id integer NOT NULL,
  fkey_inspire_category_id integer NOT NULL,
  CONSTRAINT layer_inspire_category_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_inspire_category_fkey_inspire_category_id_fkey FOREIGN KEY (fkey_inspire_category_id)
      REFERENCES inspire_category (inspire_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: wfs_featuretype_inspire_category
CREATE TABLE wfs_featuretype_inspire_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_inspire_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_inspire_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_inspire_category_fkey_inspire_category_id_fkey FOREIGN KEY (fkey_inspire_category_id)
      REFERENCES inspire_category (inspire_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_inspire_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  inspire_cat_string  TEXT;
  inspire_cat_record  RECORD;

BEGIN
inspire_cat_string := '';

FOR inspire_cat_record IN SELECT layer_inspire_category.fkey_inspire_category_id from layer_inspire_category WHERE layer_inspire_category.fkey_layer_id=$1  LOOP
inspire_cat_string := inspire_cat_string || '{' ||inspire_cat_record.fkey_inspire_category_id || '}';
END LOOP ;
  
RETURN inspire_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_inspire_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
  inspire_cat_string  TEXT;
  inspire_cat_record  RECORD;

BEGIN
inspire_cat_string := '';

FOR inspire_cat_record IN SELECT wfs_featuretype_inspire_category.fkey_inspire_category_id from wfs_featuretype_inspire_category WHERE wfs_featuretype_inspire_category.fkey_featuretype_id=$1  LOOP
inspire_cat_string := inspire_cat_string || '{' ||inspire_cat_record.fkey_inspire_category_id || '}';
END LOOP ;
  
RETURN inspire_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


-- Table: wfs_featuretype_md_topic_category
CREATE TABLE wfs_featuretype_md_topic_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_md_topic_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_md_topic_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_md_topic_category_fkey_md_topic_cat_id_fkey FOREIGN KEY (fkey_md_topic_category_id)
      REFERENCES md_topic_category (md_topic_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE OR REPLACE FUNCTION f_collect_topic_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  topic_cat_string  TEXT;
  topic_cat_record  RECORD;

BEGIN
topic_cat_string := '';

FOR topic_cat_record IN SELECT layer_md_topic_category.fkey_md_topic_category_id from layer_md_topic_category WHERE layer_md_topic_category.fkey_layer_id=$1  LOOP
topic_cat_string := topic_cat_string || '{' ||topic_cat_record.fkey_md_topic_category_id || '}';
END LOOP ;
  
RETURN topic_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_topic_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
  topic_cat_string  TEXT;
  topic_cat_record  RECORD;

BEGIN
topic_cat_string := '';

FOR topic_cat_record IN SELECT wfs_featuretype_md_topic_category.fkey_md_topic_category_id from wfs_featuretype_md_topic_category WHERE wfs_featuretype_md_topic_category.fkey_featuretype_id=$1  LOOP
topic_cat_string := topic_cat_string || '{' ||topic_cat_record.fkey_md_topic_category_id || '}';
END LOOP ;
  
RETURN topic_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

--generate the group for the decentral registrating institutions
-- View: registrating_groups
CREATE OR REPLACE VIEW registrating_groups AS 
 SELECT f.fkey_mb_group_id, f.fkey_mb_user_id
   FROM mb_user_mb_group f, mb_user_mb_group s
  WHERE f.mb_user_mb_group_type = 1 AND s.fkey_mb_group_id = 36 AND f.fkey_mb_user_id = s.fkey_mb_user_id
  ORDER BY f.fkey_mb_group_id, f.fkey_mb_user_id;

-- Function: f_getwfs_tou(integer)
CREATE OR REPLACE FUNCTION f_getwfs_tou(integer)
  RETURNS integer AS
$BODY$
DECLARE
   wfs_tou int4;
BEGIN
wfs_tou := fkey_termsofuse_id from wfs_termsofuse where wfs_termsofuse.fkey_wfs_id=$1; 
RETURN wfs_tou;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_getwms_tou(integer)
CREATE OR REPLACE FUNCTION f_getwms_tou(integer)
  RETURNS integer AS
$BODY$
DECLARE
   wms_tou int4;
BEGIN
wms_tou := fkey_termsofuse_id from wms_termsofuse where wms_termsofuse.fkey_wms_id=$1; 
RETURN wms_tou;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_collect_epsg(integer)
CREATE OR REPLACE FUNCTION f_collect_epsg(integer)
  RETURNS text AS
$BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  epsg_string  TEXT;
  epsg_record  RECORD;

BEGIN
epsg_string := '';

FOR epsg_record IN SELECT layer_epsg.epsg from layer_epsg WHERE layer_epsg.fkey_layer_id=$1  LOOP
epsg_string := epsg_string ||  epsg_record.epsg || ';';
END LOOP ;
  
RETURN epsg_string;

    --CASE
      --WHEN LEN(epsg) > 0 THEN LEFT(epsg, LEN(epsg) - 1)
     -- ELSE epsg
    

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

-- Function: f_layer_load_count(integer)
CREATE OR REPLACE FUNCTION f_layer_load_count(integer)
  RETURNS integer AS
$BODY$
DECLARE
   layer_rel int4;
BEGIN
layer_rel := load_count from layer_load_count where layer_load_count.fkey_layer_id=$1; 
RETURN layer_rel;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_collect_searchtext(integer, integer)
CREATE OR REPLACE FUNCTION f_collect_searchtext(integer, integer)
  RETURNS text AS
$BODY$DECLARE
    p_wms_id ALIAS FOR $1;
    p_layer_id ALIAS FOR $2;
    
    r_keywords RECORD;
    l_result TEXT;
BEGIN
    l_result := '';
    l_result := l_result || (SELECT COALESCE(wms_title, '') || ' ' || COALESCE(wms_abstract, '') FROM wms WHERE wms_id = p_wms_id);
    l_result := l_result || (SELECT COALESCE(layer_name, '')|| ' ' || COALESCE(layer_title, '')  || ' ' || COALESCE(layer_abstract, '') FROM layer WHERE layer_id = p_layer_id);
    FOR r_keywords IN SELECT DISTINCT keyword FROM
        (SELECT keyword FROM layer_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id AND L.fkey_layer_id = p_layer_id)
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
    END LOOP;
    FOR r_keywords IN SELECT DISTINCT md_topic_category_code_de FROM
        (SELECT md_topic_category_code_de FROM md_topic_category T JOIN layer_md_topic_category C ON (C.fkey_md_topic_category_id = T.md_topic_category_id AND C.fkey_layer_id = p_layer_id)
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.md_topic_category_code_de, '');
    END LOOP;
   l_result := UPPER(l_result);
   l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ä','AE'),'ü','UE'),'ö','OE');

    RETURN l_result;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

-- Function: f_collect_searchtext_wfs(integer, integer)
-- DROP FUNCTION f_collect_searchtext_wfs(integer, integer)
CREATE OR REPLACE FUNCTION f_collect_searchtext_wfs(integer, integer)
  RETURNS text AS
$BODY$
DECLARE
   p_wfs_id ALIAS FOR $1;
   p_featuretype_id ALIAS FOR $2;
     r_keywords RECORD;
   l_result TEXT;
BEGIN
   l_result := '';
   l_result := l_result || (SELECT COALESCE(wfs_title, '') || ' ' || COALESCE(wfs_abstract, '') FROM wfs WHERE wfs_id = p_wfs_id);
   l_result := l_result || (SELECT COALESCE(featuretype_name, '')|| ' ' || COALESCE(featuretype_title, '')  || ' ' || COALESCE(featuretype_abstract, '') FROM wfs_featuretype WHERE featuretype_id = p_featuretype_id);
   FOR r_keywords IN SELECT DISTINCT keyword FROM
       (SELECT keyword FROM wfs_featuretype_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id AND L.fkey_featuretype_id = p_featuretype_id)
       ) AS __keywords__ LOOP
       l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
   END LOOP;
  l_result := UPPER(l_result);
  l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ü','UE'),'ö','OE'),'ä','AE');

   RETURN l_result;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_getwfsmodultype(integer)
CREATE OR REPLACE FUNCTION f_getwfsmodultype(integer)
  RETURNS integer AS
$BODY$
DECLARE
    i_search INT4;
BEGIN
i_search := count(*) from wfs_conf, wfs_conf_element where wfs_conf.wfs_conf_id=$1 and wfs_conf.wfs_conf_id=wfs_conf_element.fkey_wfs_conf_id and f_search=1;
IF i_search > 0 THEN 
RETURN 1;
else
RETURN 0;
END IF;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;


-- View: search_wms_view
CREATE OR REPLACE VIEW search_wms_view AS 
 SELECT DISTINCT ON (wms_unref.layer_id) wms_unref.wms_id, wms_unref.availability, wms_unref.status, wms_unref.wms_title, wms_unref.wms_abstract, wms_unref.stateorprovince, wms_unref.country, wms_unref.accessconstraints, wms_unref.termsofuse, wms_unref.wms_owner, wms_unref.layer_id, wms_unref.epsg, wms_unref.layer_title, wms_unref.layer_abstract, wms_unref.layer_name, wms_unref.layer_parent, wms_unref.layer_pos, wms_unref.layer_queryable, wms_unref.load_count, wms_unref.searchtext, wms_unref.wms_timestamp, wms_unref.department, wms_unref.user_mb_group_name, f_collect_custom_cat_layer(wms_unref.layer_id) AS md_custom_cats, f_collect_inspire_cat_layer(wms_unref.layer_id) AS md_inspire_cats, f_collect_topic_cat_layer(wms_unref.layer_id) AS md_topic_cats, geometryfromtext(((((((((((((((((((('POLYGON(('::text || layer_epsg.minx::text) || ' '::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.minx::text) || ' '::text) || layer_epsg.maxy::text) || ','::text) || layer_epsg.maxx::text) || ' '::text) || layer_epsg.maxy::text) || ','::text) || layer_epsg.maxx::text) || ' '::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.minx::text) || ' '::text) || layer_epsg.miny::text) || '))'::text, 4326) AS the_geom, (((((layer_epsg.minx::text || ','::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.maxx::text) || ','::text) || layer_epsg.maxy::text AS bbox, wms_unref.wms_proxylog, wms_unref.wms_network_access, wms_unref.wms_pricevolume
   FROM ( SELECT wms_uncat.wms_id, wms_uncat.availability, wms_uncat.status, wms_uncat.wms_title, wms_uncat.wms_abstract, wms_uncat.stateorprovince, wms_uncat.country, wms_uncat.accessconstraints, wms_uncat.termsofuse, wms_uncat.wms_owner, wms_uncat.layer_id, wms_uncat.epsg, wms_uncat.layer_title, wms_uncat.layer_abstract, wms_uncat.layer_name, wms_uncat.layer_parent, wms_uncat.layer_pos, wms_uncat.layer_queryable, wms_uncat.load_count, wms_uncat.searchtext, wms_uncat.wms_timestamp, wms_uncat.department, wms_uncat.user_mb_group_name, wms_uncat.wms_proxylog, wms_uncat.wms_network_access, wms_uncat.wms_pricevolume
           FROM ( SELECT wms_dep.wms_id, wms_dep.availability, wms_dep.status, wms_dep.wms_title, wms_dep.wms_abstract, wms_dep.stateorprovince, wms_dep.country, wms_dep.accessconstraints, wms_dep.termsofuse, wms_dep.wms_owner, layer.layer_id, f_collect_epsg(layer.layer_id) AS epsg, layer.layer_title, layer.layer_abstract, layer.layer_name, layer.layer_parent, layer.layer_pos, layer.layer_queryable, f_layer_load_count(layer.layer_id) AS load_count, f_collect_searchtext(wms_dep.wms_id, layer.layer_id) AS searchtext, wms_dep.wms_timestamp, wms_dep.department, wms_dep.user_mb_group_name, wms_dep.wms_proxylog, wms_dep.wms_network_access, wms_dep.wms_pricevolume
                   FROM ( SELECT wms.wms_id, wms.wms_title, wms.wms_abstract, wms.stateorprovince, wms.country, mb_wms_availability.availability, mb_wms_availability.last_status AS status, wms.accessconstraints, f_getwms_tou(wms.wms_id) AS termsofuse, wms.wms_timestamp, wms.wms_owner, wms.wms_proxylog, wms.wms_network_access, wms.wms_pricevolume, user_dep.fkey_mb_group_id AS department, user_dep.fkey_mb_group_id, user_dep.fkey_mb_group_id AS wms_department, user_dep.fkey_mb_group_id AS user_mb_group_name
                           FROM registrating_groups user_dep, wms, mb_wms_availability
                          WHERE wms.wms_owner = user_dep.fkey_mb_user_id AND wms.wms_id = mb_wms_availability.fkey_wms_id) wms_dep, layer
                  WHERE layer.fkey_wms_id = wms_dep.wms_id AND layer.layer_searchable = 1) wms_uncat) wms_unref, layer_epsg
  WHERE layer_epsg.epsg::text = 'EPSG:4326'::text AND wms_unref.layer_id = layer_epsg.fkey_layer_id
  ORDER BY wms_unref.layer_id;

-- View: search_wfs_view
CREATE OR REPLACE VIEW search_wfs_view AS 
 SELECT wfs_dep.wfs_id, wfs_dep.wfs_title, wfs_dep.wfs_abstract, wfs_dep.administrativearea, wfs_dep.country, wfs_dep.accessconstraints, wfs_dep.termsofuse, wfs_dep.wfs_owner, wfs_featuretype.featuretype_id, wfs_featuretype.featuretype_srs, wfs_featuretype.featuretype_title, wfs_featuretype.featuretype_abstract, f_collect_searchtext_wfs(wfs_dep.wfs_id, wfs_featuretype.featuretype_id) AS searchtext, wfs_element.element_type, wfs_conf.wfs_conf_id, wfs_conf.wfs_conf_abstract, wfs_conf.wfs_conf_description, f_getwfsmodultype(wfs_conf.wfs_conf_id) AS modultype, wfs_dep.wfs_timestamp, wfs_dep.department, wfs_dep.mb_group_name
   FROM ( SELECT wfs.wfs_id, wfs.wfs_title, wfs.wfs_abstract, wfs.administrativearea, wfs.country, wfs.accessconstraints, f_getwfs_tou(wfs.wfs_id) AS termsofuse, wfs.wfs_timestamp, wfs.wfs_owner, user_dep.mb_group_id AS department, user_dep.mb_group_name
           FROM ( SELECT registrating_groups.fkey_mb_user_id AS mb_user_id, mb_group.mb_group_id, mb_group.mb_group_name
                   FROM registrating_groups, mb_group
                  WHERE registrating_groups.fkey_mb_group_id = mb_group.mb_group_id) user_dep, wfs
          WHERE user_dep.mb_user_id = wfs.wfs_owner) wfs_dep, wfs_featuretype, wfs_element, wfs_conf
  WHERE wfs_featuretype.fkey_wfs_id = wfs_dep.wfs_id AND wfs_featuretype.featuretype_searchable = 1 AND wfs_element.element_type::text ~~ '%Type'::text AND wfs_featuretype.featuretype_id = wfs_element.fkey_featuretype_id AND wfs_featuretype.featuretype_id = wfs_conf.fkey_featuretype_id
  ORDER BY wfs_featuretype.featuretype_id;

--metadata_pgsql.ALTER TABLE search_wfs_view OWNER TO postgres;







