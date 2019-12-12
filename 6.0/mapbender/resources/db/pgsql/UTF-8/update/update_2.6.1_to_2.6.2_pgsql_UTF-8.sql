-- update admin application replace gui with application
--
Update gui_element set e_title ='Delete application', e_comment ='Delete application', e_content='Delete application' where fkey_gui_id = 'admin_en_services' AND e_id = 'delete_filteredGui';
Update gui_element set e_title ='Delete application', e_comment ='Delete application', e_content='Delete application'  where fkey_gui_id = 'admin2_en' AND e_id = 'delete_filteredGui';

Update gui_element set e_title ='Create new application', e_comment ='Create new application', e_content='Create new application' where fkey_gui_id = 'admin_en_services' AND e_id = 'newGui';
Update gui_element set e_title ='Create new application', e_comment ='Create new application', e_content='Create new application'  where fkey_gui_id = 'admin2_en' AND e_id = 'newGui';

Update gui_element set e_title ='Add application to category', e_comment ='Add application to category', e_content='Add application to category' where fkey_gui_id = 'admin_en_services' AND e_id = 'category_filteredGUI';
Update gui_element set e_width= 190, e_title ='Add application to category', e_comment ='Add application to category', e_content='Add application to category' where fkey_gui_id = 'admin2_en' AND e_id = 'category_filteredGUI';

Update gui_element set  e_title ='Create application category', e_comment ='Create application category', e_content='Create application category' where fkey_gui_id = 'admin_en_services' AND e_id = 'createCategory';
Update gui_element set  e_width= 190,e_title ='Create application category', e_comment ='Create application category', e_content='Create application category' where fkey_gui_id = 'admin2_en' AND e_id = 'createCategory';

Update gui_element set e_top = 511 ,e_title ='Delete application', e_comment ='Delete application', e_content='Delete application' where fkey_gui_id = 'admin_en_services' AND e_id = 'delete_filteredGui';
Update gui_element set e_top =280,e_left=8, e_width= 190,e_title ='Delete application', e_comment ='Delete application', e_content='Delete application' where fkey_gui_id = 'admin2_en' AND e_id = 'delete_filteredGui';

Update gui_element set e_top=491 ,e_title ='Edit application elements', e_comment ='Edit application elements', e_content='Edit application elements' where fkey_gui_id = 'admin_en_services' AND e_id = 'editElements';
Update gui_element set e_title ='Edit application elements', e_comment ='Edit application elements', e_content='Edit application elements' where fkey_gui_id = 'admin2_en' AND e_id = 'editElements';

Update gui_element set e_title ='WMS application settings', e_comment ='WMS application settings', e_content='WMS application settings' where fkey_gui_id = 'admin_en_services' AND e_id = 'editGUI_WMS';
Update gui_element set e_title ='WMS application settings', e_comment ='WMS application settings', e_content='WMS application settings' where fkey_gui_id = 'admin2_en' AND e_id = 'editGUI_WMS';

Update gui_element set e_title ='Export application (SQL)', e_comment ='Export application (SQL)', e_content='Export application (SQL)' where fkey_gui_id = 'admin_en_services' AND e_id = 'exportGUI';
Update gui_element set e_title ='Export application (SQL)', e_comment ='Export application (SQL)', e_content='Export application (SQL)' where fkey_gui_id = 'admin2_en' AND e_id = 'exportGUI';

Update gui_element set e_title ='Allow group access to <br> applications', e_comment ='Allow group access to <br> applications', e_content='Allow group access to <br> applications' where fkey_gui_id = 'admin_en_services' AND e_id = 'filteredGroup_filteredGui';
Update gui_element set e_title ='Allow group access to <br> applications', e_comment ='Allow group access to <br> applications', e_content='Allow group access to <br> applications' where fkey_gui_id = 'admin2_en' AND e_id = 'filteredGroup_filteredGui';

