--UPDATE mb_user_mb_group set mb_user_mb_group_type = 2 where mb_user_mb_group_type = 1 ;
UPDATE mb_user_mb_group set mb_user_mb_group_type = 1 where mb_user_mb_group_type IS NULL;
--***** new role system - should not influence the normal behaviour 
-- Table: mb_role

-- DROP TABLE mb_role;

CREATE TABLE mb_role
(
  role_id serial NOT NULL,
  role_name character varying(50),
  role_description character varying(255),
  role_exclude_auth integer NOT NULL DEFAULT 0,
  CONSTRAINT role_id PRIMARY KEY (role_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE mb_role OWNER TO postgres;


--things to be done for mb_user_mb_group table:
--drop old constraint
--Allow to be member in a group with different roles
ALTER TABLE mb_user_mb_group DROP CONSTRAINT pk_fkey_mb_user_mb_group_id;

UPDATE mb_user_mb_group SET mb_user_mb_group_type = 1 WHERE mb_user_mb_group_type IS NULL OR mb_user_mb_group_type = 0;

--default to standard role
ALTER TABLE mb_user_mb_group ALTER COLUMN mb_user_mb_group_type SET DEFAULT 1;

-- Constraint: pk_fkey_mb_user_mb_group_id

-- ALTER TABLE mb_user_mb_group DROP CONSTRAINT pk_fkey_mb_user_mb_group_id;
--create new constraint
ALTER TABLE mb_user_mb_group
  ADD CONSTRAINT pk_fkey_mb_user_mb_group_id PRIMARY KEY(fkey_mb_user_id, fkey_mb_group_id, mb_user_mb_group_type);



--things for the role table
--standard roles:
INSERT INTO mb_role (role_name,role_description,role_exclude_auth) VALUES ('standard role','No special role - old behaviour.',0);

INSERT INTO mb_role (role_name,role_description,role_exclude_auth) VALUES ('primary','Primary group for a mapbender user.',0);

INSERT INTO mb_role (role_name,role_description,role_exclude_auth) VALUES ('metadata editor','Group for which the user can edit and publish metadata.',1);

--constraint for new role system
-- ALTER TABLE mb_user_mb_group DROP CONSTRAINT fkey_mb_user_mb_group_role_id;

ALTER TABLE mb_user_mb_group
  ADD CONSTRAINT fkey_mb_user_mb_group_role_id FOREIGN KEY (mb_user_mb_group_type)
      REFERENCES mb_role (role_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

--link for admin1
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','Group_User_Role',2,1,'allocate groups to user and roles','','a','','href = "../php/mod_group_user_role.php?sessionID&e_id_css=Group_User_Role" target = "AdminFrame" ',10,1234,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','GROUP -> USER -> ROLE','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin1', 'Group_User_Role', 'file css', '../css/administration_alloc.css', 'file css' ,'file/css');

--update the group for the decentral registrating institutions
-- View: registrating_groups
CREATE OR REPLACE VIEW registrating_groups AS 
 SELECT f.fkey_mb_group_id, f.fkey_mb_user_id
   FROM mb_user_mb_group f, mb_user_mb_group s
  WHERE f.mb_user_mb_group_type = 2 AND s.fkey_mb_group_id = 36 AND f.fkey_mb_user_id = s.fkey_mb_user_id
  ORDER BY f.fkey_mb_group_id, f.fkey_mb_user_id;
--*****end of role concept****


