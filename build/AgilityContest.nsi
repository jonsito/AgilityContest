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
  !include "MUI.nsh"
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
OutFile ${PROGRAM_NAME}-${VERSION}${TIMESTAMP}.exe

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
InstallDir "\AgilityContest"

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

Section "Programs"
StrCpy $PATH "${PROGRAM_NAME}"
StrCpy $PATH_ACCESO_DIRECTO "${PROGRAM_NAME}"
SetOutPath $INSTDIR

;Incluimos todos los ficheros que componen nuestra aplicacion
File AgilityContest.bat
File License.txt
File COPYING
FILE README.md
File /r agility
File /r docs
FILE /r extras
FILE /r logs
FILE /r xampp
SetOutPath $INSTDIR

;Hacemos que la instalacion se realice para todos los usuarios del sistema
SetShellVarContext all
;Creamos los directorios, acesos directos y claves del registro que queramos...
    CreateDirectory "$SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO"
    CreateShortCut "$SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO\AgilityContest.lnk" \
                    "$INSTDIR\$PATH\AgilityContest.bat" "" \
                    "$INSTDIR\$PATH\extras\AgilityContest.ico" 0 SW_SHOWMINIMIZED

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
	; directorio de logs
    AccessControl::GrantOnFile "$INSTDIR\logs" "(S-1-5-11)" "GenericRead + GenericWrite + Delete"
	; Access control for image logos
    AccessControl::GrantOnFile "$INSTDIR\agility\images\logos" "(S-1-5-11)" "GenericRead + GenericWrite + Delete"
	; Access control for configuration files
	AccessControl::GrantOnFile "$INSTDIR\agility\server\auth" "(S-1-5-11)" "GenericRead + GenericWrite + Delete"
    Pop $0 ; get "Marker" or error msg
    StrCmp $0 "Marker" Continue
    MessageBox MB_OK|MB_ICONSTOP "Error setting access control for AgilityContest directories: $0"
    Pop $0 ; pop "Marker"
    Continue:
SectionEnd

; Optional section (can be disabled by the user)
Section "Desktop Shortcut"
	; set up paths to tell app where to be invoked from
        SetOutPath $INSTDIR
	SetShellVarContext all
        CreateShortcut "$DESKTOP\AgilityContest.lnk" \
                       "$INSTDIR\AgilityContest.bat" "" \
                       "$INSTDIR\extras\AgilityContest.ico" 0 SW_SHOWMINIMIZED
SectionEnd

;;;;;;;;;;;;;;;;;;;;;;
; Uninstall settings ;
;;;;;;;;;;;;;;;;;;;;;;

Section "Uninstall"
        StrCpy $PATH "${PROGRAM_NAME}"
        StrCpy $PATH_ACCESO_DIRECTO "${PROGRAM_NAME}"
        SetShellVarContext all
	; make sure to preserve user config for versions <=1.17
        RMDir /r $SMPROGRAMS\AgilityContest\$PATH_ACCESO_DIRECTO
        RMDir /r $INSTDIR\$PATH
	RMDir /r $APPDATA\AgilityContest\$PATH
        Delete "$INSTDIR\uninstall_AgilityContest.exe"
	Delete "$DESKTOP\AgilityContest.lnk"
        DeleteRegKey HKLM SOFTWARE\AgilityContest\$PATH
        DeleteRegKey HKLM \
            Software\Microsoft\Windows\CurrentVersion\Uninstall\$PATH
SectionEnd

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Funcion que detecta si hay una version previa instalada             ;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

Function .onInit
 
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
  ; TODO: backup session files
  ClearErrors
  ; invoke uninstaller
  ExecWait '$R0 /S _?=$INSTDIR' ;Do not copy the uninstaller to a temp file
     
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

Function .onMouseOverSection
    FindWindow $R0 "#32770" "" $HWNDPARENT
    GetDlgItem $R0 $R0 1043

    StrCmp $0 0 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:Programs (Required)"

    StrCmp $0 1 "" +2
        SendMessage $R0 ${WM_SETTEXT} 0 "STR:Desktop icon (Optional)"

FunctionEnd