Update gui_element set e_title ='Allow several groups access <br> to one application', e_comment ='Allow several groups access <br> to one application', e_content='Allow several groups access <br> to one application' where fkey_gui_id = 'admin_en_services' AND e_id = 'filteredGui_filteredGroup';
Update gui_element set e_title ='Allow several groups access <br> to one application', e_comment ='Allow several groups access <br> to one application', e_content='Allow several groups access <br> to one application' where fkey_gui_id = 'admin2_en' AND e_id = 'filteredGui_filteredGroup';

Update gui_element set e_title ='Allow several users access to <br> one application', e_comment ='Allow several users access to <br> one application', e_content='Allow several users access to <br> one application' where fkey_gui_id = 'admin_en_services' AND e_id = 'filteredGui_filteredUser';
Update gui_element set e_title ='Allow several users access to <br> one application', e_comment ='Allow several users access to <br> one application', e_content='Allow several users access to <br> one application' where fkey_gui_id = 'admin2_en' AND e_id = 'filteredGui_filteredUser';

Update gui_element set e_title ='Allow one user to access <br> several applications', e_comment ='Allow one user to access <br> several applications', e_content='Allow one user to access <br> several applications' where fkey_gui_id = 'admin_en_services' AND e_id = 'filteredUser_filteredGui';
Update gui_element set e_title ='Allow one user to access <br> several applications', e_comment ='Allow one user to access <br> several applications', e_content='Allow one user to access <br> several applications' where fkey_gui_id = 'admin2_en' AND e_id = 'filteredUser_filteredGui';

Update gui_element set e_title ='Assign to edit an application to a user', e_comment ='Assign to edit an application to a user', e_content='Assign to edit an application to a user' where fkey_gui_id = 'admin_en_services' AND e_id = 'gui_owner';
Update gui_element set e_title ='Assign to edit an application to a user', e_comment ='Assign to edit an application to a user', e_content='Assign to edit an application to a user' where fkey_gui_id = 'admin2_en' AND e_id = 'gui_owner';

Update gui_element set e_title ='Application Category Management', e_comment ='Application Category Management', e_content='Application Category Management' where fkey_gui_id = 'admin_en_services' AND e_id = 'headline_GUI_Category';
Update gui_element set e_title ='Application Category Management', e_comment ='Application Category Management', e_content='Application Category Management' where fkey_gui_id = 'admin2_en' AND e_id = 'headline_GUI_Category';

Update gui_element set e_title ='Application Management', e_comment ='Application Management', e_content='Application Management' where fkey_gui_id = 'admin_en_services' AND e_id = 'headline_GUI_Management';
Update gui_element set e_title ='Application Management', e_comment ='Application Management', e_content='Application Management' where fkey_gui_id = 'admin2_en' AND e_id = 'headline_GUI_Management';

Update gui_element set e_title ='Link WMS to application', e_comment ='Link WMS to application', e_content='Link WMS to application' where fkey_gui_id = 'admin_en_services' AND e_id = 'loadWMSList';
Update gui_element set e_title ='Link WMS to application', e_comment ='Link WMS to application', e_content='Link WMS to application' where fkey_gui_id = 'admin2_en' AND e_id = 'loadWMSList';

Update gui_element set e_title ='Move back to your application list', e_comment ='Move back to your application list', e_content='' where fkey_gui_id = 'admin_en_services' AND e_id = 'myGUIlist';
Update gui_element set e_title ='Move back to your application list', e_comment ='Move back to your application list', e_content='' where fkey_gui_id = 'admin2_en' AND e_id = 'myGUIlist';

Update gui_element set e_title ='Create new application', e_comment ='Create new application', e_content='Create new application' where fkey_gui_id = 'admin_en_services' AND e_id = 'newGui';
Update gui_element set e_top= 240, e_width= 190, e_title ='Create new application', e_comment ='Create new application', e_content='Create new application' where fkey_gui_id = 'admin2_en' AND e_id = 'newGui';

