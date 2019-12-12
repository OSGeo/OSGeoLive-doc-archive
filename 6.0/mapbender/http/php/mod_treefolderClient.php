<?php
# $Id: mod_treefolderClient.php 6673 2010-08-02 13:52:19Z christoph $
# http://www.mapbender.org/index.php/Administration
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<HTML>
<HEAD>
<META NAME="Generator" CONTENT="Cosmo Create 1.0.3">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>

<TITLE>Treefolder Client</TITLE>
    <STYLE TYPE="text/css"> 
    <!-- 
    .sitemap { 
	    font-family:Arial,Helvetica;
	    font-size:10pt;
	    line-height:6pt;
    }

    body { margin-top:7px;
    	font-family : Arial, Helvetica, sans-serif;
   	font-size : 12px;
	   font-weight : bold;
      color: #808080;
      margin-left:4px;
      overflow-x: hidden;
   }
    a:link { 
      text-decoration: none;
     	font-family : Arial, Helvetica, sans-serif;
	   font-size : 12px;
   	font-weight : bold;
      color: #808080; 
    }
    a:visited { 
      text-decoration: none;
    	font-family : Arial, Helvetica, sans-serif;
   	font-size : 12px;
	   font-weight : bold;
      color: #808080; 
   }
    a:active { text-decoration: none }
        a:active { text-decoration: none }
    .header{
      font-family : Arial, Helvetica, sans-serif;
   	font-size : 14px;
	   font-weight : bold;
      color: #808080;
    }
    // -->
    </STYLE>
<?php
echo "<script language='JavaScript'>";
echo "var treetarget = '".$e_target."';";
echo "</script>";
?>
  <SCRIPT language="JavaScript1.2">
  <!--
  
  /*
   * sitemap.js 1.31 05/02/2000
   *  - Opera 5
   *
   * sitemap.js 1.3 27/11/2000
   *  - Netscape 6
   *
   * sitemap.js 1.2 20/05/2000
   *  - split array tree into arrays for each element old tree
   *  - no mory type flag, an folder is an entry which has sons
   *  - a folder can have an link
   *  - while initing an default layers is shown 
   *
   * sitemap.js 1.1 20/10/1999
   *  - showTree only updates and init layers new which have been really changed
   *  - add deep to knot entry
   *  - substitute knotDeep[ id ] w/ tree[ id2treeIndex[ id ] ].deep
   *  - add alignment to img and a &nbsp; at the beginning of eyery line
   *  - add a fake img for bookmarks on top panel
   *
   * sitemap.js 1.02 14/10/1999
   *  - fix bug in initStyles
   *
   * sitemap.js 1.01 06/10/1999
   *  - fix bug in knotDeep for Netscape 4.00-4.0.5
   *
   * sitemap.js 1.0 20/09/1999
   *
   * Javascript function for displaying hierarchic directory structures with
   * the ability to collapse and expand directories.
   *
   * Copyright (c) 1999 Polzin GmbH, Duesseldorf. All Rights Reserved.
   * Author: Lutz Eymers <ixtab@polzin.com>
   * Download: http://www.polzin.com/inet/fset_inet.phtml?w=goodies
   *
   * Permission to use, copy, modify, and distribute this software
   * and its documentation for any purposes and without fee
   * is hereby granted provided that this copyright notice
   * appears in all copies. 
   *
   * Of course, this software is provided "as is" without express or implied
   * warranty of any kind.
   *
   */

  window.onError=null;

  var idx=0
  var treeId = new Array();
  var treeP_id = new Array();
  var treeIsOn = new Array();
  var treeTyp = new Array();
  var treeName = new Array();
  var treeUrl = new Array();
  var treeWasOn = new Array();
  var treeDeep = new Array();
  var treeLastY = new Array();
  var treeIsShown = new Array();
  var treeURL = new Array();
  var treeWMS_id = new Array();
// o_id,level, Beschr, layer_id,lft, wms_id
  function Note( id,p_id,name,url,left, wms_id ) {
    treeId[ idx ] = id;
    treeP_id[ idx ] = p_id;
    treeIsOn[ idx ] = false;
    treeTyp[ idx ] = 'f';
    treeName[ idx ] = name; 
    treeUrl[ idx ] = url ;
    treeWasOn[ idx ] = false;
    treeDeep[ idx ] = 0;
    treeLastY[ idx ] = 0;
    treeIsShown[ idx ] = false;
    treeURL [ idx ] = url;
    treeWMS_id [ idx ] = wms_id;
     idx++;
//     if(url != ''){
//      parent.loadImage(name);
//     }
  }
