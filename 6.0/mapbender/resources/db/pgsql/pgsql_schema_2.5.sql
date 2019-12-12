--
-- PostgreSQL database dump Mapbender 2.5
--

-- check if plpgsql exists; if yes, install it.
CREATE OR REPLACE FUNCTION public.create_plpgsql_language ()
        RETURNS TEXT
        AS $$
            CREATE LANGUAGE plpgsql;
            SELECT 'language plpgsql created'::TEXT;
        $$
LANGUAGE 'sql';

SELECT CASE WHEN
              (SELECT true::BOOLEAN
                 FROM pg_language
                WHERE lanname='plpgsql')
            THEN
              (SELECT 'language already installed'::TEXT)
            ELSE
              (SELECT public.create_plpgsql_language())
            END;

DROP FUNCTION public.create_plpgsql_language ();

-- function gettext for i18n (requires plpgsql)
CREATE FUNCTION gettext(locale_arg text, string text) RETURNS character varying
    AS $$
 DECLARE
    msgstr varchar(512);
    trl RECORD;
 BEGIN
    -- RAISE NOTICE '>%<', locale_arg;

    SELECT INTO trl * FROM translations
    WHERE trim(from locale) = trim(from locale_arg) AND msgid = string;
    -- we return the original string, if no translation is found.
    -- this is consistent with gettext's behaviour
    IF NOT FOUND THEN
        RETURN string;
    ELSE
        RETURN trl.msgstr;
    END IF; 
 END;
 $$
    LANGUAGE plpgsql;



CREATE TABLE gui (
    gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_name character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_description character varying(255) DEFAULT ''::character varying NOT NULL,
    gui_public integer DEFAULT 1 NOT NULL
);


--
-- TOC entry 1254 (class 1259 OID 5357877)
-- Dependencies: 1631 1632 1633 4
-- Name: gui_element; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_element (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    e_id character varying(50) DEFAULT ''::character varying NOT NULL,
    e_pos integer,
    e_public integer,
    e_comment text,
    e_title character varying(255),
    e_element character varying(255) DEFAULT ''::character varying NOT NULL,
    e_src character varying(255),
    e_attributes text,
    e_left integer,
    e_top integer,
    e_width integer,
    e_height integer,
    e_z_index integer,
    e_more_styles text,
    e_content text,
    e_closetag character varying(255),
    e_js_file character varying(50),
    e_mb_mod character varying(50),
    e_target character varying(50),
    e_requires character varying(50),
    e_url character varying(255)
);


--
-- TOC entry 1255 (class 1259 OID 5357885)
-- Dependencies: 1634 1635 1636 4
-- Name: gui_element_vars; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_element_vars (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_e_id character varying(50) DEFAULT ''::character varying NOT NULL,
    var_name character varying(50) DEFAULT ''::character varying NOT NULL,
    var_value text,
    context text,
    var_type character varying(50)
);


--
-- TOC entry 1305 (class 1259 OID 5358417)
-- Dependencies: 4
-- Name: gui_kml; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_kml (
    kml_id serial NOT NULL,
    fkey_mb_user_id integer NOT NULL,
    fkey_gui_id character varying(50) NOT NULL,
    kml_doc text NOT NULL,
    kml_name character varying(64),
    kml_description text,
    kml_timestamp integer NOT NULL
);


--
-- TOC entry 1256 (class 1259 OID 5357893)
-- Dependencies: 1637 1638 1639 1640 1641 1642 1643 1644 1645 1646 4
-- Name: gui_layer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_layer (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_layer_id integer DEFAULT 0 NOT NULL,
    gui_layer_wms_id integer DEFAULT 0,
    gui_layer_status integer DEFAULT 1,
    gui_layer_selectable integer DEFAULT 1,
    gui_layer_visible integer DEFAULT 1,
    gui_layer_queryable integer DEFAULT 0,
    gui_layer_querylayer integer DEFAULT 0,
    gui_layer_minscale integer DEFAULT 0,
    gui_layer_maxscale integer DEFAULT 0,
    gui_layer_priority integer,
    gui_layer_style character varying(50),
    gui_layer_wfs_featuretype character varying(50)
);