Update gui_element set e_title ='Rename / copy application', e_comment ='Rename / copy application', e_content='Rename / copy application' where fkey_gui_id = 'admin_en_services' AND e_id = 'rename_copy_Gui';
Update gui_element set e_title ='Rename / copy application', e_comment ='Rename / copy application', e_content='Rename / copy application' where fkey_gui_id = 'admin2_en' AND e_id = 'rename_copy_Gui';

Delete from gui_element where e_id = 'CreateTreeGDE' and fkey_gui_id IN ('admin2_de', 'admin2_en');

---
Update gui_element set e_title ='Anwendung löschen', e_comment ='Anwendung löschen', e_content='Anwendung löschen' where fkey_gui_id = 'admin_de_services' AND e_id = 'delete_filteredGui';
Update gui_element set e_title ='Anwendung löschen', e_comment ='Anwendung löschen', e_content='Anwendung löschen'  where fkey_gui_id = 'admin2_de' AND e_id = 'delete_filteredGui';

Update gui_element set e_title ='Anwendung erzeugen', e_comment ='Anwendung erzeugen', e_content='Anwendung erzeugen' where fkey_gui_id = 'admin_de_services' AND e_id = 'newGui';
Update gui_element set e_title ='Anwendung erzeugen', e_comment ='Anwendung erzeugen', e_content='Anwendung erzeugen'  where fkey_gui_id = 'admin2_de' AND e_id = 'newGui';

Update gui_element set e_title ='Anwendung zu Kategorie zuordnen', e_comment ='Anwendung zu Kategorie zuordnen', e_content='Anwendung zu Kategorie zuordnen' where fkey_gui_id = 'admin_de_services' AND e_id = 'category_filteredGUI';
Update gui_element set e_width= 190, e_title ='Anwendung zu Kategorie zuordnen', e_comment ='Anwendung zu Kategorie zuordnen', e_content='Anwendung zu Kategorie zuordnen' where fkey_gui_id = 'admin2_de' AND e_id = 'category_filteredGUI';

Update gui_element set  e_title ='Anwendungskategorie erzeugen', e_comment ='Anwendungskategorie erzeugen', e_content='Anwendungskategorie erzeugen' where fkey_gui_id = 'admin_de_services' AND e_id = 'createCategory';
Update gui_element set  e_width= 190,e_title ='Anwendungskategorie erzeugen', e_comment ='Anwendungskategorie erzeugen', e_content='Anwendungskategorie erzeugen' where fkey_gui_id = 'admin2_de' AND e_id = 'createCategory';

Update gui_element set e_top = 511 ,e_title ='Anwendung löschen', e_comment ='Anwendung löschen', e_content='Anwendung löschen' where fkey_gui_id = 'admin_de_services' AND e_id = 'delete_filteredGui';
Update gui_element set e_top =280,e_left=8, e_width= 190,e_title ='Anwendung löschen', e_comment ='Anwendung löschen', e_content='Anwendung löschen' where fkey_gui_id = 'admin2_de' AND e_id = 'delete_filteredGui';

Update gui_element set e_top=491 ,e_title ='Anwendungselemente bearbeiten', e_comment ='Anwendungselemente bearbeiten', e_content='Anwendungselemente bearbeiten' where fkey_gui_id = 'admin_de_services' AND e_id = 'editElements';
Update gui_element set e_title ='Anwendungselemente bearbeiten', e_comment ='Anwendungselemente bearbeiten', e_content='Anwendungselemente bearbeiten' where fkey_gui_id = 'admin2_de' AND e_id = 'editElements';

Update gui_element set e_title ='WMS Anwendungseinstellungen', e_comment ='WMS Anwendungseinstellungen', e_content='WMS Anwendungseinstellungen' where fkey_gui_id = 'admin_de_services' AND e_id = 'editGUI_WMS';
Update gui_element set e_title ='WMS Anwendungseinstellungen', e_comment ='WMS Anwendungseinstellungen', e_content='WMS Anwendungseinstellungen' where fkey_gui_id = 'admin2_de' AND e_id = 'editGUI_WMS';