function handleClick(wms,layer){
	var array_wms = new Array();
	var array_layer = new Array();
	array_wms = wms.split(",");
	array_layer = layer.split(",");
	var myObj;
	var ind = parent.getMapObjIndexByName(treetarget);
	for(var i=0; i<array_wms.length; i++){
		var indwms = parent.getWMSIndexById(treetarget,array_wms[i]);
		for(var j=0; j<parent.mb_mapObj[ind].wms[indwms].objLayer.length; j++){
			myObj = parent.mb_mapObj[ind].wms[indwms].objLayer[j]; 
			if(myObj.layer_name == array_layer[i] && myObj.layer_metadataurl != ""){
				window.open(myObj.layer_metadataurl);
			}
		}
	}
}
function handleLayer(obj){
	var params = obj.value.split("###");
	var array_wms = new Array();
	var array_layer = new Array();
	array_wms = params[0].split(",");
	array_layer = params[1].split(",");
	var type = params[2];
	if(obj.checked == true){ var status = '1';}
	else{var status = '0';}
	//handleSelectedLayer_array(mapObj, array_wms, array_layer, type, status)
	parent.handleSelectedLayer_array(treetarget, array_wms, array_layer, 'visible', status);
	parent.handleSelectedLayer_array(treetarget, array_wms, array_layer, 'querylayer', status);
}
var cBox = new Array();
function setObjArray(){
	for (var i=0; i< document.getElementsByTagName("input").length; i++){
		var temp = document.getElementsByTagName("input")[i].value.split("###");
		array_wms = temp[0].split(",");
		array_layer = temp[1].split(",");
		var ind = cBox.length;
		cBox[ind] = new Array();
		cBox[ind]['id'] = document.getElementsByTagName("input")[i].id;
		cBox[ind]['wms'] = new Array();
		cBox[ind]['layer'] = new Array();
		cBox[ind]['wms'] = array_wms;
		cBox[ind]['layer'] = array_layer;
	}
}
function mb_getLayerObjByName(fname,wms_id,layer_name){
	var ind = parent.getMapObjIndexByName(fname);
	var wmsInd = parent.getWMSIndexById(fname,wms_id);
	var t = parent.mb_mapObj[ind].wms[wmsInd];
	for(var i=0; i < t.objLayer.length; i++){
		if(t.objLayer[i].layer_name == layer_name){
			return t.objLayer[i];
		}
	}
}
function checkLayer(){
	var checkit;
	for(var i=0; i<cBox.length; i++){
		checkit = true;
		for(var j=0; j<cBox[i]['wms'].length;j++){
			var obj = mb_getLayerObjByName(treetarget,cBox[i]['wms'][j],cBox[i]['layer'][j]);
			if(obj){
				if(obj.gui_layer_visible == '0' || obj.gui_layer_visible == 0){
					checkit = false;
				}
			}
			else{
				alert(cBox[i]['wms'][j]+" / "+cBox[i]['layer'][j]+"not defined.");
			}
		}
		if(checkit){
			document.getElementById(cBox[i]['id']).checked = true;
		}
		else{
			document.getElementById(cBox[i]['id']).checked = false;
		}
	}
}
  function initDiv ( )
  {
  document.writeln("<span class='header'><?php echo  $language[MSG53] ?></span>");
    if ( isDOM || isDomIE )
    {
      divPrefix='<DIV CLASS="sitemap" style="position:absolute; left:0; top:0; visibility:hidden;" ID="sitemap'
      divInfo='<DIV CLASS="sitemap" style="position:absolute; visibility:visible" ID="sitemap'
    }
    else
    {
      divPrefix='<DIV CLASS="sitemap" ID="sitemap'
      divInfo='<DIV CLASS="sitemap" ID="sitemap'
    }
    document.writeln( divInfo +  'info">Bitte haben Sie etwas Geduld.<BR>&nbsp;<BR>Es werden die Eintr&auml;ge aus<BR>&nbsp;<BR>der Datenbank initialisiert.</DIV> ' );
	 document.writeln("<form name='treeForm'>");
    for ( var i=1; i<idx; i++ )
    {
	 
      // linked Name ? 
      if ( treeUrl[i] != '' ){
      linkedName = "<input type='checkbox' id='c_"+i+"' value='"+treeWMS_id[i]+"###"+treeUrl[i]+"' onClick='handleLayer(this)'>";
      //linkedName += "<input type='checkbox' id='q"+i+"' value='"+treeWMS_id[i]+"###"+treeUrl[i]+"###querylayer' onClick='handleLayer(this.value)'>";
      linkedName += "<IMG SRC='../img/tree/1w.gif' BORDER='0' WIDTH='3'>";
      linkedName += "<A onclick='handleClick(\""+treeWMS_id[i]+"\",\""+treeUrl[i]+"\")' style='cursor:pointer'>" + treeName[i] + "</A>";
      }
      else{
        linkedName =  '<IMG SRC="../img/tree/1w.gif" BORDER="0" WIDTH="3">' + treeName[i];
       }
      // don't link folder icon if node has no sons
      if ( i == idx-1 || treeP_id[i+1] != treeId[i] ) {
        if ( treeDeep[ i ] == 0 )
          folderImg = '<IMG ALIGN="BOTTOM" SRC="../img/tree/file_empty.gif" BORDER="0" HEIGHT="16" WIDTH="1" HSPACE="0">'
        else
          folderImg = ''
      } else {
        folderImg = '<A HREF="javascript:sitemapClick(' + treeId[i] + ')"><IMG ALIGN="BOTTOM" SRC="../img/tree/folder_off.gif" BORDER="0" NAME="folder' + treeId[i] + '" HEIGHT="16" WIDTH="30" HSPACE="0"></A>'
      }
      // which type of file icon should be displayed?
      if ( treeP_id[i] != 0 )
      {
        if ( lastEntryInFolder( treeId[i] ) )
          fileImg = '<IMG ALIGN="BOTTOM" SRC="../img/tree/file_last.gif" BORDER="0" NAME="file'
            + treeId[i] + '" HEIGHT="16" WIDTH="30" HSPACE="0">'  
        else    
          fileImg = '<IMG ALIGN="BOTTOM" SRC="../img/tree/file.gif" BORDER="0" NAME="file'
            + treeId[i] + '" HEIGHT="16" WIDTH="30" HSPACE="0">'  
      }
      else
        fileImg = ''
      // traverse parents up to root and show vertical lines if parent 
      // is not the last entry on this layer
      verticales = ''
      for( var act_id=treeId[i] ; treeDeep[ id2treeIndex[ act_id ] ] > 1;  )
      {  
        act_id = treeP_id[ id2treeIndex[ act_id ]]
        if ( lastEntryInFolder( act_id ) )
        {
          verticales = '<IMG ALIGN="BOTTOM" SRC="../img/tree/file_empty.gif" BORDER="0" HEIGHT="16" WIDTH="30" HSPACE="0">' + verticales
        }
        else
        {
          verticales = '<IMG ALIGN="BOTTOM" SRC="../img/tree/file_vert.gif" BORDER="0" HEIGHT="16" WIDTH="30" HSPACE="0">' + verticales
        }
      }

      
      document.writeln( divPrefix + treeId[i] + '"><NOBR>&nbsp;' + verticales + fileImg + folderImg + linkedName + '</NOBR></DIV>'
      )  
    }
	  document.writeln("</form>");
  }

  function initStyles ( )
  {
    document.writeln( '<STYLE TYPE="text/css">' + "\n" + '<!--' )
    for ( var i=1,y=y0; i<idx; i++ )
    {  
      document.writeln( '#sitemap' + treeId[i] + ' {POSITION: absolute; VISIBILITY: hidden;}' )
      if ( treeIsOn[ id2treeIndex[ treeP_id[i] ] ] )
        y += deltaY
    }
    document.writeln( '#sitemapinfo {POSITION: absolute; VISIBILITY: visible;}' )
    document.writeln( '//-->' + "\n" + '</STYLE>' )
  }



  function sitemapClick( id )
  {
    var i = id2treeIndex[ id ]

    if ( treeIsOn[ i ] )
    // close directory
    {
      // mark node as invisible
      treeIsOn[ i ]=false
      // mark all sons as invisible
      actDeep = treeDeep[ i ]
      for( var j=i+1; j<idx && treeDeep[j] > actDeep; j++ )
      {
        treeWasOn[ j ] = treeIsOn[ j ]
        treeIsOn[ j ]=false
      }
      gif_off( id )
    }
    else
    // open directory
    { 
      treeIsOn[ i ]=true
      // remember and restore old status
      actDeep = treeDeep[ i ]
      for( var j=i+1; j<idx && treeDeep[j] > actDeep; j++ )
      {
        treeIsOn[ j ] = treeWasOn[ j ]
      }
      gif_on( id )
    }
    showTree()
  }

  function knotDeep( id )
  {
    var deep=0
    while ( true )
      if ( treeP_id[ id2treeIndex[id] ] == 0 )
        return deep
      else
      {
        ++deep
        id = treeP_id[ id2treeIndex[id] ]
      }
    return deep  
  }

  function initTree( id )
  {
    treeIsOn[ id2treeIndex[id] ] = true
    if ( treeTyp[ id2treeIndex[id] ] != 'b' )
      gif_on( id ) 
    while ( treeP_id[ id2treeIndex[id] ] != 0 )
    {
      id = treeP_id[ id2treeIndex[id] ]
      treeIsOn[ id2treeIndex[id] ] = true
      if ( treeTyp[ id2treeIndex[id] ] != 'b' )
        gif_on( id ) 
    }
  }

  function lastEntryInFolder( id )
  {
    var i = id2treeIndex[id]
    if ( i == idx-1 )
      return true
    if ( treeTyp[i] == 'b' )
    {
      if ( treeP_id[i+1] != treeP_id[i] )
        return true
      else 
        return false
    }
    else
    {
      var actDeep = treeDeep[i]
      for( var j=i+1; j<idx && treeDeep[j] > actDeep ; j++ )
      ;
      if ( j<idx && treeDeep[j] == actDeep )
        return false
      else
        return true
    }
  }

  function showTree()
  {
    for( var i=1, y=y0, x=x0; i<idx; i++ )
    {
      if ( treeIsOn[ id2treeIndex[ treeP_id[i] ] ] )
      {
        // show current node
        if ( !(y == treeLastY[i] && treeIsShown[i] ) )
        {
          showLayer( "sitemap"+ treeId[i] ) 
          setyLayer( "sitemap"+ treeId[i], y )
          treeIsShown[i] = true
        } 
        treeLastY[i] = y
        y += deltaY
      }
      else
      {
        // hide current node and all sons
        if ( treeIsShown[ i ] )
        {
          hideLayer( "sitemap"+ treeId[i] ) 
          treeIsShown[i] = false
        }
      }
    }
  }

  function initIndex() {
    for( var i=0; i<idx; i++ )
      id2treeIndex[ treeId[i] ] = i
  }

  function gif_name (name, width, height) {
    this.on = new Image (width, height);
    this.on.src ="../img/tree/" +  name + "_on.gif"
    this.off = new Image (width, height);
    this.off.src ="../img/tree/" +  name + "_off.gif"
  }

  function load_gif (name, width, height) {
    gif_name [name] = new gif_name (name,width,height);
  }

  function load_all () {
    load_gif ('folder',30,16)
    file_last = new Image( 30,16 )
    file_last.src = "file_last.gif"
    file_middle = new Image( 30,16 )
    file_middle.src = "file.gif"
    file_vert = new Image( 30,16 )
    file_vert.src = "file_vert.gif"
    file_empty = new Image( 30,16 )
    file_empty = "file_empty.gif"
  }

  function gif_on ( id ) {
    eval("document['folder" + id + "'].src = gif_name['folder'].on.src")
  }

  function gif_off ( id ) {
    eval("document['folder" + id + "'].src = gif_name['folder'].off.src")
  }
 
  // global configuration
  var deltaX = 30
  var deltaY = 16
  var x0 = 5
  var y0 = 20
  var defaultTarget = '_blank'

  var browserName = navigator.appName;
  var browserVersion = parseInt(navigator.appVersion);
  var isIE = false;
  var isNN = false;
  var isDOM = false;
  var isDomIE = false;
  var isDomNN = false;
  var layerok = false;

  var isIE = browserName.indexOf("Microsoft Internet Explorer" )==-1?false:true;
  var isNN = browserName.indexOf("Netscape")==-1?false:true;
  var isOpera = browserName.indexOf("Opera")==-1?false:true;
  var isDOM = document.getElementById?true:false;
  var isDomNN = document.layers?true:false;
  var isDomIE = document.all?true:false;

  if ( isNN && browserVersion>=4 ) layerok=true;
  if ( isIE && browserVersion>=4 ) layerok=true;
  if ( isOpera && browserVersion>=5 ) layerok=true;

    
  function hideLayer(layerName) {
    if (isDOM)
      document.getElementById(layerName).style.visibility="hidden"
    else if (isDomIE)
      document.all[layerName].style.visibility="hidden";
    else if (isDomNN) 
      document.layers[layerName].visibility="hidden";
  }

  function showLayer(layerName) {
    if (isDOM)
      document.getElementById(layerName).style.visibility="visible"
    else if (isDomIE)
      document.all[layerName].style.visibility="visible";
    else if (isDomNN)
      document.layers[layerName].visibility="visible";
  }

  function setyLayer(layerName, y) {
    if (isDOM)
      document.getElementById(layerName).style.top=y + "px";
    else if (isDomIE)
      document.all[layerName].style.top=y + "px";
    else if (isDomNN)
      document.layers[layerName].top=y + "px";
  }

  var id2treeIndex = new Array()

  // the structure is easy to understand with a simple example
  // p_id is the id of the parent
  // E0                                      ( id=0,p_id=-1 )
  //          E11                            ( id=1,p_id=0)
  //                     E111                ( id=2,p_id=1 )
  //                     E112                ( id=3,p_id=1 )
  //          E12                            ( id=4,p_id=0 )
  //                     E121                ( id=5,p_id=4 ) 
  //          E13                            ( id=6,p_id=0 ) 
  //                     E131                ( id=7,p_id=6 ) 
  //                                 E1311   ( id=8,p_id=7 ) 
  //                     E132                ( id=9,p_id=6 ) 
  // this is a multinary tree structure which is easy to
  // populate with database data :)
