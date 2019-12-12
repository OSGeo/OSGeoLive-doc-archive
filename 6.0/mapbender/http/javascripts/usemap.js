/* 
* $Id:$
* COPYRIGHT: (C) 2001 by ccgis. This program is free software under the GNU General Public
* License (>=v2). Read the file gpl.txt that comes with Mapbender for details. 
*/

function Usemap(usemapTargets, name, zIndex, pointBuffer, lineBuffer) {
	var targets = usemapTargets;
	this.name = name;
	this.zIndex = zIndex;
	this.pointBuffer = pointBuffer;
	this.lineBuffer = lineBuffer;
	
	//add a geometry to the actual usemap
	this.add = function(geom, title, mouseover, mouseout, click, mousemove){
		gA.addCopy(geom);
		if (typeof(title) != 'undefined') {gA.get(-1).title = title;} 
		else {gA.get(-1).title = "";}
		if (typeof(mouseover) != 'undefined') {gA.get(-1).mouseover = mouseover;} 
		else {gA.get(-1).mouseover = null;}
		if (typeof(mouseout) != 'undefined') {gA.get(-1).mouseout = mouseout;} 
		else {gA.get(-1).mouseout = null;}
		if (typeof(click) != 'undefined') {gA.get(-1).click = click;} 
		else {gA.get(-1).click = null;}
		if (typeof(mousemove) != 'undefined') {gA.get(-1).mousemove = mousemove;} 
		else {gA.get(-1).mousemove = null;}
	};
	
	//(re)generate Usemap
	this.setUsemap = function(){
		//first clean the Usemap
		this.hide();
		
		var actualMapObj;
		var actualImageMap;
		var actualImage;
		var actualDoc;
		
		//go throught the maps
		for(var t=0; t<targets.length; t++){
			
			//initialize actual dom elements
			actualMapObj = getMapObjIndexByName(targets[t]);
			actualDoc = Mapbender.modules[targets[t]].getDomElement().ownerDocument;
			actualImageMap = actualDoc.getElementById(this.name);
			actualImage = actualDoc.getElementById(this.name+"_img");

			//if the usemap doesn't exist create it
			if(!actualImageMap){
				actualImageMap = actualDoc.createElement("map");
				actualImage = actualDoc.createElement("img");
				actualImage.id = this.name+"_img";
				actualImageMap.id = this.name;
				actualImage.name = this.name+"_img";
				actualImageMap.name = this.name;
				actualImage.setAttribute("useMap", "#"+this.name);
				actualImage.style.position = "absolute";
				actualImage.style.border = "none";
				actualDoc.body.appendChild(actualImage);
				actualDoc.body.appendChild(actualImageMap);
				actualImage.src = "../img/transparent.gif";
			}
			//set Usemap Image dimensions
			actualImage.style.zIndex = this.zIndex;
			actualImage.style.width = mb_mapObj[actualMapObj].width + "px";
			actualImage.style.height = mb_mapObj[actualMapObj].height + "px";
			actualImage.style.left = mb_mapObj[actualMapObj].getDomElement().style.left;
			actualImage.style.top = mb_mapObj[actualMapObj].getDomElement().style.top;
			
			//generate areas
			for(var i=0;i<gA.count();i++){
				var m = gA.get(i);
				for(j=0;j<m.count();j++){
					var area = actualDoc.createElement("area");
					//add pointer to geometry object
					area.geom = m;
					
					//apply shape
					g = m.get(j);
					switch(g.geomType){
					case geomType.point:
						this.setPointAttributes(area, g, targets[t]);
						break;
					case geomType.line:
						this.setLineAttributes(area, g, targets[t]);
						break;
					case geomType.polygon:
						this.setPolygonAttributes(area, g, targets[t]);
						break;
					}
					
					//append to dom and set event handelers
/*					if($.browser.msie){
						alert(actualImageMap.areas.length)
				        actualImageMap.areas[actualImageMap.areas.length] = area;
					}
					else
*/					actualImageMap.appendChild(area);

					area.title = m.title;
					area.onmouseover = m.mouseover;
					area.onmouseout = m.mouseout;
					area.onmousemove = m.mousemove;
				 	area.onclick = m.click;
				}
			}
		}
	};
	
	this.setPointAttributes = function(area, m, target){
		//apply point shape to area
		m = realToMap(target, m.get(0));
		area.setAttribute("shape", 'circle');
		area.setAttribute("coords", parseInt(m.x)+","+parseInt(m.y)+","+this.pointBuffer);
	};
	this.setLineAttributes = function(area, m, target){
		//apply line shape to area
		area.setAttribute("shape", 'poly');
		if(m.count()==1)
			return this.setPointAttributes(area,m,target);
		
		points = [];
		
		first_point= realToMap(target,m.get(0));
		this_point = realToMap(target,m.get(1));
		//get vector from point 0 to point 1
		last_vec = this_point.minus(first_point);
		//get 90° rotated vector
		last_vec_o = new Point(-last_vec.y, last_vec.x);
		//calculate vectors with linebuffer length
		last_vec_o = last_vec_o.times(this.lineBuffer/last_vec_o.dist(new Point(0,0)));
		last_vec = last_vec.times(this.lineBuffer/last_vec.dist(new Point(0,0)));
		
		//add first pointsets
		points.unshift(first_point.plus(last_vec_o).minus(last_vec));
		points.push(first_point.minus(last_vec_o).minus(last_vec));
		
		for(var i=1;i<m.count()-1;i++){
			next_point = realToMap(target,m.get(i+1));
			//get vector from point n to point n+1
			vec = next_point.minus(this_point);
			//get orthogonal (90° rotated) vector		
			vec_o = new Point(-vec.y, vec.x);

			//resize vectors to linebuffer length
			vec_o = vec_o.times(this.lineBuffer/vec_o.dist(new Point(0,0)));
			vec = vec.times(this.lineBuffer/vec.dist(new Point(0,0)));
			
			//if direction is the same continue
			if(vec.equals(last_vec))
				continue;
			
			// calculate angle between the two vectors by 
			// calculating the argument diffenrences between complex numbers
			// arg(x + i*y)  
			var angle = (Math.atan2(vec.x,vec.y)-Math.atan2(last_vec.x,last_vec.y))
			//ensure that angle is -180<=angle<=180
			if(angle<-Math.PI)angle=2*Math.PI+angle;
			if(angle>+Math.PI)angle=2*Math.PI-angle;
			
			//calculate the distance between the next points on boundary
			//and the line point
			//the point will be in the direction of angle/2 relative to last_vec_o
			//since cosine is adjacent side / hypothenuse and we know that 
			//the adjacent side is lineBuffer the hypothenus (our distance) is
			var ndist = this.lineBuffer/(Math.cos(angle/2))
			//direction of next points on boundary
			var int_vec = vec_o.plus(last_vec_o);
			//resize direction vector to our distance
			int_vec = int_vec.times(ndist/int_vec.dist(new Point(0,0)));
			
			//look if we have a sharp corner (>90°)
			if(Math.abs(angle)>Math.PI/2){
				//look where we have the outer edge of corner > 90°
				if(angle<0){
					//angle is negative so the outer edge is "on top"
					//push cutted edge points
					points.push(this_point.minus(last_vec_o).plus(last_vec));
					points.push(this_point.minus(vec_o).minus(vec));
					//TODO look if we need the inner edge or maybe even not the last inserted point
					//push inner edge
					points.unshift(this_point.plus(int_vec));
				}
				else{
					//angle is positive so the outer edge is "on bottom"
					//push cutted edge points
					points.unshift(this_point.plus(last_vec_o).plus(last_vec));
					points.unshift(this_point.plus(vec_o).minus(vec));
					//TODO look if we need the inner edge or maybe even not the last inserted point
					//push inner edge
					points.push(this_point.minus(int_vec));
				}
			}
			//otherwise only calculate intersection of bufferboundary lines
			else{
				points.unshift(this_point.plus(int_vec));
				points.push(this_point.minus(int_vec));
			}
			//copy for next point
			last_vec = vec;
			last_vec_o = vec_o;
			this_point = next_point;
		}
		//add last pointsets
		points.unshift(this_point.plus(last_vec_o).plus(last_vec));
		points.push(this_point.minus(last_vec_o).plus(last_vec));

		coords = [];
		for (var i=0; i<points.length; i++) {
			coords.push(String(parseInt(points[i].x)));
			coords.push(String(parseInt(points[i].y)));
		}
		area.setAttribute("coords", coords.join(","));
	};
	this.setPolygonAttributes = function(area, m, target){
		//apply polygon shape to area
		area.setAttribute("shape", 'poly');
		coords = [];
		for (var i=0; i<m.count(); i++) {
			pos = realToMap(target, m.get(i));
			coords.push(String(parseInt(pos.x)));
			coords.push(String(parseInt(pos.y)));
		}
		area.setAttribute("coords", coords.join(","));
	};
	
	
	this.hide = function(){
		//hide the Usemap
		var actualImageMap;
		var actualImage;
		var actualDoc;
		for(var i=0;i<targets.length;i++){
			//get actual frame
			actualDoc = Mapbender.modules[targets[i]].getDomElement().ownerDocument;
			
			//clear map
			actualImageMap = actualDoc.getElementById(this.name);
			if(actualImageMap)
				actualImageMap.innerHTML = "";
			
			//hide hidden image
			actualImage = actualDoc.getElementById(this.name+"_img");
			if(actualImage){
				actualImage.style.width = "0px";
				actualImage.style.height = "0px";
			}
		}
	};
	
	this.clean = function(){
		this.hide();
		if (gA.count() > 0) {
			delete gA;
			gA = new GeometryArray();
		}
	};
	
	var gA = new GeometryArray();
}
