Procedimiento:

Descomprimir el fichero perroenpista.tgz en la carpeta c:\AgilityContest\agility\livestream,
reemplazando los ficheros que tengan el mismo nombre

Hay que editar dos ficheros. Uno indica el estilo de cada elemento y el otro la posición de éste

- "perroenpista.css", en donde indicaremos:
* El color y la imagen del fondo de pantalla
* Tamaño, font, estilo y color del texto de cada una de las etiquetas
* La visibilidad ( "display:none" ) de cada una de las etiquetas
* En general, cualquier aspecto del estilo de cada uno de los elementos de la página

- "perroenpista_layout.js", en el que:
* Se define una rejilla del mismo tamaño en filas y columnas
  que pixeles tiene la imagen de fondo del apartado anterior
* se indica la posición y tamaño de cada elemento, ( 0,0 -> esquina superior izquierda )
  el formato es columna(x), fila(y), ancho(w), alto(h)

Se ajusta la posición de cada elemento hasta que coincida con el fondo de pantalla seleccionado.
Se debe comprobar que redimensionando la ventana, las posiciones y tamaños relativos se mantienen