<?php
$sql = "SELECT id FROM gui_treegde WHERE fkey_gui_id = $1";
// $v and $t will be re-used below!
$guiList = Mapbender::session()->get("mb_user_gui");
$v = array($guiList);
$t = array("s");
$res = db_prep_query($sql, $v, $t);
if(!db_fetch_row($res)){
	$sql = "INSERT INTO gui_treegde(fkey_gui_id, my_layer_title,lft,rgt,layer) VALUES($1, 'new','1','4','')";
	//using $v and $t fom above
	db_prep_query($sql, $v, $t);
	$sql = "INSERT INTO gui_treegde(fkey_gui_id,my_layer_title,lft,rgt,layer) VALUES($1,'new','2','3','')";
	//using $v and $t fom above
	db_prep_query($sql, $v, $t);
}

$sql = "SELECT n.wms_id, n.id, n.my_layer_title, n.lft, n.rgt, n.layer, COUNT(*) AS level1, ((n.rgt - n.lft -1)/2) AS offspring ";
$sql .= "FROM gui_treegde as n, gui_treegde as p WHERE n.lft BETWEEN p.lft AND p.rgt ";
$sql .= " AND n.fkey_gui_id = $1 AND p.fkey_gui_id = $2 ";
$sql .= " GROUP BY n.wms_id, n.lft, n.my_layer_title,  ((n.rgt - n.lft -1)/2) , n.id, n.rgt, n.layer ORDER BY n.lft";
$v = array($guiList, $guiList);
$t = array("s", "s");
$res = db_prep_query($sql, $v, $t);
	echo "function initArray(){";
	echo "Note(0,-1,'','');";
	$cnt = 0;
	
	while(db_fetch_row($res)){
		if(db_result($res, $cnt, "level1") == 1 && db_result($res, $cnt, "offspring") >= 0 ){
			if(count($parent) > 0){unset($parent);}
			$level =  db_result($res, $cnt, "level1");
			$parent[$level+1] = db_result($res, $cnt, "id");
			
			echo "Note(".db_result($res, $cnt, "id").",0,'".db_result($res, $cnt, "my_layer_title")."','".db_result($res, $cnt, "layer")."',".db_result($res, $cnt, "lft");
			if(db_result($res, $cnt, "wms_id") != ''){
				echo ", '".db_result($res, $cnt, "wms_id")."'";
			}
			echo ");";
			}
		/**/
		else if(db_result($res, $cnt, "level1") > $level){
			$level =  db_result($res, $cnt, "level1");
			$parent[$level+1] = db_result($res, $cnt, "id"); 
			echo "Note(".db_result($res, $cnt, "id").",".$parent[$level].",'".db_result($res, $cnt, "my_layer_title")."','".db_result($res, $cnt, "layer")."',".db_result($res, $cnt, "lft");
			if(db_result($res, $cnt, "wms_id") != ''){
				echo ", '".db_result($res, $cnt, "wms_id")."'";
			}
			echo ");";
		}
		/**/
		else if(db_result($res, $cnt, "level1") == $level){
			$level =  db_result($res, $cnt, "level1");
			$parent[$level+1] = db_result($res, $cnt, "id");
			echo "Note(".db_result($res, $cnt, "id").",".$parent[$level].",'".db_result($res, $cnt, "my_layer_title")."','".db_result($res, $cnt, "layer")."',".db_result($res, $cnt, "lft");
			if(db_result($res, $cnt, "wms_id") != ''){
				echo ", '".db_result($res, $cnt, "wms_id")."'";
			}
			echo ");";
		}
		/**/
		else if(db_result($res, $cnt, "level1") < $level){
			$level =  db_result($res, $cnt, "level1");
			$parent[$level + 1] = db_result($res, $cnt, "id"); 
			echo "Note(".db_result($res, $cnt, "id").",".$parent[$level].",'".db_result($res, $cnt, "my_layer_title")."','".db_result($res, $cnt, "layer")."',".db_result($res, $cnt, "lft");
			if(db_result($res, $cnt, "wms_id") != ''){
				echo ", '".db_result($res, $cnt, "wms_id")."'";
			}
			echo ");";
		}
	
		$cnt++;
	}
	echo "treeTyp[0] = 'f'; treeIsOn[0] = true; treeWasOn[0] = true;";
	echo "}";
