/**
 * Admin function to load catalog into DB
 * @param caps_url
 * @return
 */
function mod_CSW_load_catalog(caps_url){
	var capUrl = "../php/mod_createJSObjFromXML.php?" + mb_session_name + "=" + mb_nr + "&caps=" + encodeURIComponent(caps);
	window.frames['loadData'].document.location.href = capUrl;
}