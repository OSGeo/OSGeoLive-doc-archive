#!/bin/bash
TMPDIR=`mktemp -d`
OLDPWD=$PWD

echo $TMPDIR
svn export . $TMPDIR/mapbender

cd $TMPDIR
rm -rf mapbender/build mapbender/test
rm -rf mapbender/resources/mapbender-2.6-i386

NaturalDocs --exclude-input mapbender/http/extensions/  --exclude-input mapbender/resources --exclude-input mapbender/tools -i mapbender/ -o html mapbender/documents/api_js -p mapbender/documents/api_js 

zip -r mapbender.zip mapbender/

cd $OLDPWD
cp $TMPDIR/mapbender.zip .
rm -rf $TMPDIR
