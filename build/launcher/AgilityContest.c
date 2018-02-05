/*
* C version of AgilityContest.bat in a desesperate intent of bypass aVast antivirus sucking
*
* Compile with:
* bash$ i686-w64-mingw32-gcc AgilityContest.c -mwindows -o AgilityContest.exe
*/
#include <sys/types.h>
#include <sys/stat.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <windows.h>
#include <tchar.h>
#include <commctrl.h>
#include <locale.h>

FILE *logFile;
extern HINSTANCE g_hinst;
HWND hwndProgress;

char **split (char *str) {
    char **res= calloc(32,sizeof(char *));
    int i = 1;
    char* hit = str;
    while((hit = strchr(hit, ',')) != NULL) { //Find next delimiter
        //In-place replacement of the delimiter
        *hit++ = '\0';
        //Next substring starts right after the hit
        res[i++] = hit;
    }
    return res;
}

void doLog(char *function, char *msg) {
    fputs(function,logFile);
    fputs(": ",logFile);
    fputs(msg,logFile);
    fputs("\n",logFile);
}

/**
CreateProcess(
    name,cmdline,processAttributes,threadAttributes,inheritHandlers,creationFlags,environment,workingdir,startupInfo,processInfo
*/
int launchAndWait (char *cmd, char *args) {
    doLog("launchAndWait cmd",cmd);
    doLog("launchAndWait args",args);
    PROCESS_INFORMATION ProcessInfo; //This is what we get as an [out] parameter

    STARTUPINFO StartupInfo; //This is an [in] parameter

    ZeroMemory(&StartupInfo, sizeof(StartupInfo));
    StartupInfo.cb = sizeof StartupInfo ; //Only compulsory field

    if(CreateProcess(cmd, TEXT(args), NULL,NULL,FALSE,CREATE_NO_WINDOW,NULL, NULL,&StartupInfo,&ProcessInfo))  {
        WaitForSingleObject(ProcessInfo.hProcess,INFINITE);
        CloseHandle(ProcessInfo.hThread);
        CloseHandle(ProcessInfo.hProcess);
         // MessageBox (NULL, args,"success", MB_OK | MB_ICONINFORMATION);
    } else {
        MessageBox (NULL, args,"failed", MB_OK | MB_ICONINFORMATION);
    }
    return 0;
}

int launchAndForget ( char *cmd, char *args,PROCESS_INFORMATION *ProcessInfo,STARTUPINFO *StartupInfo) {
    doLog("launchAndForget cmd",cmd);
    doLog("launchAndForget args",args);
    if(! CreateProcess(cmd, TEXT(args), NULL,NULL,FALSE,CREATE_NO_WINDOW,NULL, NULL,StartupInfo,ProcessInfo))  {
        MessageBox (NULL, args,"failed", MB_OK | MB_ICONINFORMATION);
    } else {
        CloseHandle(ProcessInfo->hThread);
        CloseHandle(ProcessInfo->hProcess);
    }
    return 0;
}

int first_install() {
    struct stat buffer;
    int         status;
    status = stat("..\\logs\\first_install",&buffer);
    doLog("first_install",(status==0)?"true":"false");
    return (status==0)?1:0; // true on success
}


LRESULT CALLBACK WndProc(HWND hWnd, UINT message, WPARAM wParam, LPARAM lParam) {
    switch(message) {
        case WM_CREATE: {
            //Initialize progress controls
            INITCOMMONCONTROLSEX icce = { sizeof(INITCOMMONCONTROLSEX), ICC_PROGRESS_CLASS };
            InitCommonControlsEx(&icce);
            //Create the inner label
            CreateWindow(
            	"STATIC", "AgilityContest is starting. Please wait", WS_CHILD | WS_VISIBLE, 70, 10, 350, 20,
            	hWnd, (HMENU)1, NULL, NULL
            	);
            //Create the progress control
            hwndProgress = CreateWindow(
            	PROGRESS_CLASS,NULL,WS_CHILD|WS_VISIBLE|PBS_SMOOTH, 10,10,50,20,
            	hWnd, (HMENU)2, NULL, NULL
            	);
            //Set the progress bar range and initial position
            SendMessage(hwndProgress,PBM_SETRANGE32,0,100);
            SendMessage(hwndProgress,PBM_SETPOS,0,0);
            break;
        }
    	case WM_CLOSE:
			DestroyWindow (hWnd);
			break;
		case WM_DESTROY:
			PostQuitMessage (0);
			break;
    }
    return DefWindowProc(hWnd, message, wParam, lParam);
}

