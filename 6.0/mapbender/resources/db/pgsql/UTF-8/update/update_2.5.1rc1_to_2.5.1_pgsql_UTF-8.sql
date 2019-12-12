--
-- database changes in version 2.5.1
--

DELETE FROM wfs_conf_element WHERE f_id = ((SELECT DISTINCT f_id FROM wfs_conf_element) EXCEPT (SELECT DISTINCT f_id FROM wfs_conf_element, wfs_element WHERE f_id = element_id));

ALTER TABLE ONLY wfs_element
    ADD CONSTRAINT wfs_element_element_id_key UNIQUE (element_id);
    
ALTER TABLE ONLY wfs_conf_element
    ADD CONSTRAINT wfs_conf_element_id_ibfk_1 FOREIGN KEY (f_id) REFERENCES wfs_element (element_id) ON UPDATE CASCADE ON DELETE CASCADE;
    