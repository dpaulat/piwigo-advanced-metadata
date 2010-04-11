<?php


$lang['Grum Plugin Classes is not installed'] = '<b>Grum Plugin Classes</b> plugin is not installed';

$lang['amd_title_page'] = 'Metadata advanced management';
$lang['g003_version'] = 'v';

$lang['g003_error_invalid_ajax_call'] = "Invalid function call!";

$lang['g003_metadata'] = "Metadata";
$lang['g003_database'] = "Repository";
$lang['g003_status'] = "Status";
$lang['g003_show'] = "Browse";

$lang['g003_numberOfAnalyzedPictures'] = "%d images have been analyzed and %d metada have been found";
$lang['g003_numberOfNotAnalyzedPictures'] = "%d images have not been analyzed";
$lang['g003_analyze_not_analyzed_pictures'] = "The analysis focuses on the images that have never been analyzed, and adds to the existing repository";
$lang['g003_analyze_all_pictures'] = "The analysis includes all the images in the gallery, and replaces the current repository";
$lang['g003_analyze_caddie_add_pictures'] = "The analysis focuses on the images in the basket, and adds to the existing repository";
$lang['g003_analyze_caddie_replace_pictures'] = "The analysis focuses on the images in the basket, and replaces the current repository";
$lang['g003_analyze'] = "Analyze";
$lang['g003_update_metadata'] = "Update metadata repository";
$lang['g003_status_of_database'] = "Repository status";
$lang['g003_updating_metadata'] = "Repository update";
$lang['g003_analyze_in_progress'] = "Analyze in progress...";
$lang['g003_analyze_is_finished'] = "Analyze completed";
$lang['g003_loading'] = "Loading...";
$lang['g003_numberOfPicturesWithoutTags'] = "%d images have no metadata";
$lang['g003_no_items_selected'] = "No metadata is selected";
$lang['g003_selected_tag_isnot_linked_with_any_picture'] = "The selected metadata is not linked to any image";
$lang['g003_TagId'] = "Metadata";
$lang['g003_TagLabel'] = "Name";
$lang['g003_NumOfImage'] = "Images";
$lang['g003_Pct'] = "%";
$lang['g003_select'] = "Selection";
$lang['g003_display'] = "Display";
$lang['g003_order'] = "Order by";
$lang['g003_filter'] = "Filter";
$lang['g003_tagOrder'] = "Metadata";
$lang['g003_numOrder'] = "Image number";
$lang['g003_valueOrder'] = "Value";
$lang['g003_no_filter'] = "No filter";
$lang['g003_magic_filter'] = "Magic (calculated metada)";
$lang['g003_exclude_unused_tags'] = "Exclude unused metadata";
$lang['g003_Value'] = "Value";
$lang['g003_selected_tags_only'] = "Return selected metadata only";

$lang['g003_select_metadata'] = "Metadata selection";
$lang['g003_display_management'] = "Metadata display management";
$lang['g003_number_of_filtered_metadata'] = "Metadata number:";
$lang['g003_number_of_distinct_values'] = "Number of distinct values:";

$lang['g003_click_to_edit_group'] = "Click to edit properties of the metadata group";
$lang['g003_click_to_delete_group'] = "Click to remove the metadata group";
$lang['g003_click_to_manage_group'] = "Click to manage elements of the metadata group";
$lang['g003_click_to_manage_list'] = "Click to add/remove metadata";
$lang['g003_add_a_group'] = "Add a group of metadata";
$lang['g003_adding_a_group'] = "Adding a group of metadata";
$lang['g003_editing_a_group'] = "Editing a group of metadata";
$lang['g003_deleting_a_group'] = "Removing a group of metadata";
$lang['g003_new_group'] = "New metadata group";
$lang['g003_name'] = "Name";
$lang['g003_add_delete_tags'] = "Add/remove metadata";
$lang['g003_confirm_group_delete'] = "Are you sure you want to delete the %s metadata group?";
$lang['g003_default_group_name'] = "Shooting conditions";

$lang['g003_ok'] = "Ok";
$lang['g003_cancel'] = "Cancel";
$lang['g003_yes'] = "Yes";
$lang['g003_no'] = "No";


$lang['g003_invalid_group_id'] = "Invalid metadata group Id";
$lang['g003_no_tag_can_be_selected'] = "No metadata available";


