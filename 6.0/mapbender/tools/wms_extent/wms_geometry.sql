-- Add geometry column the_geom
Select AddGeometryColumn('mapbender','layer_epsg','the_geom',4326,'POLYGON',2);

-- update the column 
UPDATE layer_epsg set the_geom = 
transform(
geometryfromText(
'POLYGON(('||
layer_epsg.minx || ' ' || layer_epsg.miny || ',' ||
layer_epsg.minx || ' ' || layer_epsg.maxy || ',' ||
layer_epsg.maxx || ' ' || layer_epsg.maxy || ',' ||
layer_epsg.maxx || ' ' || layer_epsg.miny || ',' ||
layer_epsg.minx || ' ' || layer_epsg.miny ||
'))',ltrim(epsg,'EPSG:')::int4) ,4326
)
where epsg != 'EPSG:31492'
AND epsg != 'EPSG:31493'
AND epsg != 'EPSG:31494'
AND epsg != 'EPSG:42304'
AND epsg != 'EPSG:102257'
AND epsg != 'NONE'
;

-- create a view which is used in the mapfile mapbender_wms.map
Create view qry_wms_extent as
Select DISTINCT layer.layer_id,layer.fkey_wms_id,
layer_pos,
wms.wms_id, wms.wms_title,wms.wms_version,wms.wms_abstract,wms.wms_getcapabilities,
layer_epsg.epsg, 
layer_epsg.minx,
layer_epsg.miny,
layer_epsg.maxx,
layer_epsg.maxy,
layer_epsg.the_geom 
from layer
LEFT JOIN wms ON wms.wms_id = layer.fkey_wms_id
LEFT JOIN layer_epsg ON  layer.layer_id = layer_epsg.fkey_layer_id
where layer.layer_pos=0;
