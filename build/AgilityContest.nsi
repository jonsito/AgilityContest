;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Instalador para AgilityContest
; Juan Antonio Martinez
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;Definimos el valor de la variable VERSION, en caso de no definirse 
; en el script podria ser definida en el compilador
!define PROGRAM_NAME "AgilityContest"
!define VERSION __VERSION__
!define TIMESTAMP __TIMESTAMP__

;--------------------------------
;Include Modern UI
    !include "MUI2.nsh"
    !include "Sections.nsh"

;Include String functions to check version
  !include "WordFunc.nsh"
  !insertmacro VersionCompare

;Seleccionamos el algoritmo de compresion utilizado para comprimir 
;nuestra aplicacion
SetCompressor lzma

;--------------------------------
; Con esta opcion alertamos al usuario cuando pulsa el boton cancelar 
; y le pedimos confirmacion para abortar la instalacion
;Esta macro debe colocarse en esta posicion del script sino no funcionara
  !define mui_abortwarning

;--------------------------------
; definimos la imagen de la pagina de bienvenida del instalador
    !define MUI_ICON "${NSISDIR}/Contrib/Graphics/Icons/modern-install.ico"
    !define MUI_UNICON "${NSISDIR}/Contrib/Graphics/Icons/modern-uninstall.ico"
    !define MUI_WELCOMEFINISHPAGE_BITMAP "wellcome.bmp"
    !define MUI_UNWELCOMEFINISHPAGE_BITMAP "wellcome.bmp"
    !define MUI_HEADERIMAGE
    !define MUI_HEADERIMAGE_BITMAP "installer.bmp" ; optional

;--------------------------------
;Pages

  ;Mostramos la pagina de bienvenida 
  !insertmacro MUI_PAGE_WELCOME 
  ;Pagina donde mostramos el contrato de licencia 
  !insertmacro MUI_PAGE_LICENSE "License.txt" 
  ;pagina donde se muestran las distintas secciones definidas 
  !insertmacro MUI_PAGE_COMPONENTS 
  ;pagina donde se selecciona el directorio donde instalar nuestra aplicacion 
  !insertmacro MUI_PAGE_DIRECTORY 
  ;pagina de instalacion de ficheros 
  !insertmacro MUI_PAGE_INSTFILES
  ;pagina final
  !insertmacro MUI_PAGE_FINISH

;paginas referentes al desinstalador
!insertmacro MUI_UNPAGE_WELCOME
!insertmacro MUI_UNPAGE_CONFIRM
!insertmacro MUI_UNPAGE_INSTFILES
!insertmacro MUI_UNPAGE_FINISH

;--------------------------------
;Languages

!insertmacro MUI_LANGUAGE "English"

; Para generar instaladores en diferentes idiomas podemos escribir lo siguiente:
;  !insertmacro MUI_LANGUAGE ${LANGUAGE}
; De esta forma pasando la variable LANGUAGE al compilador podremos generar
;paquetes en distintos idiomas sin cambiar el script

;;;;;;;;;;;;;;;;;;;;;;;;;
; Configuracion General ;
;;;;;;;;;;;;;;;;;;;;;;;;;
Var PATH
Var PATH_ACCESO_DIRECTO
Var installedVersion

;Nuestro instalador se llamara si la version fuera la 1.0: Ejemplo-1.0-win32.exe
OutFile ${PROGRAM_NAME}-${VERSION}-${TIMESTAMP}.exe

;Aqui comprobamos que en la version Inglesa se muestra correctamente el mensaje:
;Welcome to the $Name Setup Wizard
;Al tener reservado un espacio fijo para este mensaje, y al ser
;la frase en espanol mas larga:
; Bienvenido al Asistente de Instalacion de Aplicacion $Name
; no se ve el contenido de la variable $Name si el tamano es muy grande
Name ${PROGRAM_NAME}
Caption "${PROGRAM_NAME} ${VERSION} Setup"

# Icon .\icons\extras\logo.ico

;Comprobacion de integridad del fichero activada
CRCCheck on
;Estilos visuales del XP activados
XPStyle on

# tambien comprobamos los distintos
; tipos de comentarios que nos permite este lenguaje de script

;Indicamos cual sera el directorio por defecto donde instalaremos nuestra
;aplicacion, el usuario puede cambiar este valor en tiempo de ejecucion.
InstallDir "C:\AgilityContest"

