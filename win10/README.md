To cleanup the original .wim file ...
emerge wimlib
wimupdate win10_boot_64_en.wim 2 << EOF
delete --force /setup.exe
delete --force --recursive /Sources
delete --force --recursive /sources
EOF

# extract wim to get diff from base
mkdir idx1; wimapply win10_boot_64_en.wim 1 idx1
mkdir idx2; wimapply win10_boot_64_en.wim 2 idx2

# Generate cleanuplist for some WinSxS as well
diff -u -r idx* | grep SxS: | sed 's,SxS: amd64,SxS/amd64,g' | sed 's,^Only in idx2,delete --force --recursive ,g' | wimupdate win10_boot_64_en.wim 2

wimexport win10_boot_64_en.wim 2 win10_boot_64_en.wim.new --boot --rebuild

move as needed
# This should give clean-ish win10 PE

create startnet.cmd
remember to run unix2dos on it.

place background as \Windows\System32\setup.bmp, winpe.jpg and winre.jpg
In the sources of the setup, replace background_cli.bmp (use pbrush to save it in the existing format)
Then there is also spwizimg.dll which can be modified

