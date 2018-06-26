AgilityContest.exe: AgilityContest.o resources.o
	i686-w64-mingw32-gcc AgilityContest.o resources.o -mwindows -o AgilityContest.exe -l comctl32

resources.h:
	echo "#define AC_PROGRAM_ICON  101" > resources.h
	echo "#define AC_PROGRAM_INFO  1"	>> resources.h
	echo "#define AC_MANIFEST 1  /* CREATEPROCESS_MANIFEST_RESOURCE_ID */ "	>> resources.h
	echo "#define AC_PRODUCT_VERSION_COMMA $(AC_VERSION),0" | sed -e 's/\./,/g' >> resources.h
	echo "#define AC_PRODUCT_VERSION_DOT \"$(AC_VERSION).0\"" >> resources.h

resources.o: agilitycontest.ico resources.rc resources.h AgilityContest.exe.manifest
	i686-w64-mingw32-windres resources.rc -O coff -o resources.o

AgilityContest.o: AgilityContest.c
	i686-w64-mingw32-gcc -Wall -O2 -c AgilityContest.c

clean:
	rm -f AgilityContest.o resources.o resources.h AgilityContest.exe

install: AgilityContest.exe
	cp AgilityContest.exe ../../AgilityContest.exe
