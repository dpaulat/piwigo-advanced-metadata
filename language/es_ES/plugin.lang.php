<?php

$lang['Grum Plugin Classes is not installed'] = 'El plugin <b>Grum Plugin Classes</b> no esta instalado';

$lang['amd_title_page'] = 'Gestión avanzada de los métadatos';
$lang['g003_version'] = 'v';

$lang['g003_error_invalid_ajax_call'] = "Llamada de función invalida";

$lang['g003_stat'] = "Estadísticas";
$lang['g003_metadata'] = "Métadatos";
$lang['g003_database'] = "Référencial";
$lang['g003_status'] = "Estatuto";
$lang['g003_show'] = "Consultar";

$lang['g003_performances'] = "Rendimiento";
$lang['g003_setting_nb_items_per_request'] = "Numero de imágenes analizadas por demandas";
$lang['g003_apply'] = "Afficher en écriture latineAplicar";

$lang['g003_numberOfAnalyzedPictures'] = "%d imágenes han sido objeto de un análisis y representan %d métadatos";
$lang['g003_numberOfNotAnalyzedPictures'] = "%d imagenes no han sido objecto de un análisis";
$lang['g003_analyze_not_analyzed_pictures'] = "El análisis se centra en las imágenes que nunca han sido analizadas y se añaden al repositorio actual";
$lang['g003_analyze_all_pictures'] = "El análisis se centra en todas las imágenes de la galería y sustituye al repositorio actual";
$lang['g003_analyze_caddie_add_pictures'] = "El análisis se centra en las imágenes de la cesta y se suma a la del repositorio actual";
$lang['g003_analyze_caddie_replace_pictures'] = "El análisis se centra en las imágenes de la cesta y simplemente reemplaza el repositorio actual";
$lang['g003_analyze'] = "Afficher en écriture latineAnalizar";
$lang['g003_update_metadata'] = "Actualizar el repositorio de metadatos";
$lang['g003_status_of_database'] = "Estado del repositorio";
$lang['g003_updating_metadata'] = "Actualización del repositorio";
$lang['g003_analyze_in_progress'] = "En tratamiento...";
$lang['g003_analyze_is_finished'] = "Tratamiento terminado";
$lang['g003_loading'] = "Cargando...";
$lang['g003_numberOfPicturesWithoutTags'] = "%d imágenes no tienen metadatos";
$lang['g003_no_items_selected'] = "Ningún metadatos seleccionados";
$lang['g003_selected_tag_isnot_linked_with_any_picture'] = "Los metadatos seleccionados no están vinculados a ninguna imágenes";
$lang['g003_TagId'] = "Metadatos";
$lang['g003_TagLabel'] = "Nombre";
$lang['g003_NumOfImage'] = "Imágenes";
$lang['g003_Pct'] = "%";
$lang['g003_select'] = "Selección";
$lang['g003_display'] = "Afficher en écriture latineMostrar";
$lang['g003_order'] = "Clasificar por";
$lang['g003_filter'] = "Filtrar";
$lang['g003_tagOrder'] = "Metadatos";
$lang['g003_numOrder'] = "Numero de imágenes";
$lang['g003_valueOrder'] = "Valor";
$lang['g003_no_filter'] = "Ningún filtro";
$lang['g003_magic_filter'] = "Magic (metadatos calculados)";
$lang['g003_exclude_unused_tags'] = "Excluir los metadatos sin utilizar ";
$lang['g003_Value'] = "Valor";
$lang['g003_selected_tags_only'] = "Devolver solo los metadatos seleccionados";

$lang['g003_select_metadata'] = " Selección de metadatos";
$lang['g003_display_management'] = "Gestión de la visualización de metadatos";
$lang['g003_number_of_filtered_metadata'] = "Número de metadatos :";
$lang['g003_number_of_distinct_values'] = "Número de valores distintos :";

$lang['g003_click_to_edit_group'] = "Haga clic para editar las propiedades del grupo de metadatos";
$lang['g003_click_to_delete_group'] = "Haga clic para eliminar el grupo de metadatos";
$lang['g003_click_to_manage_group'] = "Haga clic para administrar los elementos del grupo de metadatos";
$lang['g003_click_to_manage_list'] = "Haga clic para añadir o quitar metadatos";
$lang['g003_add_a_group'] = "Añadir un grupo de metadatos";
$lang['g003_adding_a_group'] = "Adición de un grupo de metadatos";
$lang['g003_editing_a_group'] = "Edición de un grupo de metadatos";
$lang['g003_deleting_a_group'] = "Eliminación de un grupo de metadatos";
$lang['g003_new_group'] = "Nuevo grupo de metadatos";
$lang['g003_name'] = "Nombre";
$lang['g003_add_delete_tags'] = "Agregar o quitar metadatos";
$lang['g003_confirm_group_delete'] = "¿Está seguro que desea eliminar el grupo de metadatos %s ?";
$lang['g003_default_group_name'] = "Condiciones de visualización";

$lang['g003_ok'] = "Ok";
$lang['g003_cancel'] = "Cancelar";
$lang['g003_yes'] = "Si";
$lang['g003_no'] = "No";


$lang['g003_invalid_group_id'] = "Identificador de grupo de metadatos no válido";
$lang['g003_no_tag_can_be_selected'] = "No se dispone de los metadatos";

