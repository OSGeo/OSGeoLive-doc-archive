INSERT INTO wfs_conf_element (
	fkey_wfs_conf_id,
	f_id,
	f_geom,
	f_search,
	f_pos,
	f_style_id,
	f_toupper,
	f_label,
	f_label_id,
	f_show,
	f_respos,
	f_form_element_html,
	f_edit,
	f_mandatory,
	f_auth_varname,
	f_operator,
	f_show_detail,
	f_detailpos,
	f_min_input,
	f_helptext,
	f_category_name
) VALUES (
	$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, 
	$13, $14, $15, $16, $17, $18, $19, $20, $21
)