Update gui_element set e_title ='Anwendung exportieren (SQL)', e_comment ='Anwendung exportieren (SQL)', e_content='Anwendung exportieren (SQL)' where fkey_gui_id = 'admin_de_services' AND e_id = 'exportGUI';
Update gui_element set e_title ='Anwendung exportieren (SQL)', e_comment ='Anwendung exportieren (SQL)', e_content='Anwendung exportieren (SQL)' where fkey_gui_id = 'admin2_de' AND e_id = 'exportGUI';

Update gui_element set e_title ='Gruppe Zugriff auf <br>Anwendung erlauben', e_comment ='Gruppe Zugriff auf <br>Anwendung erlauben', e_content='Gruppe Zugriff auf <br>Anwendung erlauben' where fkey_gui_id = 'admin_de_services' AND e_id = 'filteredGroup_filteredGui';
Update gui_element set e_title ='Gruppe Zugriff auf <br>Anwendung erlauben', e_comment ='Gruppe Zugriff auf <br>Anwendung erlauben', e_content='Gruppe Zugriff auf <br>Anwendung erlauben' where fkey_gui_id = 'admin2_de' AND e_id = 'filteredGroup_filteredGui';

Update gui_element set e_title ='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben', e_comment ='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben', e_content='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben' where fkey_gui_id = 'admin_de_services' AND e_id = 'filteredGui_filteredGroup';
Update gui_element set e_title ='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben', e_comment ='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben', e_content='mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben' where fkey_gui_id = 'admin2_de' AND e_id = 'filteredGui_filteredGroup';

Update gui_element set e_title ='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben', e_comment ='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben', e_content='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben' where fkey_gui_id = 'admin_de_services' AND e_id = 'filteredGui_filteredUser';
Update gui_element set e_title ='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben', e_comment ='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben', e_content='mehreren Benutzern Zugriff auf <br>einzelne Anwendung erlauben' where fkey_gui_id = 'admin2_de' AND e_id = 'filteredGui_filteredUser';

Update gui_element set e_title ='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben', e_comment ='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben', e_content='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben' where fkey_gui_id = 'admin_de_services' AND e_id = 'filteredUser_filteredGui';
Update gui_element set e_title ='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben', e_comment ='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben', e_content='einzelnem Benutzer Zugriff auf <br>Anwendungen erlauben' where fkey_gui_id = 'admin2_de' AND e_id = 'filteredUser_filteredGui';

Update gui_element set e_title ='Anwendung editieren Benutzer zuordnen', e_comment ='Anwendung editieren Benutzer zuordnen', e_content='Anwendung editieren Benutzer zuordnen' where fkey_gui_id = 'admin_de_services' AND e_id = 'gui_owner';
Update gui_element set e_title ='Anwendung editieren Benutzer zuordnen', e_comment ='Anwendung editieren Benutzer zuordnen', e_content='Anwendung editieren Benutzer zuordnen' where fkey_gui_id = 'admin2_de' AND e_id = 'gui_owner';

Update gui_element set e_title ='Anwendungskategorien verwalten', e_comment ='Anwendungskategorien verwalten', e_content='Anwendungskategorien verwalten' where fkey_gui_id = 'admin_de_services' AND e_id = 'headline_GUI_Category';
Update gui_element set e_title ='Anwendungskategorien verwalten', e_comment ='Anwendungskategorien verwalten', e_content='Anwendungskategorien verwalten' where fkey_gui_id = 'admin2_de' AND e_id = 'headline_GUI_Category';

Update gui_element set e_title ='Anwendungsverwaltung', e_comment ='Anwendungsverwaltung', e_content='Anwendungsverwaltung' where fkey_gui_id = 'admin_de_services' AND e_id = 'headline_GUI_Management';
Update gui_element set e_title ='Anwendungsverwaltung', e_comment ='Anwendungsverwaltung', e_content='Anwendungsverwaltung' where fkey_gui_id = 'admin2_de' AND e_id = 'headline_GUI_Management';