$lang['g003_warning_on_analyze_0'] = "¡Atención !";
$lang['g003_warning_on_analyze_1'] = "La alimentación del repositorio es un proceso que puede llegar a ser largo (hasta varios minutos de tratamiento) y exige muchos recursos en el servidor en función del número de fotos seleccionadas para el análisis.";
$lang['g003_warning_on_analyze_2'] = "Algunos hospedadores pueden sancionar este tipo de uso.";
$lang['g003_warning_on_analyze_3'] = "Es muy recomendable llenar la canasta con cerca de cincuenta imágenes representativas de la galería de fotos para proceder al tratamiento.";




$lang['g003_help_exif'] = "Los metadatos son información EXIF que se almacena en la imagen por la cámara en el momento del disparo.

Cualquier información que se encuentra allí son de carácter técnico:
[ul]
[li]equipo utilizado (modelo de cámara, fabricante)[/li]
[li]las condiciones de disparo (apertura, tiempo de exposición, la distancia focal)[/li]
[li]el momento de los disparos (fecha, hora)[/li]
[li]la ubicación geográfica (datos GPS)[/li]
[li]información sobre el formato de la foto (tamaño, resolución, compresión)[/li]

La alimentación de los metadatos EXIF está normalizada ([url]http://www.exif.org/Exif2-2.PDF[/url]), sin embargo:
[ul]
[li]Este estándar adoptado por la [url=http://www.jeita.or.jp]JEITA[/url] Japan Electronics and Information Technology Industries Association) ya no cambia desde el 2002 [/li]
[li]Todo metadatos definidos en la norma es opcional: todos los dispositivos no informan sobre todos los metadatos[/li]
[li]hay un metadatos [i]MakerNote[/i], que es un campo abierto utilizados por los fabricantes y en el que se almacena información que no está presente en las condiciones (por ejemplo, las referencias al objetivo) esta información es única a cada empresa, ver cada dispositivo. El plugin puede interpretar parte de esta información para los aparatos [b]Pentax[/b], [b]Canon[/b], [b]Nikon[/b].[/Li]
[/ul]";


$lang['g003_help_iptc']="Los metadatos IPTC son informaciónes que son almacenada en la imagen, por el fotógrafo, con un programa adapdado.

La naturaleza de la información contenida allí es esencialmente orientada hacia el mundo profesional
[ul]
[li]referencias del fotógrafo (nombre, contacto)[/li]
[li]información sobre el derechos de autor[/li]
[li]La descripción de la imagen (título, descripción, comentarios, palabras clave)[/li]
[li]una variedad de información relacionada con el mundo profesionales[/li]
[/ul]

La alimentación de los metadatos IPTC esta normalizada ([url]http://www.iptc.org [/url]).
Esta norma ha sido establecida por un consorcio de agencias de noticias más importantes del mundo, la [i]International Press Telecommunications Council[/i] (abreviado como IPTC).";

$lang['g003_03_help_xmp'] = "Los metadatos XMP son esencialmente EXIF e IPTC almacenados en formato XML.

La ventaja de los metadatos XMP es la provisión de flexibilidad:
[ul]
[li]información se pueden almacenar en varios idiomas [/li]
[li]el uso del conjunto de caracteres Unicode permite (principalmente) utilizar caracteres no latinos[/li]
[li]XML facilita la interpretación y el intercambio de información[/li]
[/ul]

La alimentación de los metadatos XMP está normalizado ([url]http://www.metadataworkinggroup.org/specs[/url]).
Es aconsejado utilizar preferentemente el EXIF e IPTC si están presentes.

La conversión de EXIF e IPTC de metadatos XMP se efectúa con el software de edición de fotos.

El modelo XMP es más pobre que el modelo de EXIF, las consecuencias de esta conversión se traducirá en una pérdida de información en la foto. En general, la pérdida de información no es de gran importancia para la mayoría de los usuarios, sin embargo, la norma recomienda que el software que el almacena los metadatos XMP conserven las informaciónes originales: lamentablemente no es siempre el caso.";

$lang['g003_help_magic '] = "La misma información puede ser almacenada en formatos múltiples dentro de una foto:
[ul]
[li]pueden estar presentes en todos los formatos[/li]
[li]puede estar presente en un formato, pero no en otro [/li]
[/ul]

Ejemplo, la abertura puede estar presente en cuatro diferentes metadatos:
[ul]
[li][b]exif.exif.FNumber[/b][/li]
[li][b]exif.exif.ApertureValue[/b][/li]
[li][b]xmp.exif:aperturevalue[/b][/li]
[li][b]xmp.exif:fnumber[/b][/li]
[/ul]

Para facilitar el retorno de la información que pueda estar disperso, el plugin ofrece una pequeña muestra de los metadatos más ampliamente utilizados y es responsable de analizar la presencia en las fotos, y restaurar la información más relevante.
Estos metadatos se llaman [b]Magic[/b].

Así, el [b]metadatos magic.ShotInfo.Aperture[/b] devuelve:
[ul]
[li]el valor de la metadatos [b]exif.exif.FNumber[/b] si está presente en la foto, de lo contrario[/li]
[li]el valor de la metadatos [b]xmp.exif:fnumber[/b] si está presente en la foto, de lo contrario[/li]
[li]el valor de la metadatos [b]exif.exif.ApertureValue[/b] si está presente en la foto, de lo contrario[/li]
[li]el valor de la metadatos [b]xmp.exif:aperturevalue[/b] si está presente en la foto[/li]
[/ul]";


?>
