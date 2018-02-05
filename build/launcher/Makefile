AgilityContest.exe: AgilityContest.o AgilityContest.res
	i686-w64-mingw32-gcc AgilityContest.o AgilityContest.res -mwindows -o AgilityContest.exe -l comctl32

AgilityContest.res: agilitycontest.ico AgilityContest.rc AgilityContest.exe.manifest
	i686-w64-mingw32-windres AgilityContest.rc -O coff -o AgilityContest.res

AgilityContest.o: AgilityContest.c
	i686-w64-mingw32-gcc -Wall -O2 -c AgilityContest.c

clean:
	rm -f AgilityContest.o AgilityContest.res AgilityContest.exe

install: AgilityContest.exe
	cp AgilityContest.exe ../../AgilityContest.exe
