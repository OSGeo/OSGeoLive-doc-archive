"""
Script to remove all duplicated images from the docs
and replace their links in the HTML files with the path
to the original image
"""
import os
from pathlib import Path
import re


def update_links(folder, replace_pairs):
    """
    Replace src="../_images/autodesk1.jpg" with src="../_images/autodesk.jpg"
    in HTML files
    """

    for fn in Path(folder).rglob("*.html"):
        parent_dir = os.path.dirname(str(fn).replace(folder, ""))

        parts = parent_dir.split("\\")
        if len(parts) <= 1 or len(parts[1]) != 2:  # a language folder
            continue

        # Read the content of the file
        with open(fn, "r", newline="", encoding="utf-8") as f:
            file_content = f.read()

        updated_content = file_content
        update = False

        # Perform the find and replace operation on the file content
        for copy_filename, original_filename in replace_pairs:
            find_str = f"_images/{copy_filename}"
            replace_str = f"_images/{original_filename}"

            # Check if the search string is in the file content
            if find_str in updated_content:
                update = True
                updated_content = updated_content.replace(find_str, replace_str)

        if update is True:
            print(f"Updating {fn}...")
            # Write the modified content back to the file
            with open(fn, "w", newline="", encoding="utf-8") as f:
                f.write(updated_content)


def remove_digits_from_filename(filename):
    # Regular expression pattern to match filenames with digits at the end
    pattern = r"\d+\.\w+$"

    # Use re.sub() to replace the matched pattern with the file extension (e.g., ".png")
    new_filename = re.sub(
        pattern, lambda match: "." + match.group().split(".")[-1], filename
    )

    return new_filename


folders = [
    # r"C:\GitHub\OSGeoLive-doc-archive\6.0\_images", # 529 MB to 146 MB
    # r"C:\GitHub\OSGeoLive-doc-archive\6.5\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\7.0\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\7.9\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\8.0\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\8.5\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\9.0\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\9.5\_images",
    # r"C:\GitHub\OSGeoLive-doc-archive\10.0\_images",
    r"C:\GitHub\OSGeoLive-doc-archive\10.5\_images"  # 1.11 GB to 335 MB
]

# Regular expression pattern to match 1 or 2 digits before the file extension
pattern = r"^.*\d{1,2}(?=\.[^.]+$)"

files_to_delete = []
replace_pairs = []

for fld in folders:
    for fn in Path(fld).rglob("*.*"):
        folder = os.path.dirname(fn)
        basename = os.path.basename(fn)
        if re.match(pattern, basename):
            original_basename = remove_digits_from_filename(basename)
            original_file = os.path.join(folder, original_basename)
            print(fn)
            print(original_file)
            if os.path.isfile(original_file) is True:
                # only update links where the file is an exact copy
                # as there are cases where cartaro_edit_map.png and cartaro_edit_map2.png
                # are different (original) files
                if os.path.getsize(original_file) == os.path.getsize(fn):
                    replace_pairs.append((basename, original_basename))
                    files_to_delete.append(fn)

    root_folder = os.path.dirname(fld)
    update_links(root_folder, replace_pairs)

print("Removing files...")
for f in files_to_delete:
    os.remove(f)

print("Done!")