--
-- TOC entry 1257 (class 1259 OID 5357905)
-- Dependencies: 1647 1648 1649 4
-- Name: gui_mb_group; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_mb_group (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_mb_group_id integer DEFAULT 0 NOT NULL,
    mb_group_type character varying(50) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1258 (class 1259 OID 5357910)
-- Dependencies: 1650 1651 4
-- Name: gui_mb_user; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_mb_user (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_mb_user_id integer DEFAULT 0 NOT NULL,
    mb_user_type character varying(50)
);


--
-- TOC entry 1260 (class 1259 OID 5357916)
-- Dependencies: 1652 1654 1655 4
-- Name: gui_treegde; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_treegde (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_layer_id text,
    id serial NOT NULL,
    lft integer DEFAULT 0 NOT NULL,
    rgt integer DEFAULT 0 NOT NULL,
    my_layer_title character varying(50),
    layer text,
    wms_id text
);


--
-- TOC entry 1261 (class 1259 OID 5357925)
-- Dependencies: 1656 1657 4
-- Name: gui_wfs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_wfs (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_wfs_id integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1301 (class 1259 OID 5358383)
-- Dependencies: 1744 1745 4
-- Name: gui_wfs_conf; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_wfs_conf (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_wfs_conf_id integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1262 (class 1259 OID 5357929)
-- Dependencies: 1658 1659 1660 1661 1662 1663 1664 1665 1666 4
-- Name: gui_wms; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gui_wms (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_wms_id integer DEFAULT 0 NOT NULL,
    gui_wms_position integer DEFAULT 0 NOT NULL,
    gui_wms_mapformat character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_wms_featureinfoformat character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_wms_exceptionformat character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_wms_epsg character varying(50) DEFAULT ''::character varying NOT NULL,
    gui_wms_visible integer DEFAULT 1 NOT NULL,
    gui_wms_sldurl character varying(255) DEFAULT ''::character varying NOT NULL,
    gui_wms_opacity integer DEFAULT 100
);


--
-- TOC entry 1264 (class 1259 OID 5357945)
-- Dependencies: 4
-- Name: keyword; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE keyword (
    keyword_id serial NOT NULL,
    keyword character varying(255) NOT NULL
);


--
-- TOC entry 1266 (class 1259 OID 5357950)
-- Dependencies: 1669 1670 1671 1672 1673 1674 1675 1676 4
-- Name: layer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE layer (
    layer_id serial NOT NULL,
    fkey_wms_id integer DEFAULT 0 NOT NULL,
    layer_pos integer DEFAULT 0 NOT NULL,
    layer_parent character varying(50) DEFAULT ''::character varying NOT NULL,
    layer_name character varying(255) DEFAULT ''::character varying NOT NULL,
    layer_title character varying(255) DEFAULT ''::character varying NOT NULL,
    layer_queryable integer DEFAULT 0 NOT NULL,
    layer_minscale integer DEFAULT 0,
    layer_maxscale integer DEFAULT 0,
    layer_dataurl character varying(255),
    layer_metadataurl character varying(255),
    layer_abstract text
);


--
-- TOC entry 1267 (class 1259 OID 5357964)
-- Dependencies: 1677 1678 1679 1680 1681 1682 4
-- Name: layer_epsg; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE layer_epsg (
    fkey_layer_id integer DEFAULT 0 NOT NULL,
    epsg character varying(50) DEFAULT ''::character varying NOT NULL,
    minx double precision DEFAULT 0,
    miny double precision DEFAULT 0,
    maxx double precision DEFAULT 0,
    maxy double precision DEFAULT 0
);


--
-- TOC entry 1268 (class 1259 OID 5357972)
-- Dependencies: 4
-- Name: layer_keyword; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE layer_keyword (
    fkey_layer_id integer NOT NULL,
    fkey_keyword_id integer NOT NULL
);


--
-- TOC entry 1303 (class 1259 OID 5358411)
-- Dependencies: 4
-- Name: layer_load_count; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE layer_load_count (
    fkey_layer_id integer,
    load_count bigint
);


--
-- TOC entry 1269 (class 1259 OID 5357974)
-- Dependencies: 1683 1684 1685 4
-- Name: layer_style; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE layer_style (
    fkey_layer_id integer DEFAULT 0 NOT NULL,
    name character varying(50) DEFAULT ''::character varying NOT NULL,
    title character varying(100) DEFAULT ''::character varying NOT NULL,
    legendurl character varying(255),
    legendurlformat character varying(50)
);


--
-- TOC entry 1271 (class 1259 OID 5357981)
-- Dependencies: 1687 1688 4
-- Name: mb_group; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_group (
    mb_group_id serial NOT NULL,
    mb_group_name character varying(50) DEFAULT ''::character varying NOT NULL,
    mb_group_owner integer,
    mb_group_description character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1273 (class 1259 OID 5357988)
-- Dependencies: 1690 4
-- Name: mb_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_log (
    id serial NOT NULL,
    time_client character varying(13) DEFAULT 0,
    time_server character varying(13),
    time_readable character varying(50),
    mb_session character varying(50),
    gui character varying(50),
    module character varying(50),
    ip character varying(20),
    username character varying(50),
    userid character varying(50),
    request text
);


--
-- TOC entry 1274 (class 1259 OID 5357995)
-- Dependencies: 1691 1692 1693 1694 1695 4
-- Name: mb_monitor; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_monitor (
    upload_id character varying(255) DEFAULT ''::character varying NOT NULL,
    fkey_wms_id integer DEFAULT 0 NOT NULL,
    status integer NOT NULL,
    status_comment character varying(255) DEFAULT ''::character varying NOT NULL,
    timestamp_begin integer NOT NULL,
    timestamp_end integer NOT NULL,
    upload_url character varying(255) DEFAULT ''::character varying NOT NULL,
    updated character(1) DEFAULT ''::bpchar NOT NULL,
    image integer,
    map_url character varying(2048)
);


--
-- TOC entry 1276 (class 1259 OID 5358007)
-- Dependencies: 1697 1698 1699 1700 1701 4
-- Name: mb_user; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_user (
    mb_user_id serial NOT NULL,
    mb_user_name character varying(50) DEFAULT ''::character varying NOT NULL,
    mb_user_password character varying(50) DEFAULT ''::character varying NOT NULL,
    mb_user_owner integer DEFAULT 0 NOT NULL,
    mb_user_description character varying(255),
    mb_user_login_count integer DEFAULT 0 NOT NULL,
    mb_user_email character varying(50),
    mb_user_phone character varying(50),
    mb_user_department character varying(255),
    mb_user_resolution integer DEFAULT 72 NOT NULL,
    mb_user_organisation_name character varying(255),
    mb_user_position_name character varying(255),
    mb_user_phone1 character varying(255),
    mb_user_facsimile character varying(255),
    mb_user_delivery_point character varying(255),
    mb_user_city character varying(255),
    mb_user_postal_code integer,
    mb_user_country character varying(255),
    mb_user_online_resource character varying(255)
);


--
-- TOC entry 1277 (class 1259 OID 5358018)
-- Dependencies: 1702 1703 4
-- Name: mb_user_mb_group; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_user_mb_group (
    fkey_mb_user_id integer DEFAULT 0 NOT NULL,
    fkey_mb_group_id integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1278 (class 1259 OID 5358022)
-- Dependencies: 1704 1705 4
-- Name: mb_user_wmc; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mb_user_wmc (
    wmc_id character varying(20) DEFAULT ''::character varying NOT NULL,
    fkey_user_id integer DEFAULT 0 NOT NULL,
    wmc text NOT NULL,
    wmc_title character varying(50),
    wmc_timestamp integer
);


--
-- TOC entry 1280 (class 1259 OID 5358031)
-- Dependencies: 4
-- Name: md_topic_category; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE md_topic_category (
    md_topic_category_id serial NOT NULL,
    md_topic_category_code_en character varying(255),
    md_topic_category_code_de character varying(255)
);


--
-- TOC entry 1282 (class 1259 OID 5358039)
-- Dependencies: 4
-- Name: sld_user_layer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE sld_user_layer (
    sld_user_layer_id serial NOT NULL,
    fkey_mb_user_id integer NOT NULL,
    fkey_layer_id integer NOT NULL,
    fkey_gui_id character varying,
    sld_xml text,
    use_sld smallint
);


--
-- TOC entry 1284 (class 1259 OID 5358047)
-- Dependencies: 4
-- Name: translations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE translations (
    trs_id serial NOT NULL,
    locale character varying(8),
    msgid character varying(512),
    msgstr character varying(512)
);


--
-- TOC entry 1286 (class 1259 OID 5358058)
-- Dependencies: 1710 1711 1712 4
-- Name: wfs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs (
    wfs_id serial NOT NULL,
    wfs_version character varying(50) DEFAULT ''::character varying NOT NULL,
    wfs_name character varying(255),
    wfs_title character varying(255) DEFAULT ''::character varying NOT NULL,
    wfs_abstract text,
    wfs_getcapabilities character varying(255) DEFAULT ''::character varying NOT NULL,
    wfs_describefeaturetype character varying(255),
    wfs_getfeature character varying(255),
    wfs_transaction character varying(255),
    wfs_owsproxy character varying(50),
    wfs_getcapabilities_doc text,
    wfs_upload_url character varying(255),
    fees character varying(255),
    accessconstraints text,
    individualname character varying(255),
    positionname character varying(255),
    providername character varying(255),
    city character varying(255),
    deliverypoint character varying(255),
    administrativearea character varying(255),
    postalcode character varying(255),
    voice character varying(255),
    facsimile character varying(255),
    electronicmailaddress character varying(255),
    wfs_mb_getcapabilities_doc text,
    wfs_owner integer,
    wfs_timestamp integer,
    country character varying(255)
);


--
-- TOC entry 1288 (class 1259 OID 5358066)
-- Dependencies: 1714 1715 1716 1717 4
-- Name: wfs_conf; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_conf (
    wfs_conf_id serial NOT NULL,
    wfs_conf_abstract text,
    fkey_wfs_id integer DEFAULT 0 NOT NULL,
    fkey_featuretype_id integer DEFAULT 0 NOT NULL,
    g_label character varying(50),
    g_label_id character varying(50),
    g_button character varying(50),
    g_button_id character varying(50),
    g_style text,
    g_buffer double precision DEFAULT 0,
    g_res_style text,
    g_use_wzgraphics integer DEFAULT 0,
    wfs_conf_description text
);


--
-- TOC entry 1290 (class 1259 OID 5358078)
-- Dependencies: 1719 1720 1721 1722 4
-- Name: wfs_conf_element; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_conf_element (
    wfs_conf_element_id serial NOT NULL,
    fkey_wfs_conf_id integer DEFAULT 0 NOT NULL,
    f_id integer DEFAULT 0 NOT NULL,
    f_geom integer DEFAULT 0,
    f_gid integer DEFAULT 0 NOT NULL,
    f_search integer,
    f_pos integer,
    f_style_id character varying(255),
    f_toupper integer,
    f_label character varying(255),
    f_label_id character varying(50),
    f_show integer,
    f_respos integer,
    f_form_element_html text,
    f_edit integer,
    f_mandatory integer,
    f_auth_varname character varying(255),
    f_show_detail integer,
    f_operator character varying(50)
);


--
-- TOC entry 1292 (class 1259 OID 5358090)
-- Dependencies: 1723 4
-- Name: wfs_element; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_element (
    fkey_featuretype_id integer DEFAULT 0 NOT NULL,
    element_id serial NOT NULL,
    element_name character varying(50),
    element_type character varying(50)
);


--
-- TOC entry 1294 (class 1259 OID 5358096)
-- Dependencies: 1725 1727 1728 4
-- Name: wfs_featuretype; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_featuretype (
    fkey_wfs_id integer DEFAULT 0 NOT NULL,
    featuretype_id serial NOT NULL,
    featuretype_name character varying(50) DEFAULT ''::character varying NOT NULL,
    featuretype_title character varying(50),
    featuretype_srs character varying(50),
    featuretype_searchable integer DEFAULT 1,
    featuretype_abstract character varying(50)
);


--
-- TOC entry 1302 (class 1259 OID 5358399)
-- Dependencies: 4
-- Name: wfs_featuretype_keyword; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_featuretype_keyword (
    fkey_featuretype_id integer NOT NULL,
    fkey_keyword_id integer NOT NULL
);


--
-- TOC entry 1295 (class 1259 OID 5358101)
-- Dependencies: 1729 1730 1731 1732 4
-- Name: wfs_featuretype_namespace; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wfs_featuretype_namespace (
    fkey_wfs_id integer DEFAULT 0 NOT NULL,
    fkey_featuretype_id integer DEFAULT 0 NOT NULL,
    namespace character varying(255) DEFAULT ''::character varying NOT NULL,
    namespace_location character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1297 (class 1259 OID 5358112)
-- Dependencies: 1734 1735 1736 1737 1738 4
-- Name: wms; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wms (
    wms_id serial NOT NULL,
    wms_version character varying(50) DEFAULT ''::character varying NOT NULL,
    wms_title character varying(255) DEFAULT ''::character varying NOT NULL,
    wms_abstract text,
    wms_getcapabilities character varying(255) DEFAULT ''::character varying NOT NULL,
    wms_getmap character varying(255) DEFAULT ''::character varying NOT NULL,
    wms_getfeatureinfo character varying(255) DEFAULT ''::character varying NOT NULL,
    wms_getlegendurl character varying(255),
    wms_filter character varying(255),
    wms_getcapabilities_doc text,
    wms_owsproxy character varying(50),
    wms_upload_url character varying(255),
    fees character varying(255),
    accessconstraints text,
    contactperson character varying(255),
    contactposition character varying(255),
    contactorganization character varying(255),
    address character varying(255),
    city character varying(255),
    stateorprovince character varying(255),
    postcode character varying(255),
    country character varying(255),
    contactvoicetelephone character varying(255),
    contactfacsimiletelephone character varying(255),
    contactelectronicmailaddress character varying(255),
    wms_mb_getcapabilities_doc text,
    wms_owner integer,
    wms_timestamp integer,
    wms_supportsld boolean,
    wms_userlayer boolean,
    wms_userstyle boolean,
    wms_remotewfs boolean
);


--
-- TOC entry 1298 (class 1259 OID 5358123)
-- Dependencies: 1739 1740 1741 4
-- Name: wms_format; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wms_format (
    fkey_wms_id integer DEFAULT 0 NOT NULL,
    data_type character varying(50) DEFAULT ''::character varying NOT NULL,
    data_format character varying(100) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1299 (class 1259 OID 5358128)
-- Dependencies: 4
-- Name: wms_md_topic_category; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wms_md_topic_category (
    fkey_wms_id integer NOT NULL,
    fkey_md_topic_category_id integer NOT NULL
);


--
-- TOC entry 1300 (class 1259 OID 5358130)
-- Dependencies: 1742 1743 4
-- Name: wms_srs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wms_srs (
    fkey_wms_id integer DEFAULT 0 NOT NULL,
    wms_srs character varying(50) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1765 (class 2606 OID 5358135)
-- Dependencies: 1264 1264
-- Name: keyword_keyword_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY keyword
    ADD CONSTRAINT keyword_keyword_key UNIQUE (keyword);


--
-- TOC entry 1814 (class 2606 OID 5358424)
-- Dependencies: 1305 1305
-- Name: mb_gui_kml_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT mb_gui_kml_pkey PRIMARY KEY (kml_id);


--
-- TOC entry 1785 (class 2606 OID 5358137)
-- Dependencies: 1280 1280
-- Name: md_topic_category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY md_topic_category
    ADD CONSTRAINT md_topic_category_pkey PRIMARY KEY (md_topic_category_id);


--
-- TOC entry 1800 (class 2606 OID 5358139)
-- Dependencies: 1294 1294
-- Name: pk_featuretype_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs_featuretype
    ADD CONSTRAINT pk_featuretype_id PRIMARY KEY (featuretype_id);


--
-- TOC entry 1802 (class 2606 OID 5358363)
-- Dependencies: 1295 1295 1295 1295
-- Name: pk_featuretype_namespace; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs_featuretype_namespace
    ADD CONSTRAINT pk_featuretype_namespace PRIMARY KEY (fkey_wfs_id, fkey_featuretype_id, namespace);


--
-- TOC entry 1750 (class 2606 OID 5358141)
-- Dependencies: 1254 1254 1254
-- Name: pk_fkey_gui_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_element
    ADD CONSTRAINT pk_fkey_gui_id PRIMARY KEY (fkey_gui_id, e_id);


--
-- TOC entry 1752 (class 2606 OID 5358143)
-- Dependencies: 1255 1255 1255 1255
-- Name: pk_fkey_gui_id_fkey_e_id_var_name; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_element_vars
    ADD CONSTRAINT pk_fkey_gui_id_fkey_e_id_var_name PRIMARY KEY (fkey_gui_id, fkey_e_id, var_name);


--
-- TOC entry 1756 (class 2606 OID 5358145)
-- Dependencies: 1257 1257 1257
-- Name: pk_fkey_mb_group_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_mb_group
    ADD CONSTRAINT pk_fkey_mb_group_id PRIMARY KEY (fkey_mb_group_id, fkey_gui_id);


--
-- TOC entry 1758 (class 2606 OID 5358147)
-- Dependencies: 1258 1258 1258
-- Name: pk_fkey_mb_user_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_mb_user
    ADD CONSTRAINT pk_fkey_mb_user_id PRIMARY KEY (fkey_gui_id, fkey_mb_user_id);


--
-- TOC entry 1781 (class 2606 OID 5358149)
-- Dependencies: 1277 1277 1277
-- Name: pk_fkey_mb_user_mb_group_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_user_mb_group
    ADD CONSTRAINT pk_fkey_mb_user_mb_group_id PRIMARY KEY (fkey_mb_user_id, fkey_mb_group_id);


--
-- TOC entry 1760 (class 2606 OID 5358151)
-- Dependencies: 1260 1260 1260 1260 1260
-- Name: pk_fkey_treegde_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_treegde
    ADD CONSTRAINT pk_fkey_treegde_id PRIMARY KEY (fkey_gui_id, id, lft, rgt);


--
-- TOC entry 1812 (class 2606 OID 5358388)
-- Dependencies: 1301 1301 1301
-- Name: pk_fkey_wfs_conf_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT pk_fkey_wfs_conf_id PRIMARY KEY (fkey_gui_id, fkey_wfs_conf_id);


--
-- TOC entry 1773 (class 2606 OID 5358153)
-- Dependencies: 1271 1271
-- Name: pk_group_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_group
    ADD CONSTRAINT pk_group_id PRIMARY KEY (mb_group_id);


--
-- TOC entry 1748 (class 2606 OID 5358155)
-- Dependencies: 1253 1253
-- Name: pk_gui_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui
    ADD CONSTRAINT pk_gui_id PRIMARY KEY (gui_id);


--
-- TOC entry 1754 (class 2606 OID 5358347)
-- Dependencies: 1256 1256 1256
-- Name: pk_gui_layer; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_layer
    ADD CONSTRAINT pk_gui_layer PRIMARY KEY (fkey_gui_id, fkey_layer_id);


--
-- TOC entry 1762 (class 2606 OID 5358349)
-- Dependencies: 1262 1262 1262
-- Name: pk_gui_wms; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gui_wms
    ADD CONSTRAINT pk_gui_wms PRIMARY KEY (fkey_gui_id, fkey_wms_id);


--
-- TOC entry 1767 (class 2606 OID 5358157)
-- Dependencies: 1264 1264
-- Name: pk_keyword_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY keyword
    ADD CONSTRAINT pk_keyword_id PRIMARY KEY (keyword_id);


--
-- TOC entry 1769 (class 2606 OID 5358159)
-- Dependencies: 1266 1266
-- Name: pk_layer_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY layer
    ADD CONSTRAINT pk_layer_id PRIMARY KEY (layer_id);


--
-- TOC entry 1771 (class 2606 OID 5358351)
-- Dependencies: 1268 1268 1268
-- Name: pk_layer_keyword; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY layer_keyword
    ADD CONSTRAINT pk_layer_keyword PRIMARY KEY (fkey_layer_id, fkey_keyword_id);


--
-- TOC entry 1775 (class 2606 OID 5358367)
-- Dependencies: 1273 1273
-- Name: pk_mb_log; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_log
    ADD CONSTRAINT pk_mb_log PRIMARY KEY (id);


--
-- TOC entry 1777 (class 2606 OID 5358365)
-- Dependencies: 1274 1274 1274
-- Name: pk_mb_monitor; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_monitor
    ADD CONSTRAINT pk_mb_monitor PRIMARY KEY (upload_id, fkey_wms_id);


--
-- TOC entry 1779 (class 2606 OID 5358161)
-- Dependencies: 1276 1276
-- Name: pk_mb_user_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_user
    ADD CONSTRAINT pk_mb_user_id PRIMARY KEY (mb_user_id);


--
-- TOC entry 1808 (class 2606 OID 5358359)
-- Dependencies: 1299 1299 1299
-- Name: pk_md_topic_category; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wms_md_topic_category
    ADD CONSTRAINT pk_md_topic_category PRIMARY KEY (fkey_wms_id, fkey_md_topic_category_id);


--
-- TOC entry 1787 (class 2606 OID 5358369)
-- Dependencies: 1282 1282
-- Name: pk_sld_user_layer; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT pk_sld_user_layer PRIMARY KEY (sld_user_layer_id);


--
-- TOC entry 1783 (class 2606 OID 5358353)
-- Dependencies: 1278 1278
-- Name: pk_user_wmc; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY mb_user_wmc
    ADD CONSTRAINT pk_user_wmc PRIMARY KEY (wmc_id);


--
-- TOC entry 1796 (class 2606 OID 5358163)
-- Dependencies: 1290 1290
-- Name: pk_wfs_conf_element_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs_conf_element
    ADD CONSTRAINT pk_wfs_conf_element_id PRIMARY KEY (wfs_conf_element_id);


--
-- TOC entry 1794 (class 2606 OID 5358165)
-- Dependencies: 1288 1288
-- Name: pk_wfs_conf_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs_conf
    ADD CONSTRAINT pk_wfs_conf_id PRIMARY KEY (wfs_conf_id);


--
-- TOC entry 1798 (class 2606 OID 5358355)
-- Dependencies: 1292 1292 1292
-- Name: pk_wfs_element; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs_element
    ADD CONSTRAINT pk_wfs_element PRIMARY KEY (fkey_featuretype_id, element_id);


--
-- TOC entry 1792 (class 2606 OID 5358167)
-- Dependencies: 1286 1286
-- Name: pk_wfs_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wfs
    ADD CONSTRAINT pk_wfs_id PRIMARY KEY (wfs_id);


--
-- TOC entry 1806 (class 2606 OID 5358357)
-- Dependencies: 1298 1298 1298 1298
-- Name: pk_wms_format; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wms_format
    ADD CONSTRAINT pk_wms_format PRIMARY KEY (fkey_wms_id, data_type, data_format);


--
-- TOC entry 1804 (class 2606 OID 5358169)
-- Dependencies: 1297 1297
-- Name: pk_wms_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wms
    ADD CONSTRAINT pk_wms_id PRIMARY KEY (wms_id);


--
-- TOC entry 1810 (class 2606 OID 5358361)
-- Dependencies: 1300 1300 1300
-- Name: pk_wms_srs; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY wms_srs
    ADD CONSTRAINT pk_wms_srs PRIMARY KEY (fkey_wms_id, wms_srs);


--
-- TOC entry 1790 (class 2606 OID 5358054)
-- Dependencies: 1284 1284
-- Name: translations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY translations
    ADD CONSTRAINT translations_pkey PRIMARY KEY (trs_id);


--
-- TOC entry 1763 (class 1259 OID 5358170)
-- Dependencies: 1264
-- Name: ind_keyword; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ind_keyword ON keyword USING btree (keyword);


--
-- TOC entry 1788 (class 1259 OID 5358055)
-- Dependencies: 1284
-- Name: msgid_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX msgid_idx ON translations USING btree (msgid);


--
-- TOC entry 1853 (class 2606 OID 5358406)
-- Dependencies: 1799 1294 1302
-- Name: fkey_featuretype_id_fkey_keyword_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_featuretype_keyword
    ADD CONSTRAINT fkey_featuretype_id_fkey_keyword_id FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1852 (class 2606 OID 5358401)
-- Dependencies: 1302 1766 1264
-- Name: fkey_keyword_id_fkey_featuretype_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_featuretype_keyword
    ADD CONSTRAINT fkey_keyword_id_fkey_featuretype_id FOREIGN KEY (fkey_keyword_id) REFERENCES keyword(keyword_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1830 (class 2606 OID 5358171)
-- Dependencies: 1264 1268 1766
-- Name: fkey_keyword_id_fkey_layer_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY layer_keyword
    ADD CONSTRAINT fkey_keyword_id_fkey_layer_id FOREIGN KEY (fkey_keyword_id) REFERENCES keyword(keyword_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1831 (class 2606 OID 5358176)
-- Dependencies: 1266 1268 1768
-- Name: fkey_layer_id_fkey_keyword_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY layer_keyword
    ADD CONSTRAINT fkey_layer_id_fkey_keyword_id FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1834 (class 2606 OID 5358181)
-- Dependencies: 1276 1277 1778
-- Name: fkey_mb_user_mb_group_mb_use_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY mb_user_mb_group
    ADD CONSTRAINT fkey_mb_user_mb_group_mb_use_id FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1833 (class 2606 OID 5358186)
-- Dependencies: 1297 1274 1803
-- Name: fkey_monitor_wms_id_wms_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY mb_monitor
    ADD CONSTRAINT fkey_monitor_wms_id_wms_id FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1815 (class 2606 OID 5358191)
-- Dependencies: 1747 1253 1254
-- Name: gui_element_ibfk1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_element
    ADD CONSTRAINT gui_element_ibfk1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1816 (class 2606 OID 5358196)
-- Dependencies: 1254 1749 1254 1255 1255
-- Name: gui_element_vars_ibfk1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_element_vars
    ADD CONSTRAINT gui_element_vars_ibfk1 FOREIGN KEY (fkey_gui_id, fkey_e_id) REFERENCES gui_element(fkey_gui_id, e_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1854 (class 2606 OID 5358425)
-- Dependencies: 1305 1778 1276
-- Name: gui_kml_fkey_mb_user_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT gui_kml_fkey_mb_user_id FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1855 (class 2606 OID 5358430)
-- Dependencies: 1747 1253 1305
-- Name: gui_kml_id_fkey_gui_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT gui_kml_id_fkey_gui_id FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1819 (class 2606 OID 5358201)
-- Dependencies: 1747 1257 1253
-- Name: gui_mb_group_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_mb_group
    ADD CONSTRAINT gui_mb_group_ibfk_1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1820 (class 2606 OID 5358206)
-- Dependencies: 1271 1772 1257
-- Name: gui_mb_group_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_mb_group
    ADD CONSTRAINT gui_mb_group_ibfk_2 FOREIGN KEY (fkey_mb_group_id) REFERENCES mb_group(mb_group_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1821 (class 2606 OID 5358211)
-- Dependencies: 1258 1253 1747
-- Name: gui_mb_user_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_mb_user
    ADD CONSTRAINT gui_mb_user_ibfk_1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1822 (class 2606 OID 5358216)
-- Dependencies: 1778 1258 1276
-- Name: gui_mb_user_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_mb_user
    ADD CONSTRAINT gui_mb_user_ibfk_2 FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1823 (class 2606 OID 5358221)
-- Dependencies: 1253 1747 1260
-- Name: gui_treegde_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_treegde
    ADD CONSTRAINT gui_treegde_ibfk_1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1850 (class 2606 OID 5358389)
-- Dependencies: 1253 1301 1747
-- Name: gui_wfs_conf_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT gui_wfs_conf_ibfk_1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1851 (class 2606 OID 5358394)
-- Dependencies: 1301 1793 1288
-- Name: gui_wfs_conf_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT gui_wfs_conf_ibfk_2 FOREIGN KEY (fkey_wfs_conf_id) REFERENCES wfs_conf(wfs_conf_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1824 (class 2606 OID 5358226)
-- Dependencies: 1253 1261 1747
-- Name: gui_wfs_ibfk_3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wfs
    ADD CONSTRAINT gui_wfs_ibfk_3 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1825 (class 2606 OID 5358231)
-- Dependencies: 1286 1791 1261
-- Name: gui_wfs_ibfk_4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wfs
    ADD CONSTRAINT gui_wfs_ibfk_4 FOREIGN KEY (fkey_wfs_id) REFERENCES wfs(wfs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1826 (class 2606 OID 5358236)
-- Dependencies: 1262 1253 1747
-- Name: gui_wms_ibfk_3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wms
    ADD CONSTRAINT gui_wms_ibfk_3 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1827 (class 2606 OID 5358241)
-- Dependencies: 1262 1803 1297
-- Name: gui_wms_ibfk_4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_wms
    ADD CONSTRAINT gui_wms_ibfk_4 FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1829 (class 2606 OID 5358246)
-- Dependencies: 1266 1768 1267
-- Name: layer_epsg_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY layer_epsg
    ADD CONSTRAINT layer_epsg_ibfk_1 FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1828 (class 2606 OID 5358251)
-- Dependencies: 1297 1803 1266
-- Name: layer_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY layer
    ADD CONSTRAINT layer_ibfk_1 FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1832 (class 2606 OID 5358256)
-- Dependencies: 1768 1269 1266
-- Name: layer_style_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY layer_style
    ADD CONSTRAINT layer_style_ibfk_1 FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1835 (class 2606 OID 5358261)
-- Dependencies: 1271 1772 1277
-- Name: mb_user_mb_group_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY mb_user_mb_group
    ADD CONSTRAINT mb_user_mb_group_ibfk_1 FOREIGN KEY (fkey_mb_group_id) REFERENCES mb_group(mb_group_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1836 (class 2606 OID 5358266)
-- Dependencies: 1276 1778 1278
-- Name: mb_user_wmc_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY mb_user_wmc
    ADD CONSTRAINT mb_user_wmc_ibfk_1 FOREIGN KEY (fkey_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1817 (class 2606 OID 5358271)
-- Dependencies: 1256 1747 1253
-- Name: pk_gui_layer_ifbk3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_layer
    ADD CONSTRAINT pk_gui_layer_ifbk3 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1818 (class 2606 OID 5358276)
-- Dependencies: 1768 1266 1256
-- Name: pk_gui_layer_ifbk4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gui_layer
    ADD CONSTRAINT pk_gui_layer_ifbk4 FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1837 (class 2606 OID 5358331)
-- Dependencies: 1282 1276 1778
-- Name: sld_user_layer_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_1 FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1838 (class 2606 OID 5358336)
-- Dependencies: 1266 1768 1282
-- Name: sld_user_layer_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_2 FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1839 (class 2606 OID 5358341)
-- Dependencies: 1282 1253 1747
-- Name: sld_user_layer_ibfk_3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_3 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1841 (class 2606 OID 5358281)
-- Dependencies: 1288 1290 1793
-- Name: wfs_conf_element_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_conf_element
    ADD CONSTRAINT wfs_conf_element_ibfk_1 FOREIGN KEY (fkey_wfs_conf_id) REFERENCES wfs_conf(wfs_conf_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1840 (class 2606 OID 5358286)
-- Dependencies: 1791 1286 1288
-- Name: wfs_conf_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_conf
    ADD CONSTRAINT wfs_conf_ibfk_1 FOREIGN KEY (fkey_wfs_id) REFERENCES wfs(wfs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1842 (class 2606 OID 5358291)
-- Dependencies: 1294 1799 1292
-- Name: wfs_element_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_element
    ADD CONSTRAINT wfs_element_ibfk_1 FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1843 (class 2606 OID 5358296)
-- Dependencies: 1286 1791 1294
-- Name: wfs_featuretype_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_featuretype
    ADD CONSTRAINT wfs_featuretype_ibfk_1 FOREIGN KEY (fkey_wfs_id) REFERENCES wfs(wfs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1844 (class 2606 OID 5358301)
-- Dependencies: 1294 1295 1799
-- Name: wfs_featuretype_namespace_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_featuretype_namespace
    ADD CONSTRAINT wfs_featuretype_namespace_ibfk_1 FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1845 (class 2606 OID 5358306)
-- Dependencies: 1286 1791 1295
-- Name: wfs_featuretype_namespace_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wfs_featuretype_namespace
    ADD CONSTRAINT wfs_featuretype_namespace_ibfk_2 FOREIGN KEY (fkey_wfs_id) REFERENCES wfs(wfs_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1846 (class 2606 OID 5358311)
-- Dependencies: 1298 1297 1803
-- Name: wms_format_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wms_format
    ADD CONSTRAINT wms_format_ibfk_1 FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1847 (class 2606 OID 5358316)
-- Dependencies: 1784 1280 1299
-- Name: wms_md_topic_category_fkey_md_topic_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wms_md_topic_category
    ADD CONSTRAINT wms_md_topic_category_fkey_md_topic_category_id_fkey FOREIGN KEY (fkey_md_topic_category_id) REFERENCES md_topic_category(md_topic_category_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1848 (class 2606 OID 5358321)
-- Dependencies: 1299 1297 1803
-- Name: wms_md_topic_category_fkey_wms_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wms_md_topic_category
    ADD CONSTRAINT wms_md_topic_category_fkey_wms_id_fkey FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1849 (class 2606 OID 5358326)
-- Dependencies: 1803 1300 1297
-- Name: wms_srs_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY wms_srs
    ADD CONSTRAINT wms_srs_ibfk_1 FOREIGN KEY (fkey_wms_id) REFERENCES wms(wms_id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY wfs_conf
    ADD CONSTRAINT wfs_conf_ibfk_2 FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;