; check if the program has already been installed, if so, take this dir
; as install dir
InstallDirRegKey HKLM SOFTWARE\AgilityContest\${PROGRAM_NAME} "Install_Dir"
;Mensaje que mostraremos para indicarle al usuario que seleccione un directorio
DirText "Setup will install ${PROGRAM_NAME} in the following folder. To install in a different forlder, click Browse an select another folder. Click Install to start the installation."
;Indicamos que cuando la instalacion se complete no se cierre el instalador automaticamente
AutoCloseWindow false
;Mostramos todos los detalles del la instalacion al usuario.
ShowInstDetails show
;En caso de encontrarse los ficheros se sobreescriben
SetOverwrite on
;Optimizamos nuestro paquete en tiempo de compilacion, es altamente recomendable habilitar siempre esta opcion
SetDatablockOptimize on
;Habilitamos la compresion de nuestro instalador
SetCompress auto
;Personalizamos el mensaje de desinstalacion
UninstallText "${PROGRAM_NAME} will be uninstalled from the following folder. Click Uninstall to start the uninstallation"

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Install settings                                                    ;
; En esta seccion anadimos los ficheros que forman nuestra aplicacion ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

Section "!AgilityContest App"
StrCpy $PATH "${PROGRAM_NAME}"
StrCpy $PATH_ACCESO_DIRECTO "${PROGRAM_NAME}"
SetOutPath $INSTDIR

;Incluimos todos los ficheros que componen nuestra aplicacion
File AgilityContest.exe
File License.txt
File COPYING
FILE README.md
FILE Contributors
File /r agility
File /r docs
FILE /r extras
FILE /r logs
FILE /r xampp

;Hacemos que la instalacion se realice para todos los usuarios del sistema
SetShellVarContext all
;Creamos los directorios, acesos directos y claves del registro que queramos...
    CreateDirectory "$SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO"
    CreateShortCut "$SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO\AgilityContest.lnk" \
                    "$INSTDIR\AgilityContest.exe" "" \
                    "$INSTDIR\extras\AgilityContest.ico" 0 SW_SHOWNORMAL

;Datos del registr de Windows
    WriteRegStr HKLM \
            SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\$PATH \
            "DisplayName" "${PROGRAM_NAME} ${VERSION}"
    WriteRegStr HKLM \
            SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\$PATH \
            "UninstallString" '"$INSTDIR\uninstall_AgilityContest.exe"'
    WriteUninstaller "uninstall_AgilityContest.exe"

    WriteRegStr HKLM SOFTWARE\AgilityContest\${PROGRAM_NAME} "InstallDir" $INSTDIR
       
    WriteRegStr HKLM SOFTWARE\AgilityContest\${PROGRAM_NAME} "Version" ${VERSION}

; permisos de escritura en determinados directorios
	SetShellVarContext all
    ; logs\first_install file is removed by admin, make sure that std perms works
    AccessControl::GrantOnFile "$INSTDIR\logs" "(S-1-5-11)" "FullAccess"
	; Access control for image logos
    AccessControl::GrantOnFile "$INSTDIR\agility\images\logos" "(S-1-5-11)" "GenericRead + GenericWrite + Delete"
	; Access control for configuration files
	AccessControl::GrantOnFile "$INSTDIR\agility\server\auth" "(S-1-5-11)" "GenericRead + GenericWrite + Delete"
SectionEnd

; Optional section (can be disabled by the user)
Section "Desktop Shortcut" desk
	; set up paths to tell app where to be invoked from
    SetOutPath $INSTDIR
	SetShellVarContext all
    CreateShortcut "$DESKTOP\AgilityContest.lnk" \
                       "$INSTDIR\AgilityContest.exe" "" \
                       "$INSTDIR\extras\AgilityContest.ico" 0 SW_SHOWNORMAL
SectionEnd

Section "Initial language: Spanish" esp
    SetOutPath $INSTDIR
    File /oname=settings.bat settings_es.bat
SectionEnd

Section /o "Initial language: English" eng
    SetOutPath $INSTDIR
    File /oname=settings.bat settings_en.bat
SectionEnd

Section /o "Initial language: German" ger
    SetOutPath $INSTDIR
    File /oname=settings.bat settings_de.bat
SectionEnd

Section /o "Initial language: Hungarian" hun
    SetOutPath $INSTDIR
    File /oname=settings.bat settings_hu.bat
SectionEnd

Section /o "Initial language: Portuguese" prt
    SetOutPath $INSTDIR
    File /oname=settings.bat settings_pt.bat
SectionEnd

;;;;;;;;;;;;;;;;;;;;;;
; Uninstall settings ;
;;;;;;;;;;;;;;;;;;;;;;

