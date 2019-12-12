Tool to show a simple bounding box graphic which is rendered by mapserver.
Mapbender itselfs comes without an own ows server component. Therefor it is not so easy to generate a simple graphic for the extents (bboxes) of the different resources (wms/layer/wfs/featuretypes/georss/kml) which are registrated in the mapbender meta-database. It will be possible to do it by using a mapbender or openlayers client and render the objects with javascript. An other possibility is to render the bbox and the background by using a wms server component. The bbox can be given as vendor specific parameters to mapserver and the server decides to render the geometry. It is easy to render different objects for different areas of the bounding boxes. For mapserver a postgis database backend is used. The user can define the same database as used for mapbender installation (if postgis is provided).
Principle:
Send vsp for bbox to mapserv.cgi and generating a simple polygon by postgis functions on the fly (data string). The polygon and/or the central point will be rendered by the mapserv process itself. The url of the request is defined in a special conf file and can be included by the module which needs the visualization of extents.
The principle is better than the solution with a special client because no javascript data has to be send thru the browser and the rendering of bounding boxes is not time-consuming.

List of files:
tools/wms_extent/extent_service.conf
tools/wms_extent/extents.map
tools/wms_extent/symbolset_mapbender.sym
tools/wms_extent/target.png

