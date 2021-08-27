function renderPage() {
    // indicar tamaño de la cuadricula
    var g= {'cols':1280, 'rows':720};
    // doLayout( g,'<identificador de elemento>', <columna>,<fila>,<ancho>,<alto>);
    /* informacion de la prueba */
    doLayout(g,'#pp_header_label',      10,     10,     200,    25);
    doLayout(g,'#pp_NombrePrueba_label',10,     150,     140,     25);
    doLayout(g,'#pp_NombrePrueba',      220,    645,     600,    25);
    doLayout(g,'#pp_NombreJornada_label',10,    180,    140,     25);
    doLayout(g,'#pp_NombreJornada',     1100,    10,    600,    25);
    doLayout(g,'#pp_NombreManga_label', 10,     210,    140,     25);
    doLayout(g,'#pp_NombreManga',       300,    580,    600,    25);
    doLayout(g,'#pp_NombreRing',        10,     240,    200,    25);

    /* logotipo del club */
    doLayout(g,'#pp_Logo',              52,     542,    150,    150);
    /* informacion del perro */
    doLayout(g,'#pp_Timestamp_label',   10,     370,    140,     25);
    doLayout(g,'#pp_Timestamp' ,        150,    370,    600,    25);
    doLayout(g,'#pp_Drs_label',         10,     400,    140,     25);
    doLayout(g,'#pp_Drs',               150,    400,    600,    25);
    doLayout(g,'#pp_Nombre_label',      10,     430,    140,     25);
    doLayout(g,'#pp_Nombre',            550,    595,    600,    25);
    doLayout(g,'#pp_NombreLargo_label', 10,     460,    140,     25);
    doLayout(g,'#pp_NombreLargo',       150,    460,    600,    25);
    doLayout(g,'#pp_NombreGuia_label',  10,     490,    140,     25);
    doLayout(g,'#pp_NombreGuia',        250,    610,    600,    25);
    doLayout(g,'#pp_NombreClub_label',  10,     520,    140,     25);
    doLayout(g,'#pp_NombreClub',        150,    520,    600,    25);
    doLayout(g,'#pp_NombreEquipo_label',10,     550,    140,     25);
    doLayout(g,'#pp_NombreEquipo',      150,    550,    600,    25);
    doLayout(g,'#pp_Categoria_label',   10,     580,    140,     25);
    doLayout(g,'#pp_Categoria',         150,    580,    600,    25);
    doLayout(g,'#pp_Grado_label',       10,     610,    140,     25);
    doLayout(g,'#pp_Grado',             150,    610,    600,    25);

    /* informacion del cronometro */
    doLayout(g,'#pp_Flt_label',         800,     520,    140,     25);
    doLayout(g,'#pp_Flt',               950,    520,    300,    25);
    doLayout(g,'#pp_Reh_label',         800,     550,    140,     25);
    doLayout(g,'#pp_Reh',               950,    550,    300,    25);
    doLayout(g,'#pp_Toc_label',         800,     580,    140,     25);
    doLayout(g,'#pp_Toc',               950,    580,    300,    25);
    doLayout(g,'#pp_Eli_label',         800,     610,    140,     25);
    doLayout(g,'#pp_Eli',               950,    610,    300,    25);
    doLayout(g,'#pp_NPr_label',         800,     640,    140,     25);
    doLayout(g,'#pp_NPr',               950,    640,    300,    25);
    doLayout(g,'#pp_Tim_label',         800,     670,    140,     25);
    doLayout(g,'#pp_Tim',               950,    670,    300,    25);
}

/**
 * Used to evaluate position, width and heigh on an element to be
 * layed out in a grid
 * @param g grid size { cols, rows }
 * @param id id of element to be layed out
 * @param x start col
 * @param y start row
 * @param w nuber of cols
 * @param h number of rows
 */
function doLayout(g,id,x,y,w,h) {
    var elem=$(id);
    elem.css('display','inline-block');
    elem.css('position','absolute');
    elem.css('float','left');
    // elem.css('padding','5px');
    elem.css('-webkit-box-sizing','border-box');
    elem.css('-moz-box-sizing','border-box');
    elem.css('box-sizing','border-box');
    elem.css('left',  ((25+x*100)/g.cols)+'%');
    elem.css('top',   ((100+y*100)/g.rows)+'%');
    elem.css('width', ((w*100)/g.cols)+'%');
    elem.css('height',((h*100)/g.rows)+'%');
    // elem.css('line-height',((h*100)/dg.rows)+'%');
    // elem.css('vertical-align','bottom');
}

