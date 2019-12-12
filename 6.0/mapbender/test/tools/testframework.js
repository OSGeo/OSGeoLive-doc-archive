
prefix = environment['user.dir'];
load(prefix + "/tools/qunit.js");

var xmloutput = false;
//parse arguments
for(var i in arguments){
  if(arguments[i] == "--xml"){
    xmloutput = true;
    print("XML Output is not yet supported");
    xmloutput = false; 
  }
}



// overriding stuff to fit env.js
var QUnit = QUnit || {};

if(xmloutput){
  // output junit xml
  // http://mail-archives.apache.org/mod_mbox//ant-dev/200902.mbox/%3Cdffc72020902241548l4316d645w2e98caf5f0aac770@mail.gmail.com%3E
  //http://junitpdfreport.sourceforge.net/manual/junitreport_xslt_description/
var testsuites = {};
var testsuiteCurrent = null;

QUnit.moduleStart = function(name){
  var testsuite = {};
  testsuite.start = new Date().getTime();
  testsuites[name] = testsuite;
  testsuite.tests = {};
  testsuiteCurrent = testsuite;
};

QUnit.moduleDone = function(modulename, failures, total){
  var testsuite = {};
  testsuite.name = modulename;
  testsuite.file = ""; // FIXME: what goes here ?
  testsuite.error = ""; // FIXME: what goes here?
  testsuite.tests = total;
  testsuite.failures = failures;
  testsuite.end = new Date().getTime();
  testsuite.time = testsuite.end - testsuite.start;
};

// <testcase name="testname" class="testsuitename" file="the file being testes" line="line where the test atrts" assertions="the number of assertions" time="time it took to write" >
QUnit.testStart = function(testname){
  var test = {};
  test.name = testname;
  test.start = new Date().getTime();
  testsuiteCurrent.tests[testname] = test;
};

QUnit.testDone = function(testname, failures, total){
  var test = testsuiteCurrent.tests[testname];
  test.classname = testsuiteCurrent.name;
  test.file = "";
  test.assertions=
  test.end = new Date().getTime();
  test.time = test.end - test.start;
};

QUnit.done = function(failures,total){
  var jUnitDoc = "";
  for(testsuitename in testsuites){
    testsuite = testsuites[testsuitename]; 
    jUnitDoc += "\t"+'<testsuite name="' + testsuite.name + '" errors="'+ testsuite.error +'" ';
    jUnitDoc += 'tests="' + testsuite.tests + 'failures="'+ testsuite.failures +'" >';
    for(testname in testsuite){
      test = testsuite[testname];
      jUnitDoc += "\t\t" +'<testcase name="'+ test.name +'" class="'+ testsuite.name + '" time="'+ test.time + '" />';
    }
    jUnitDoc += "</testsuite>";
  }
  print(jUnitDoc);
};


}else{
  QUnit.log = function(result, message){ var resultmsg = result? "OK":"FAIL"; print(resultmsg +" "+  message); };
  QUnit.testStart = function(name) { print("\trunning test " + name); };
  QUnit.testDone = function(name, failures, total){ print("\tTest " + name + " failed " + failures + " of " + total +"\n"); };

  QUnit.moduleStart = function(name){print("running module "+ name);  };
  QUnit.moduleDone = function(name,failures,total){ print("Module "+ name + " failed " + failures + " of " + total + "\n\n");};
  QUnit.done = function(failure, total){ print("Finished. failed " + failure + " of " + total);  };
}
