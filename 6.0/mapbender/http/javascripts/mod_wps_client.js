
//make this php ?

$('<div id="wpsClientDialog" title="WPS Client">\n\
<style> fieldset ul { list-style-type: none;  padding: 0em }\n\
		fieldset li {padding-left: 0em; border-bottom: 1px dotted gray; }\n\
		fieldset li label { min-width: 17em; display: inline-block;}\n\
		fieldset li img { height: 1em; width: 1em;}\n\
		fieldset input { width: 10em; }\n\
		.validationError { visibility: hidden; color: red;}\n\
}</style>\n\
<form>\n\
 <fieldset>\n\
 <ul>\n\
  <li>\n\
  	<label for="wps_interpol">interpolation system</label> \n\
	<select id="wps_interpol">\n\
		<option value="psgp">psgp</option>\n\
		<option value="automap">automap</option>\n\
		<option value="transGaussian">transGaussian</option>\n\
		<option value="copula">copula</option>\n\
		<option value="idw">idw</option>\n\
		<option value="automatic">automatic</option>\n\
	</select>\n\
  </li>\n\
<li> <label for="wps_maxcalctime">max. calculation time (minutes)</label><input id="wps_maxcalctime" type="text" class="text" /></li>\n\
</ul>\n\
 </fieldset>\n\
 <fieldset>\n\
<ul>\n\
	<li> <label for="wps_mean"><input id="wps_mean" type="checkbox" checked="checked" disabled="disabled">mean</label>\n\
	<img id="wps_cs_mean" src="../img/colorscheme_default.png" />\n\
	<select id="wps_selectcs_mean">\n\
		<option value="default" selected="selected" >default</option>\n\
		<option value="2"> colorscheme 2</option>\n\
		<option value="3"> colorscheme 3</option>\n\
	</select>\n\
	</li>\n\
 	<li><label for="wps_variance"><input id="wps_variance" type="checkbox" checked="checked" >variance</label>\n\
	<img id="wps_cs_mean" src="../img/colorscheme_default.png" />\n\
	<select id="wps_selectcs_variance">\n\
		<option value="default" selected="selected" >default</option>\n\
		<option value="2"> colorscheme 2</option>\n\
		<option value="3"> colorscheme 3</option>\n\
	</select>\n\
	</li>\n\
 	<li><label for="wps_propability"><input id="wps_propability" type="checkbox" checked="checked" >propability</label>\n\
	<img id="wps_cs_propability" src="../img/colorscheme_default.png" />\n\
	<select id="wps_selectcs_propability">\n\
		<option value="default" selected="selected" >default</option>\n\
		<option value="2"> colorscheme 2</option>\n\
		<option value="3"> colorscheme 3</option>\n\
	</select>\n\
	</li>\n\
	<li> <label for="wps_propability_limit">limit <span class="validationError">need a number</span></label><input id="wps_propability_limit" type="text" class="text"/></li>\n\
</ul>\n\
</fieldset>\n\
<fieldset>\n\
<ul>\n\
	<li><label for="wps_clipping"><input id="wps_clipping" type="checkbox" checked="checked" >clipping</label></li>\n\
	<li><label form="wps_image_format">format</label><select id="wps_image_format"><option selected="selected">image/jpeg</option><option>image/png</option></select></li>\n\
</ul>\n\
 </fieldset>\n\
</form>\n\
</div>').dialog({
		bgiframe: true,
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {
			"Save": function() {
		/*	
 *				unklar:
				$parameters['colorschema'] = isset($parameters['colorschema']) ? $parameters['colorschema'] : "";
		*/
				var attributes = {
					interpolationMethod: "automatic",
					calculationTime: 12000,
					propabilityLimit: 0,
					clipping: true,
					imageFormat:'image/jpeg',
					predictionTypes: "Mean",
					bbox:  "",
					bboxSRS:"",
					mean_color: 'default',
					variance_color: 'default',
					propability_color: 'default',
				};
				
				attributes.interpolationMethod = $('#wps_interpol').val();
				attributes.calculationTime 	= $('#wps_maxcalctime').val();
							
				attributes.predictionTypes = (!!$('#wps_mean').attr('checked'))? "Mean,":"";
				attributes.predictionTypes += (!!$('#wps_variance').attr('checked'))? "Variance,":""
				attributes.predictionTypes += (!!$('#wps_propability').attr('checked'))? "Propability,":""
				//cut off trailing ,
				attributes.predictionTypes = attributes.predictionTypes.slice(0,attributes.predictionTypes.length -1)
			
		
				attributes.mean_color		= $('#wps_selectcs_mean').val();
				attributes.variance_color	= $('#wps_selectcs_variance').val();
				attributes.propability_color= $('#wps_selectcs_propability').val();
				
				attributes.propabilityLimit 	= $('#wps_propability_limit').val();
				
				attributes.clipping 	= (!!$('#wps_clipping').attr('checked'))? true: false;
				attributes.imageFormat 		= $('#wps_image_format').val();

				attributes.bbox = mb_mapObj[0].getExtent();
				attributes.bboxSRS = mb_mapObj[0].getSRS();
				
				
				//validation
				var numeric = /[0-9]+/
				if (!attributes.propabilityLimit.match(numeric)){
					$('[for=wps_propability_limit] span.validationError').css('visibility','visible');
					return;
				}else{
					$('[for=wps_propability_limit] span.validationError').css('visibility','hidden');
				}
				var req = new Mapbender.Ajax.Request({
					url: "../php/mod_wps.php",
					method: "createWPSRequest",
					parameters : {
					  attributes:attributes,
					},
					callback: (function(result,success,message){ 
						$('<div><textarea><![CDATA['+ result  + ']]></textarea></div>').dialog({autoOpen: true, height: 200, width:300});
					 })
				}); 
				req.send();

				$(this).dialog('close');
				$('#wpsClientDialog form')[0].reset() 
				// also reset colorscheme indicators
				$('#wpsClientDialog form img').attr('src','../img/colorscheme_default.png');
				$('span.validationError').css('visibility','hidden');
			},
			"Cancel": function() {
				$(this).dialog('close');
				$('#wpsClientDialog form')[0].reset() 
				// also reset colorscheme indicators
				$('#wpsClientDialog form img').attr('src','../img/colorscheme_default.png');
				$('span.validationError').css('visibility','hidden');
			}
}})

var change_cs_indicator = function(){
	var map = {
		csdefault: '../img/colorscheme_default.png',
		cs2: 		 '../img/colorscheme_2.png',
		cs3: 		 '../img/colorscheme_3.png'
	};
	var key = 'cs'+$(this).val();
	try{
		var src = map[key];
	} catch (e){
		var src = map['csdefault'];
	}
	$(this).prev().attr('src',src);
};
$('#wps_selectcs_mean').change(change_cs_indicator );
$('#wps_selectcs_variance').change(change_cs_indicator );
$('#wps_selectcs_propability').change(change_cs_indicator );

$('#wps_interpol').change((function(){
	if($(this).val() == 'idw'){
		$('#wps_variance').attr('checked','');
		$('#wps_variance').attr('disabled','disabled');
	}else{
		$('#wps_variance').attr('checked','checked');
		$('#wps_variance').attr('disabled','');
		
	}
}));

$('#wpsClient').click(function(){
	$('#wpsClientDialog').dialog('open');
});
