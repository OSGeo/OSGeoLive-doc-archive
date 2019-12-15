"""
Script to replace "image" files which contain filenames
with the actual image file
See symlink information relating to these
files at https://lists.osgeo.org/pipermail/osgeolive/2019-December/014520.html

This creates lots of duplicate image files, but fixes missing images
in the archive docs, and is simpler than trying to implement
cross-platform symlinks in git
"""

import os
from shutil import copyfile
from pathlib import Path

def create_image_copy(fn):

    try:
        with open(fn, "r") as f:
            image_file = f.read()
            fld = os.path.dirname(fn)
            src = os.path.join(fld, image_file)
            assert os.path.isfile(src)
            extension = os.path.splitext(src)[1]
            assert extension in [".png", ".jpg", ".gif"]
            copyfile(src, fn)
            print("Copying {}".format(src))
    except (UnicodeDecodeError, ValueError):
        pass # is an image or binary file
        # ValueError: stat: embedded null character in path
    except AssertionError:
        print("{} failed".format(fn))
    except:
        print("Unknown error for {}".format(fn))
        raise

folders = [
    r"D:\GitHub\OSGeoLive-doc\6.0\_images",
    r"D:\GitHub\OSGeoLive-doc\6.5\_images",
    r"D:\GitHub\OSGeoLive-doc\7.0\_images",
    r"D:\GitHub\OSGeoLive-doc\7.9\_images",
    r"D:\GitHub\OSGeoLive-doc\8.0\_images",
    r"D:\GitHub\OSGeoLive-doc\8.5\_images",
    r"D:\GitHub\OSGeoLive-doc\9.0\_images",
    r"D:\GitHub\OSGeoLive-doc\9.5\_images",
    r"D:\GitHub\OSGeoLive-doc\10.0\_images",
    r"D:\GitHub\OSGeoLive-doc\10.5\_images"
]

for fld in folders:
    for fn in Path(fld).rglob('*.*'):
        create_image_copy(fn)

print("Done!")
