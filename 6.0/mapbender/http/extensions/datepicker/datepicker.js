var monthDays=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
var currDate=new Date();
var showDate=new Date();
var target = window.opener.dTarget;

showDate.setDate(1);

function writeY() {
  for(i=2000; i<2021; i++) {
	document.write('<option value='+i+'>'+i+'<\/option>');
  }
}

function writeM() {
  for(i=0; i<12; i++) {
		var j = i + 1;
		document.write('<option value='+j+'>'+monthNames[i]+'</option>');
  }
}

function picker() {
  document.frm.lYears.selectedIndex = showDate.getFullYear() - 2000;
  document.frm.lMonths.selectedIndex = showDate.getMonth();
  var d = lenM(showDate);
  var y = showDate.getFullYear() - 2000;
	var begin = showDate.getDay() - 1;
	if(begin < 0) begin += 7;
  for(i=0; i<42; i++) {
    btn=document.frm.elements['btn'+(i+1)];
    if(i<begin) {
      btn.value='';
    }
    else if(i>=begin+d) {
      btn.value='';
    }
    else {
      btn.style.color='black';
      btn.value=i-begin+1;
    }
  }
}

function go(x){
	var out;

	if(x){
		if (x =='x'){
			out = twoDigits(currDate.getDate()) + '.' + (twoDigits(currDate.getMonth()+1)) + '.' + currDate.getFullYear();
		}
		else{ 		
  		out = twoDigits(x) + '.' + (twoDigits(showDate.getMonth()+1)) + '.' + showDate.getFullYear();
		}
		target.value = out;
		close();
 	}
}

function twoDigits(x){
	x = '0' + x;
	return x.match(/\d\d$/);
}

function lenM(dt) {
  var m = dt.getMonth();
	var d = monthDays[m];
  if(m == 1 && !(dt.getFullYear() % 4)) {
    d=29;
  }
  return d;
}

function setDate(y, m) {
  showDate.setFullYear(y);
  showDate.setMonth(m);
}

function selMonth(m) {
  showDate.setMonth(m);
  picker();
}

function selYear(y) {
  showDate.setFullYear(y+2000);
  picker();
}

function prevMonth() {
  var m=showDate.getMonth()-1;
  var y=showDate.getFullYear();
  if(m<0) {
    m=11;
    y--;
    if(y<2000) y=2020;
  }
  setDate(y, m);
  picker();
}

function nextMonth() {
  var m=showDate.getMonth()+1;
  var y=showDate.getFullYear();
  if(m>11) {
    m=0;
    y++;
    if(y>2020) y=2000;
  }
  setDate(y, m);
  picker();
}

