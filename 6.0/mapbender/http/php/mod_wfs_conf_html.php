<?php
function myNl2br ($str) {
	return preg_replace('#\r?\n#', '\\n', $str);
}

$textAreaCode = <<<CODE
<textarea cols='' rows='' id=''>
</textarea>
CODE;

$datepickerCode = <<<CODE
<input type='text' id='' class='hasdatepicker' />
CODE;

$selectCode = <<<CODE
<select id=''>
 <option>Auswahl...</option>
 <option value=''></option>
 <option value=''></option>
 <option value=''></option>
</select>
CODE;

$checkboxCode = <<<CODE
<input type='checkbox' id='' value='1'>
CODE;

$templateOptionArray = array(
	"none" => array(
		"label" =>"--", 
		"code" => ""
	),
	"datepicker" => array(
		"label" => "Datepicker", 
		"code" => $datepickerCode
	),
	"textarea" => array(
		"label" => "Textarea", 
		"code" => $textAreaCode
	),
	"select" => array(
		"label" =>"Select",
		"code" => $selectCode
	),
	"checkbox" => array(
		"label" => "Checkbox",
		"code" => $checkboxCode
	)
);
?>