?>
  function initArray_()
  {
    Note(0,-1,'','')
	  Note(1,0,'Tutorials','')	  	  
    Note(8,1,'HTML','')
    Note(10,8,'SelfHtml','http://www.teamone.de/selfaktuell/') 
	  Note(9,1,'willi','')
    Note(100,9,'SelfHtml','http://www.teamone.de/selfaktuell/')       
	  Note(3,1,'JavaScript','')
    Note(4,3, 'Netscape Guide 1.3','http://developer.netscape.com/docs/manuals/js/client/jsguide/index.htm')
    Note(7,3, 'Introduction to Javascript','http://rummelplatz.uni-mannheim.de/~skoch/js/script.htm')	  
    Note(12,1, 'Perl','')
    Note(14,12, 'Perl Tutorial','http://www.awu.id.ethz.ch/~didi/perl/perl_start.html')
    Note(13,1,'SQL','')
    Note(15,13, 'Introduction to SQL','http://w3.one.net/~jhoffman/sqltut.htm')
	  Note(111,1, 'Introduction to SQL','http://w3.one.net/~jhoffman/sqltut.htm')
    Note(2,0, 'Reference Manuals','')
    Note(11,2, 'HTML Version 3.2 Referenz','http://www.cls-online.de/htmlref/index.htm')
    Note(6,2,'Netscape Reference 1.3','http://developer.netscape.com/docs/manuals/js/client/jsref/index.htm')
    Note(17,2,'PHP Manual','http://www.php.net/manual/html/')	  
    treeTyp[0] = 'f'
    treeIsOn[0] = true
    treeWasOn[0] = true
  }

  var idx=0
  initArray()
  initIndex()
  load_all()
  for( i=1; i<idx; i++ )
  {
    treeDeep[i] = knotDeep( treeId[i] )
    if ( treeDeep[i] == 0 )
      treeIsShown[i] = true
  }
  if ( isDomNN )
    initStyles();
  //-->  
  </SCRIPT>
</HEAD>
<BODY VLINK="#000000" ALINK="#000000" LINK="#000000" BGCOLOR="#ffffff" TEXT="#000000"
 onLoad="if (layerok) showTree();" 
 >
 <!-- onkeydown="parent.keyhandler('FolderFrame')"-->
<SCRIPT language="JavaScript1.2">
<!--
  initDiv();
  hideLayer("sitemapinfo");
  setObjArray();
//-->
</SCRIPT>
</BODY>
</HTML>
