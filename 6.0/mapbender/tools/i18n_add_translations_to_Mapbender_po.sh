if [ $# -lt 1 ]
then
	echo "$0 error: Please enter the path to the script that you want to check for new translations."
	echo "Enter the path like this: http/php/mb_listGUIs.php"
	echo "f.e.: ./i18n_add_translations_to_Mapbender_po.sh [path to the file]"	
	echo "f.e.: ./i18n_add_translations_to_Mapbender_po.sh http/php/mb_listGUIs.php"
else
	xgettext -p ../resources/locale/bg_BG/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po bg_BG"

	xgettext -p ../resources/locale/de_DE/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po de_DE"

	xgettext -p ../resources/locale/es_ES/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po es_ES"

	xgettext -p ../resources/locale/fr_FR/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po fr_FR"

	xgettext -p ../resources/locale/el_GR/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po el_GR"

	xgettext -p ../resources/locale/hu_HU/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po hu_HU"

	xgettext -p ../resources/locale/it_IT/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po it_IT"

	xgettext -p ../resources/locale/pt_PT/LC_MESSAGES/ -o Mapbender.po -L php -j --keyword=_mb -n --from-code 	utf-8 ../$1
	echo "$1: new Translations added to Mapbender.po pt_PT"
fi