Update gui_element set e_title ='WMS in Anwendung einbinden', e_comment ='WMS in Anwendung einbinden', e_content='WMS in Anwendung einbinden' where fkey_gui_id = 'admin_de_services' AND e_id = 'loadWMSList';
Update gui_element set e_title ='WMS in Anwendung einbinden', e_comment ='WMS in Anwendung einbinden', e_content='WMS in Anwendung einbinden' where fkey_gui_id = 'admin2_de' AND e_id = 'loadWMSList';

Update gui_element set e_title ='Zurück zur Anwendungsübersicht', e_comment ='Zurück zur Anwendungsübersicht', e_content='' where fkey_gui_id = 'admin_de_services' AND e_id = 'myGUIlist';
Update gui_element set e_title ='Zurück zur Anwendungsübersicht', e_comment ='Zurück zur Anwendungsübersicht', e_content='' where fkey_gui_id = 'admin2_de' AND e_id = 'myGUIlist';

Update gui_element set e_title ='Anwendung erzeugen', e_comment ='Anwendung erzeugen', e_content='Anwendung erzeugen' where fkey_gui_id = 'admin_de_services' AND e_id = 'newGui';
Update gui_element set e_top= 240, e_width= 190, e_title ='Anwendung erzeugen', e_comment ='Anwendung erzeugen', e_content='Anwendung erzeugen' where fkey_gui_id = 'admin2_de' AND e_id = 'newGui';

Update gui_element set e_title ='Anwendung kopieren/umbenennen', e_comment ='Anwendung kopieren/umbenennen', e_content='Anwendung kopieren/umbenennen' where fkey_gui_id = 'admin_de_services' AND e_id = 'rename_copy_Gui';
Update gui_element set e_title ='Anwendung kopieren/umbenennen', e_comment ='Anwendung kopieren/umbenennen', e_content='Anwendung kopieren/umbenennen' where fkey_gui_id = 'admin2_de' AND e_id = 'rename_copy_Gui';

Delete from gui_element where e_id = 'CreateTreeGDE' and fkey_gui_id IN ('admin2_de', 'admin2_en');
Delete from gui_element where e_id = 'CreateTreeGDE' and fkey_gui_id IN ('admin_de_services', 'admin_en_services');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
VALUES('admin_en_services','Customize Tree',2,1,'Create a set of nested folders that contain the applications WMS','Customize tree','a','','href = "../php/mod_customTree.php?sessionID" target="AdminFrame"',10,533,200,20,5 ,'','Customize tree','a','','','','AdminFrame','');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
VALUES('admin_de_services','Customize Tree',2,1,'Create a set of nested folders that contain the applications WMS','Baumstruktur konfigurieren','a','','href = "../php/mod_customTree.php?sessionID" target="AdminFrame"',10,533,200,20,5 ,'','Baumstruktur konfigurieren','a','','','','AdminFrame','');


--
-- update application description 
--
Update gui set gui_description = 'admin application containing all adminstrative modules (use only as a template)' where gui_id = 'admin1';
Update gui set gui_description = 'admin application in german language (Administrations-Anwendung in deutscher Sprache)' where gui_id = 'admin2_de';
Update gui set gui_description = 'admin application in english' where gui_id = 'admin2_en';
Update gui set gui_description = 'extended admin application - WMS, WFS, metadata-handling (erweiterte Administration-Anwendung in deutscher Sprache)' where gui_id = 'admin_de_services';
Update gui set gui_description = 'extended admin application - WMS, WFS, metadata-handling (english)' where gui_id = 'admin_en_services';
Update gui set gui_description = 'application with tab, search modules' where gui_id = 'gui';
Update gui set gui_description = 'application combining most of the Mapbender functionality' where gui_id = 'gui1';
Update gui set gui_description = 'application with WFS search and digitizing using WFS-T' where gui_id = 'gui_digitize';
Update gui set gui_description = 'application with focus on layout' where gui_id = 'gui2';