$lang['g003_warning_on_analyze_3'] = "The repository is gradually fed each time a page of the gallery is visited. Thus the time required for its complete making depends from:";
$lang['g003_warning_on_analyze_3a'] = "the number of pictures in the gallery";
$lang['g003_warning_on_analyze_3b'] = "the number of pictures displayed every day";
$lang['g003_warning_on_analyze_4a'] = "the repository is used for statistical purposes only and facilitates the metadata selection for display";
$lang['g003_warning_on_analyze_4b'] = "an image not used in the repository making still has metadata on the gallery";
$lang['g003_warning_on_analyze_5'] = "In order to get a complete repository quickly, a more complete analyze of the gallery is possible:";
$lang['g003_warning_on_analyze_0'] = "Warning!";
$lang['g003_warning_on_analyze_1'] = "Building the repository with the direct analysis process might be long (up to several minutes of treatment) and resource-consuming for the server, depending on the number of photos selected for analysis.";
$lang['g003_warning_on_analyze_2'] = "This type of use may be penalized by some hosts.";



$lang['g003_metadata_detail'] = "Possible values for the metadata";

$lang['g003_help'] = "Help on metadata";
$lang['g003_help_tab_exif'] = "Exif";
$lang['g003_help_tab_iptc'] = "IPTC";
$lang['g003_help_tab_xmp'] = "XMP";
$lang['g003_help_tab_magic'] = "Magic";
$lang['g003_help_exif'] = "EXIF Metadata is information stored in the image file by the camera at shooting.


The information there are mainly technical: 
[ul]
[li]equipment used (camera model, maker)[/li]
[li]shooting conditions (aperture, exposure time, focal length)[/li]
[li]time of the shooting (date, time)[/li]
[li]geographic location (GPS coordinates)[/li]
[li]information on the photo format (size, resolution, compression)[/li]
[/ul]

EXIF metadata is standardized ([url]http://www.exif.org/Exif2-2.PDF[/url]), but :
[ul]
[li]This standard established by the [url=http://www.jeita.or.jp]JEITA[/url] (Japan Electronics and Information Technology Industries Association) has no longer changed since 2002[/li]
[li]each metadata defined in the standard is optional, so not all cameras feed all metadata[/li]
[li]a [i]MakerNote[/i] metadata exists as an open field used by manufacturers to store information missing from the specifications (eg, lenses references); this data are specific to each manufacturer, sometimes for each camera. The plugin knows how to render some of this information for [b]Pentax[/b], [b]Canon[/b] and [b] Nikon [/b] cameras.[/li]
[/ul]";

$lang['g003_help_iptc'] = "IPTC Metadata consists of information the photographer can record in the image with an appropriate software.

Information there is mainly oriented towards the professional world: 
[ul]
[li]photographer references (name, contact)[/li]
[li]information on the Copyright[/li]
[li]description of the picture (title, description, reviews, tags)[/li]
[li]various information related to the professional world[/li]
[/ul]

IPTC metadata is standardized ([url]http://www.iptc.org[/url]).
This standard has been established by a consortium of major news agencies in the world, the [i]International Press Telecommunications Council [/i] (IPTC).
[li] information on the format of the photo (size, resolution, compression)";
$lang['g003_help_xmp'] = "XMP metadata are essentially EXIF and IPTC metadata that have been stored image file using XML format.

XMP metadata provide more flexibility:
[ul]
[li]information can be stored in several different languages[/li]
[li]usage of the Unicode character set allows (mainly) to use non-Latin characters[/li]
[li]XML facilitates the interpretation and exchange of information[/li]
[/ul]

XMP metadata is standardized ([url]http://www.metadataworkinggroup.org/specs[/url]).
The standard advises to use preferably the EXIF and IPTC metadata, if present.

EXIF & IPTC metadata conversion to XMP metadata is usually done with a photo editing software.

As XMP model is poorer than EXIF, this conversion will result in information loss in the picture. Usually the lost information is not too important for most users; however, the standard recommends that the software recording XMP metadata retain the original metadata: unfortunately, that is not always the case.";
$lang['g003_help_magic'] = "The same information can be stored within a photo in multiple formats:
[ul]
[li]it may exist in every format[/li]
[li]it may be present in one format but not in another one[/li]
[/ul]

For example, the aperture may be present in 4 different metadata:
[ul]
[li][b]exif.exif.FNumber[/b][/li]
[li][b]exif.exif.ApertureValue[/b][/li]
[li][b]xmp.exif:ApertureValue[/b][/li]
[li][b]xmp.exif:FNumber[/b][/li]
[/ul]

To facilitate the rendering of information that may be scattered, the plugin provides a small group of the most used metadata, and takes on the analyze of those present in the picture to return the most relevant information.
These are called [b]Magic[/ b] metadata.

Thus, the [b]magic.ShotInfo.Aperture[/b] metadata returns:
[ul]
[li]if present in the photo, the value of the [b]exif.exif.FNumber[/b] metadata, otherwise [/li]
[li]if present in the photo, the value of the [b]xmp.exif: FNumber[/b]metadata, otherwise [/li]
[li]if present in the photo, the value of the [b]exif.exif.ApertureValue[/b] metadata, otherwise [/ li]
[li]if present in the photo, the value of the [b]xmp.exif: ApertureValue[/b] metadata.[/li]
[/ul]";

?>