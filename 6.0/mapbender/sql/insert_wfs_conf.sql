INSERT INTO wfs_conf (
	wfs_conf_abstract,
	fkey_wfs_id,
	fkey_featuretype_id,
	g_label,
	g_label_id,
	g_button,
	g_button_id,
	g_style,
	g_buffer,
	g_res_style,
	wfs_conf_description,
	wfs_conf_type
) VALUES (
	$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12
)
