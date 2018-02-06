AgilityContest.exe: AgilityContest.o resources.o
	i686-w64-mingw32-gcc AgilityContest.o resources.o -mwindows -o AgilityContest.exe -l comctl32

resources.o: agilitycontest.ico resources.rc resources.h AgilityContest.exe.manifest
	i686-w64-mingw32-windres resources.rc -O coff -o resources.o

AgilityContest.o: AgilityContest.c
	i686-w64-mingw32-gcc -Wall -O2 -c AgilityContest.c

clean:
	rm -f AgilityContest.o resources.o AgilityContest.exe

install: AgilityContest.exe
	cp AgilityContest.exe ../../AgilityContest.exe