//---------------------------------------------------------------------------

int WINAPI WinMain (HINSTANCE hInstance, HINSTANCE hPrevInst, LPTSTR lpCmdLine, int nShowCmd) {

    STARTUPINFO mysqld_si;
    PROCESS_INFORMATION mysqld_pi;
    ZeroMemory( &mysqld_si, sizeof(mysqld_si) );
    mysqld_si.cb = sizeof(mysqld_si);
    ZeroMemory( &mysqld_pi, sizeof(mysqld_pi) );

    STARTUPINFO apache_si;
    PROCESS_INFORMATION apache_pi;
    ZeroMemory( &apache_si, sizeof(apache_si) );
    apache_si.cb = sizeof(apache_si);
    ZeroMemory( &apache_pi, sizeof(apache_pi) );

/*  // removed because depends on external libraries that may not exist
    INITCOMMONCONTROLSEX icc;
    // Initialise common controls.
    icc.dwSize = sizeof(icc);
    icc.dwICC = ICC_WIN95_CLASSES;
    InitCommonControlsEx(&icc);
*/
    CONST HANDLE handlers[] = { mysqld_pi.hProcess,apache_pi.hProcess };

    logFile=fopen(".\\logs\\startup.log","w");

    // @echo off
    char *set_lang="Hello World!\n";
    doLog("init",set_lang);
    // call settings.bat
    // settings.bat sets default language. So just parse and setenv
    FILE *f=fopen(".\\settings.bat","r");
    if (f) {
        // trick translate "SET LANG=es_ES to" "es-ES" for using setlocale()
        set_lang=calloc(32,sizeof(char));
        char *tmp=calloc(32,sizeof(char));
        fgets(set_lang,31,f);
        fclose(f);
        char *eol=strchr(set_lang,'\n');
        if (eol) *eol='\0'; // remove newline at end of string
        strncpy(tmp,set_lang,32);
        for (char* p=tmp;*p;p++) { if (*p=='_') *p='-'; } // translate es_ES to es-ES
        char *locale=1+strchr(tmp,'=');
        doLog("setlocale",locale);
    }

    // cd /d %~dp0\xampp
    // rem echo AgilityContest Launch Script
    // set working directory to ${cwd}\xampp
    char *wd=calloc(256,sizeof(char)); // enought to store current and new directory
    getcwd(wd,255);
    strncat(wd,"\\xampp",250);
    chdir(wd);
    doLog("chdir",wd);

    // presenta mensaje de arranque...
    // char *cmd="start \"\" /B mshta \"javascript:var sh=new ActiveXObject( 'WScript.Shell' ); sh.Popup( 'AgilityContest is starting. Please wait', 20, 'Working...', 64 );close()\"";
    // system(cmd);
    WNDCLASSEX wc; /* A properties struct of our window */

    /* zero out the struct and set the stuff we want to modify */
    memset(&wc,0,sizeof(wc));
    wc.cbSize = sizeof(WNDCLASSEX);
    wc.lpfnWndProc = WndProc; /* This is where we will send messages to */
    wc.hInstance = hInstance;
    wc.hCursor = LoadCursor(NULL, IDC_ARROW);

    /* White, COLOR_WINDOW is just a #define for a system color, try Ctrl+Clicking it */
    wc.hbrBackground = (HBRUSH)(COLOR_WINDOW+1);
    wc.lpszClassName = "WindowClass";
    wc.hIcon = LoadIcon(NULL, IDI_APPLICATION); /* Load a standard icon */
    wc.hIconSm = LoadIcon(NULL, IDI_APPLICATION); /* use the name "A" to use the project icon */
    RegisterClassEx(&wc);
    // main window
    HWND hwndParent=CreateWindow(wc.lpszClassName,"Starting",WS_OVERLAPPEDWINDOW|WS_VISIBLE,100,100,450,100,0,0,hInstance,NULL);
    MSG  msg;
    for(int n=0; n<5;n++) { // dirty trick, but works just enought to handle initial windowOpenEvent (no more interaction)
        if (PeekMessage(&msg, hwndParent, 0, 0,PM_REMOVE)!=0) {
            TranslateMessage(&msg);
            DispatchMessage(&msg);
        } else {
            sleep(1);
        }
    }

    // rem notice that this may require admin privileges
    // rem for windows 8 and 10 disable w3svc service
    // rem also configure firewall to allow http https and mysql
    // net stop W3SVC
    char *cmd="C:\\Windows\\system32\\net.exe";
    char *args="C:\\Windows\\system32\\net.exe stop W3SVC";
    launchAndWait(cmd,args);
    SendMessage(hwndProgress,PBM_SETPOS,10,0);

    // netsh advfirewall firewall add rule name=\"MySQL Server\" action=allow protocol=TCP dir=in localport=3306
    cmd="C:\\Windows\\system32\\netsh.exe";
    args="C:\\Windows\\system32\\netsh.exe advfirewall firewall add rule name=\"MySQL Server\" action=allow protocol=TCP dir=in localport=3306";
    launchAndWait(cmd,args);

    SendMessage(hwndProgress,PBM_SETPOS,20,0);
    // netsh advfirewall firewall add rule name=\"Apache HTTP Server\" action=allow protocol=TCP dir=in localport=80
    cmd="C:\\Windows\\system32\\netsh.exe";
    args="C:\\Windows\\system32\\netsh.exe advfirewall firewall add rule name=\"Apache HTTP Server\" action=allow protocol=TCP dir=in localport=80";
    launchAndWait(cmd,args);
    SendMessage(hwndProgress,PBM_SETPOS,30,0);

    // netsh advfirewall firewall add rule name=\"Apache HTTPs Server\" action=allow protocol=TCP dir=in localport=443
    cmd="C:\\Windows\\system32\\netsh.exe";
    args="C:\\Windows\\system32\\netsh.exe advfirewall firewall add rule name=\"Apache HTTPs Server\" action=allow protocol=TCP dir=in localport=443";
    launchAndWait(cmd,args);
    SendMessage(hwndProgress,PBM_SETPOS,40,0);

    /* on first install set properly php environment (paths, configs and so ) */
    if ( first_install() ) {
        // rem if required prepare portable xampp to properly setup directories
        // if not exist ..\logs\first_install GOTO mysql_start
        // rem echo Configuring first boot of XAMPP
        // set PHP_BIN=php\php.exe
        // set CONFIG_PHP=install\install.php
        // %PHP_BIN% -n -d output_buffering=0 -q %CONFIG_PHP% usb >nul
        char *php=calloc(32+strlen(wd),sizeof(char));
        char *phpargs=calloc(256+strlen(wd),sizeof(char));
        sprintf(php,"%s\\php\\php.exe",wd);
        sprintf(phpargs,"%s\\php\\php.exe -n -d output_buffering=0 -q install\\install.php usb >nul",wd);
        launchAndWait(php,phpargs);
        SendMessage(hwndProgress,PBM_SETPOS,50,0);
    }

    // rem start mysql database server
    // :mysql_start
    // rem echo MySQL Database is trying to start
    // rem echo Please wait  ....
    // start "" /B mysql\bin\mysqld --defaults-file=mysql\bin\my.ini --standalone --console >nul
    // rem timeout  5
    // ping -n 5 127.0.0.1 >nul
    char *mysqld=calloc(32+strlen(wd),sizeof(char));
    char *mysqldargs=calloc(256+strlen(wd),sizeof(char));
    sprintf(mysqld,"%s\\mysql\\bin\\mysqld.exe",wd);
    sprintf(mysqldargs,"--defaults-file=mysql\\bin\\my.ini --standalone --console");
    launchAndForget(mysqld,mysqldargs,&mysqld_pi,&mysqld_si);
    SendMessage(hwndProgress,PBM_SETPOS,60,0);
    // system("start \"\" /B mysql\\bin\\mysqld --defaults-file=mysql\\bin\\my.ini --standalone --console >nul");
    sleep(7);

    // rem start apache web server
    // :apache_start
    // rem echo Starting Apache Web Server....
    // start "" /B apache\bin\httpd.exe >nul
    // rem timeout  5
    // ping -n 5 127.0.0.1 >nul
    char *apache=calloc(32+strlen(wd),sizeof(char));
    char *apacheargs=calloc(256+strlen(wd),sizeof(char));
    sprintf(apache,"%s\\apache\\bin\\httpd.exe",wd);
    sprintf(apacheargs," ");
    launchAndForget(apache,apacheargs,&apache_pi,&apache_si);
    SendMessage(hwndProgress,PBM_SETPOS,70,0);
    // system("start \"\" /B apache\\bin\\httpd.exe");
    sleep(7);

    /* create database and basic data on first install */
    // if not exist ..\logs\first_install GOTO browser_start
    if ( first_install() ) {
        // rem echo Creating AgilityContest Databases. Please wait
        //timeout /t 5
        char *buff=calloc(1024,sizeof(char));
        FILE *f=fopen("..\\logs\\install.sql","w");
        FILE *u=fopen("..\\extras\\users.sql","r");
        sleep(5); // extra timeout for let mysqld extra time to start

        // echo DROP DATABASE IF EXISTS agility; > ..\logs\install.sql
        // echo CREATE DATABASE agility; >> ..\logs\install.sql
        // echo USE agility; >> ..\logs\install.sql
        // rem type ..\extras\agility.sql >> ..\logs\install.sql
        // type ..\extras\users.sql >> ..\logs\install.sql
        fputs("DROP DATABASE IF EXISTS agility;\n",f);
        fputs("CREATE DATABASE agility;\n",f);
        fputs("USE AGILITY;\n",f);
        while(fgets(buff,1024,u)!=NULL) fputs(buff,f);
        fclose(u);
        fclose(f);

        // rem on first run create database and database users
        // mysql\bin\mysql -u root < ..\logs\install.sql
        char *mysql=calloc(32+strlen(wd),sizeof(char));
        char *mysqlargs=calloc(256+strlen(wd),sizeof(char));
        sprintf(mysql,"%s\\mysql\\bin\\mysql.exe",wd);
        sprintf(mysqlargs,"%s\\mysql\\bin\\mysql.exe -u root < ..\\logs\\install.sql",wd);
        launchAndWait(mysql,mysqlargs);

        SendMessage(hwndProgress,PBM_SETPOS,80,0);
    }

    /*
    del ..\logs\install.sql
    del ..\logs\first_install
    rem echo Opening AgilityContest console for first time...
    start /MAX "AgilityContest" https://localhost/agility/console/index.php?installdb=1
    goto wait_for_end
    rem normal start when database is installed
    :browser_start
    rem echo Opening AgilityContest console...
    start /MAX "AgilityContest" https://localhost/agility/console
    */
    char *browser=calloc(256,sizeof(char));
    if (first_install() ) {
        sprintf(browser,"%s && start /MAX \"AgilityContest\" https://localhost/agility/console/index.php?installdb=1",set_lang);
    } else {
        sprintf(browser,"%s && start /MAX \"AgilityContest\" https://localhost/agility/console",set_lang);
    }
    // del ..\logs\install.sql
    // del ..\logs\first_install
    unlink("..\\logs\\install.sql");
    unlink("..\\logs\\first_install.sql");
    doLog("system",browser);
    system(browser);
    SendMessage(hwndProgress,PBM_SETPOS,90,0);

    // :wait_for_end
    // exit
    doLog("wait","");
    fclose (logFile);
    WaitForMultipleObjects ( 2,handlers,1,INFINITE);
    return 0;
}

