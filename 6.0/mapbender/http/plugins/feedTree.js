 /**
  * Package: feedTree
  *
  * Description:
  * Module to load GeoRSS or KML temporary in a tree
  * 
  * Files:
  *  - mapbender/http/plugins/feedTree.js
  *  - mapebnder/lib/mb.ui.displayFeatures.js
  *  - mapbender/http/css/feedtree.css
  *
  * SQL:
  * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
  * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
  * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
  * > 'feedTree',2,1,'Displays a GeoRSS feed on the map','GeoRss','ul','','',1,1,200,200,NULL ,
  * > 'visibility:visible','','ul','../plugins/feedTree.js','../../lib/mb.ui.displayFeatures.js',
  * > 'mapframe1','jq_ui_widget','http://www.mapbender.org/Loadkmlgeorss');
  * > 
  * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
  * > VALUES('gui', 'feedTree', 'styles', '../css/feedtree.css', '' ,'file/css');
  *
  * Help:
  * http://www.mapbender.org/Loadkmlgeorss
  *
  * Maintainer:
  * http://www.mapbender.org/User:Karim_Malhas
  * 
  *
  * License:
  * Copyright (c) 2009, Open Source Geospatial Foundation
  * This program is dual licensed under the GNU General Public License 
  * and Simplified BSD license.  
  * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
  */

var $feedTree = $(this);
var FeedTree = function(o){
	$feedTree.children().remove();
	$feedTree.addClass('feedtree');

	var $RSSfolder = $('<li class="open rss"><button class="toggle" name="toggle" value="toggle"></button><a href="#">RSS</a><ul></ul></li>');
	$feedTree.append($RSSfolder);

	$addButton = $('<button class="add" name="addrss" value="addrss"></button>');
	$addButton.click(function(){
		$('<div ><input class="feedurl" /></div>').dialog({
			"title": "RSS feed hinzufügen",
			"buttons":{
				"OK": function(){
					$('#mapframe1').georss({ url: $(this).find('.feedurl').val()});
					$(this).dialog('destroy');
				}
			}
		});
	});
	$RSSfolder.find("a").after($addButton);

	
//	var $KMLfolder = $('<li class="open rss"><img src="../img/kml_logo.png" /><a href="#">KML</a><ul></ul></li>');
//	$feedTree.append($KMLfolder);

	
	
	o.$target.bind('georss:loaded',function(e,obj){
		//console.log(obj);
		var checked = obj.display ? 'checked="checked"':'';


		title = obj.url;
		abbrevTitle = title.length < 20 ?  title : title.substr(0,17) + "...";
		$rssEntry = $('<li title="'+ title +'" class="open"><button class="toggle" name="toggle" value="toggle" ></button> <input type="checkbox"'+checked  +'/><button class="remove" name="remove" value="remove" ></button><a href="#">'+abbrevTitle+'</a></li>');
		$RSSfolder.children("ul").append($rssEntry);
		$rssEntry.find("a").bind("click",(function(jsonFeatureCollection){return function(){
				var map = o.$target.mapbender();
				var g = new GeometryArray();
				g.importGeoJSON(jsonFeatureCollection,false);
		
				var bbox = g.getBBox();
				var bufferFloat = parseFloat(0.1);
				var buffer = new Point(bufferFloat,bufferFloat);
				bbox[0] = bbox[0].minus(buffer);
				bbox[1] = bbox[1].plus(buffer);


				map.calculateExtent(
				  new Mapbender.Extent(bbox[0], bbox[1])
				);
				map.setMapRequest();
	
				};
		})(obj.data));
		
		$featureList = $("<ul />");
		$rssEntry.append($featureList);
		for(var i = 0;i < obj.data.features.length;i++){

			title = obj.data.features[i].properties.title;
			abbrevTitle = title.length < 20 ?  title : title.substr(0,17) + "...";
			$feature = $('<li title="'+ title +'"><a href="#" >'+ abbrevTitle + '</a></li>');
			$featureList.append($feature);
			title = obj.data.features[i].properties.title;
			$feature.bind('click',(function(jsonFeature){return function(){
				
				var map = o.$target.mapbender();
				var g = new GeometryArray();
				g.importGeoJSON(jsonFeature,false);
				var feature = g.get(0);
		
				var bbox = feature.getBBox();
				var bufferFloat = parseFloat(0.1);
				var buffer = new Point(bufferFloat,bufferFloat);
				bbox[0] = bbox[0].minus(buffer);
				bbox[1] = bbox[1].plus(buffer);


				map.calculateExtent(
				  new Mapbender.Extent(bbox[0], bbox[1])
				);
				map.setMapRequest();
	
				};
			})(obj.data.features[i]));
			
			$feature.bind('mouseout',(function(jsonFeature){return function(){
				var me = $feedTree.mapbender();
				me.resultHighlight.clean();
				me.resultHighlight.paint();
			}})());
			$feature.bind('mouseover',(function(jsonFeature){return function(){
				var me = $feedTree.mapbender();
				var g = new GeometryArray();
				g.importGeoJSON(jsonFeature,false);
				var feature = g.get(0);
			
				if(feature.geomType != "point"){
					feature = feature.getBBox4();
					me.resultHighlight = new Highlight(
							[o.target],
							"FeedTreeHighlight", 
							{"position":"absolute", "top":"0px", "left":"0px", "z-index":100}, 
							2);
				
					me.resultHighlight.add(feature, "#00ff00");
					me.resultHighlight.paint();
				}
				else if(feature.geomType == "point"){

				}
	
				};
			})(obj.data.features[i]));


		}

		$("*:checkbox",$rssEntry).bind('click', function(){
			if($(this).attr('checked')){
				o.$target.georss('show',obj.url);
			}else{
				o.$target.georss('hide',obj.url);
			}
		});
		
		$("button.toggle",$rssEntry).bind('click', function(){
			if($(this).parent().hasClass("open")){
				$(this).parent().removeClass("open");
				$(this).parent().addClass("closed");
			}else{
				$(this).parent().removeClass("closed");
				$(this).parent().addClass("open");

			}
		});

		$("button.remove",$rssEntry).bind('click', function(){
			o.$target.georss('remove',obj.url);
			$(this).parent().remove();
		});

		
	});

};

Mapbender.events.init.register(function(){
	$feedTree.mapbender(new FeedTree(options));
});