Section "Uninstall"
    StrCpy $PATH "${PROGRAM_NAME}"
    StrCpy $PATH_ACCESO_DIRECTO "${PROGRAM_NAME}"
    SetShellVarContext all
    ; on uninistall do not preserve license and config
    ; as we are uninstalling... :-).... but make sure installer does it on reinstall
    ;
    ; make sure that application is stopped
    Exec '"$INSTDIR\xampp\apache\bin\pv.exe" -f -k httpd.exe -q'
    Exec '"$INSTDIR\xampp\apache\bin\pv.exe" -f -k mysqld.exe -q'
    RMDir /r $SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO
    RMDir /r $INSTDIR
    Delete "$INSTDIR\uninstall_AgilityContest.exe"
	Delete "$DESKTOP\AgilityContest.lnk"
    DeleteRegKey HKLM SOFTWARE\AgilityContest\$PATH
    DeleteRegKey HKLM \
        Software\Microsoft\Windows\CurrentVersion\Uninstall\$PATH
SectionEnd

;; recuperamos ficheros de configuracion tras la instalacion
Function .onInstSuccess
  ; recuperamos ficheros de configuracion de la desinstalacion previa
  CopyFiles $TEMP\registration.info $INSTDIR\agility\server\auth
  CopyFiles $TEMP\config.ini $INSTDIR\agility\server\auth
  ; recuperamos backups
  CopyFiles $TEMP\agility-*.sql $INSTDIR\logs
FunctionEnd

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Funcion que detecta si hay una version previa instalada             ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

Function .onInit

  ; Preserve configuration and backup files (if exists).
  ; Make sure previous temp config is deleted
  Delete $TEMP\config.ini
  CopyFiles $INSTDIR\agility\server\auth\registration.info $TEMP
  CopyFiles $INSTDIR\agility\server\auth\config.ini $TEMP
  CopyFiles $INSTDIR\logs\agility-*.sql $TEMP
  StrCpy $1 ${esp} ; Spanish is selected by default

  ReadRegStr $R0 HKLM \
  "Software\Microsoft\Windows\CurrentVersion\Uninstall\${PROGRAM_NAME}" \
  "UninstallString"
  StrCmp $R0 "" done

  ReadRegStr $R1 HKLM SOFTWARE\${PROGRAM_NAME} "Version"
  StrCpy $installedVersion "$R1"
  
  MessageBox MB_OKCANCEL|MB_ICONEXCLAMATION \
  "${PROGRAM_NAME} $installedVersion is currently installed. $\n\
  Press OK to remove and install ${PROGRAM_NAME} ${VERSION} $\n\
  Or select `Cancel` to abort and keep current configuration " \
  IDOK uninst
  Abort
 
;Run the uninstaller
uninst:
  ; make sure that application is stopped before uninstall/reinstall
  ifFileExists "$INSTDIR\xampp\apache\bin\pv.exe" 0 dontExecKillProc
  nsExec::Exec '"$INSTDIR\xampp\apache\bin\pv.exe" -f -k httpd.exe -q'
  nsExec::Exec '"$INSTDIR\xampp\apache\bin\pv.exe" -f -k mysqld.exe -q'

  dontExecKillProc:

  ClearErrors
  ; invoke uninstaller without /S (silent) option
  ExecWait '$R0 _?=$INSTDIR' ;Do not copy the uninstaller to a temp file
     
  IfErrors no_remove_uninstaller done
    ;You can either use Delete /REBOOTOK in the uninstaller or add some code
    ;here to remove the uninstaller. Use a registry key to check
    ;whether the user has chosen to uninstall. If you are using an uninstaller
    ;components page, make sure all sections are uninstalled.
  no_remove_uninstaller:

done:
                         
FunctionEnd

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;; show section descriptions
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

Function .onSelChange

  !insertmacro StartRadioButtons $1
    !insertmacro RadioButton ${esp}
    !insertmacro RadioButton ${eng}
    !insertmacro RadioButton ${ger}
    !insertmacro RadioButton ${hun}
    !insertmacro RadioButton ${prt}
  !insertmacro EndRadioButtons

FunctionEnd

Function .onMouseOverSection
    FindWindow $R0 "#32770" "" $HWNDPARENT
    GetDlgItem $R0 $R0 1043

    StrCmp $0 0 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:AgilityContest Suite (Required)"

    StrCmp $0 1 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:Desktop icon (Optional)"

    StrCmp $0 2 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:FirstBoot in Spanish. You may change later"

    StrCmp $0 3 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:FirstBoot in English. You may change later"

    StrCmp $0 4 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:FirstBoot in German. You may change later"

    StrCmp $0 5 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:FirstBoot in Hungarian. You may change later"

    StrCmp $0 6 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:FirstBoot in Portuguese. You may change later"

FunctionEnd
