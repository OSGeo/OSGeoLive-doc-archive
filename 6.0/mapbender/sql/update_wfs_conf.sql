UPDATE wfs_conf SET 
wfs_conf_abstract = $1,
g_label = $2,
g_label_id = $3,
g_button = $4,
g_button_id = $5,
g_style = $6,
g_buffer = $7,
g_res_style = $8,
wfs_conf_description = $9,
wfs_conf_type = $10
WHERE wfs_conf_id = $11
