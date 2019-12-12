#!/bin/sh

# Usage: test-jquery.sh [version]
# Currently supported versions: 1.4.1, 1.3.2, 1.3.1, and 1.2.6
#
# This script will check out the jQuery development tree from Subversion 
# or github if necessary, massage the testing scripts as necessary, copy 
# our latest version of env.js into place, and then run the test scripts.


if [ -n "$2" ]; then 
    echo 'debug'
    if [ -n "$2" ]; then VERSION="$2"; else VERSION="1.4.1"; fi
    DEBUG=1
else 
    echo 'jquery'
    if [ -n "$1" ]; then VERSION="$1"; else VERSION="1.4.1"; fi
    DEBUG=0
fi

JQUERY_DIR="test/vendor/jQuery/$VERSION";

#ant


case "$VERSION" in
    "1.3.2")
        svn export http://jqueryjs.googlecode.com/svn/tags/$VERSION/ $JQUERY_DIR
        rm -rf "$JQUERY_DIR/test/qunit"
        svn export -r6173 http://jqueryjs.googlecode.com/svn/trunk/qunit $JQUERY_DIR/test/qunit
        ;;
   "1.3.1")
        if [ ! -d "$JQUERY_DIR" ]; then
            svn export http://jqueryjs.googlecode.com/svn/tags/$VERSION/ $JQUERY_DIR
            rm -rf "$JQUERY_DIR/test/qunit"
            svn export -r6133 http://jqueryjs.googlecode.com/svn/trunk/qunit $JQUERY_DIR/test/qunit  
        fi
        ;;
   "1.4.1")
        if [ ! -d "$JQUERY_DIR" ]; then
            echo 'cloning jquery 1.4.1 repo'
            git clone git://github.com/jquery/jquery.git $JQUERY_DIR
            cd $JQUERY_DIR
            git branch jquery-1.4.1 1.4.1
            git checkout jquery-1.4.1
            make
            cd -
        fi
        echo 'running jquery 1.4.1 tests'
        if [ $DEBUG -eq 1 ]; then
            echo 'enabling rhino debugger'
            java  -cp rhino/js.jar  org.mozilla.javascript.tools.debugger.Main bin/jquery-1.4.1-test.js
        else
            echo 'running with rhino'
            java -XX:+HeapDumpOnOutOfMemoryError -jar rhino/js.jar -opt -1 bin/jquery-1.4.1-test.js
        fi
        echo 'completed jquery 1.4.1 tests'
        ;;
esac

#cp dist/env.rhino.js $JQUERY_DIR/build/runtest/env.js
#cp dist/env-js.jar $JQUERY_DIR/build/js.jar
#cp bin/jquery-$VERSION-test.js $JQUERY_DIR/build/runtest/test.js

#if [ $DEBUG -eq 1 ]; then
#    echo 'enabling rhino debugger'
#    perl -pi~ -e "s/^JAR(.*)(-jar.*|-cp.*)/JAR\1 -cp \\$\{BUILD_DIR}\/js.jar org.mozilla.javascript.tools.debugger.Main/" $JQUERY_DIR/Makefile;
#else
#    echo 'running with rhino'
#    perl -pi~ -e "s/^JAR(.*)(-jar.*|-cp.*)/JAR\1 -jar \\$\{BUILD_DIR}\/js.jar/" $JQUERY_DIR/Makefile;
#    java -jar rhino/js.jar bin/
#fi

#cd $JQUERY_DIR
#make runtest
