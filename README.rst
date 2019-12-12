OSGeoLive Documentation Archive
===============================

About OSGeoLive
---------------

OSGeoLive_ is a self-contained bootable DVD, USB thumb drive or Virtual
Machine based on Lubuntu, that allows you to try a wide variety of open source
geospatial software without installing anything. It is composed entirely of
free software, allowing it to be freely distributed, duplicated and passed
around.

General Notes
-------------

This branch contains all documentation releases of OSGeoLive_. 

The archive is stored in the ``gh-pages`` branch of the ``OSGeoLive-doc`` repository. While the overall size of the repository is greatly increased ``git pull`` will
only download this branch if explicitly requested. 

In the future new releases could be automatically added to the archive via Travis. 

Extraction Process
------------------

ISOs were downloaded from https://sourceforge.net. This contains an archive of all archives from v2.0 to the present release, for example https://master.dl.sourceforge.net/project/osgeo-live/2.0/arramagong-livedvd-2.0.3-Final.iso
The "mini" ISO versions were used as the full versions have been removed from SourceForge. The documentation in both however is identical. 

Older ISOs (v2.0 to v5.0) could be extracted using 7Zip and a docs folder was contained in the root. 

Versions v6.0 to v.8.5 required unzipping, and then further extracting the files from ``casper/filesystem.squashfs``. Once this was extracted (to a ``filesystem`` folder) files were found in ``casper/filesystem/var/www/html``

From v9.0 onwards ``casper/filesystem.squashfs`` was extracted and the docs contained in
``casper/filesystem/usr/share/doc/osgeolive-docs/html``. 

Archive Compilation
-------------------

* Seth Girvin `@geographika <https://github.com/geographika>`_

.. _OSGeoLive: https://live.osgeo.org