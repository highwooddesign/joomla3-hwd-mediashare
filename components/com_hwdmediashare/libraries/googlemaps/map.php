<?php
/**
 * @version    SVN $Id: map.php 427 2012-06-29 16:14:33Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelform library
jimport('joomla.application.component.modeladmin');

if (!class_exists('GoogleMapAPI')) {
    require_once('GoogleMap.php');
}

/**
 * hwdMediaShare framework google map class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareMap extends GoogleMapAPI
{
        /**
         * Return map header javascript (goes between <head></head>)
         */
        function getJavascriptHeader()
        {
                $document = JFactory::getDocument();

                if( $this->mobile == true)
                {
                        $document->setMetaData('viewport','initial-scale=1.0, user-scalable=no');
                }

                if(!empty($this->_elevation_polylines)||(!empty($this->_directions)&&$this->elevation_directions))
                {
                        $document->addScript('http://www.google.com/jsapi');
                        $document->addScriptDeclaration("// Load the Visualization API and the piechart package.
                                                         google.load('visualization', '1', {packages: ['columnchart']});");
                }

                $document->addScript('http://maps.google.com/maps/api/js?sensor='.(($this->mobile==true)?"true":"false"));

                if($this->marker_clusterer)
                {
                        $document->addScript($this->marker_clusterer_location);
                }
        }
        /**
         * Return map javascript
         */
        function getJavascriptMap()
        {
                    $_script = "";
                    $_key = $this->map_id;
                    $_output = '<script type="text/javascript" charset="utf-8">' . "\n";
            $_output .= '//<![CDATA[' . "\n";
            $_output .= "/*************************************************\n";
            $_output .= " * Created with GoogleMapAPI" . $this->_version . "\n";
            $_output .= " * Author: Brad Wedell <brad AT mycnl DOT com>\n";
            $_output .= " * Link http://code.google.com/p/php-google-map-api/\n";
            $_output .= " * Copyright 2010 Brad Wedell\n";
            $_output .= " * Original Author: Monte Ohrt <monte AT ohrt DOT com>\n";
            $_output .= " * Original Copyright 2005-2006 New Digital Group\n";
            $_output .= " * Originial Link http://www.phpinsider.com/php/code/GoogleMapAPI/\n";
            $_output .= " *************************************************/\n";
                    if($this->street_view_dom_id!=""){
                            $_script .= "
                                    var panorama".$this->street_view_dom_id."$_key = '';
                            ";
                            if(!empty($this->_markers)){
                                    $_script .= "
                                            var panorama".$this->street_view_dom_id."markers$_key = [];
                                    ";
                            }
                    }

                    if(!empty($this->_markers)){
                            $_script .= "
                                    var markers$_key  = [];
                            ";
                            if($this->sidebar) {
                                    $_script .= "
                                            var sidebar_html$_key  = '';
                                            var marker_html$_key  = [];
                                    ";
                            }
                    }
                    if($this->marker_clusterer){
                            $_script .= "
                              var markerClusterer$_key = null;
                            ";
                    }
            if($this->directions) {
                $_script .= "
                    var to_htmls$_key  = [];
                    var from_htmls$_key  = [];
                ";
            }
                    if(!empty($this->_directions)){
                            $_script .= "
                                var directions$_key = [];
                            ";
                    }
                    //Polylines
                    if(!empty($this->_polylines)){
                            $_script .= "
                                    var polylines$_key = [];
                                    var polylineCoords$_key = [];
                            ";
                            if(!empty($this->_elevation_polylines)){
                                    $_script .= "
                                            var elevationPolylines$_key = [];
                                    ";
                            }
                    }
            //Polygons
                    if(!empty($this->_polygons)){
                            $_script .= "
                                    var polygon$_key = [];
                                    var polygonCoords$_key = [];
                            ";
                    }
                    //Elevation stuff
                    if(!empty($this->_elevation_polylines)||(!empty($this->_directions)&&$this->elevation_directions)){
                            $_script .= "
                                    var elevationCharts$_key = [];
                            ";
                    }
                    //Overlays
                    if(!empty($this->_overlays)){
                            $_script .= "
                                    var overlays$_key = [];
                            ";
                    }
            //KML Overlays
            if(!empty($this->_kml_overlays)){
                $_script .= "
                    var kml_overlays$_key = [];
                ";
            }
            //New Icons
            if(!empty($this->_marker_icons)){
                    $_script .= "var icon$_key  = []; \n";
                    foreach($this->_marker_icons as $icon_key=>$icon_info){
                            //no need to check icon key here since that's already done with setters
                            $_script .= "
                              icon".$_key."['$icon_key'] = {};
                              icon".$_key."['$icon_key'].image =  new google.maps.MarkerImage('".$icon_info["image"]."',
                                          // The size
                                          new google.maps.Size(".$icon_info['iconWidth'].", ".$icon_info['iconHeight']."),
                                          // The origin(sprite)
                                          new google.maps.Point(0,0),
                                          // The anchor
                                          new google.maps.Point(".$icon_info['iconAnchorX'].", ".$icon_info['iconAnchorY'].")
                      );
                            ";
                            if(isset($icon_info['shadow']) && $icon_info['shadow']!=""){
                              $_script .= "
                        icon".$_key."['$icon_key'].shadow = new google.maps.MarkerImage('".$icon_info["shadow"]."',
                          // The size
                          new google.maps.Size(".$icon_info['shadowWidth'].", ".$icon_info['shadowHeight']."),
                          // The origin(sprite)
                          new google.maps.Point(0,0),
                          // The anchor
                          new google.maps.Point(".$icon_info['iconAnchorX'].", ".$icon_info['iconAnchorY'].")
                        );
                      ";
                            }
                    }
            }

            $_script .= "var map$_key = null;\n";

                    //start setting script var
            if($this->onload) {
               $_script .= 'function onLoad'.$this->map_id.'() {' . "\n";
            }

            if(!empty($this->browser_alert)) {
                    //TODO:Update with new browser catch - GBrowserIsCompatible is deprecated
                //$_output .= 'if (GBrowserIsCompatible()) {' . "\n";
            }

            /*
             *TODO:Update with local search bar once implemented in V3 api
                    $strMapOptions = "";
                    if($this->local_search){
                            $_output .= "
                                    mapOptions.googleBarOptions= {
                                            style : 'new'
                                            ".(($this->local_search_ads)?",
                                            adsOptions: {
                                                client: '".$this->ads_pub_id."',
                                                channel: '".$this->ads_channel."',
                                                language: 'en'
                                            ":"")."
                                    };
                            ";
                            $strMapOptions .= ", mapOptions";
                    }
            */

                    if($this->display_map){
                            $_script .= sprintf('var mapObj%s = document.getElementById("%s");', $_key, $this->map_id) . "\n";
                            $_script .= "if (mapObj$_key != 'undefined' && mapObj$_key != null) {\n";

                            $_script .= "
                                    var mapOptions$_key = {
                                            scrollwheel: ". ($this->scrollwheel?"true":"false") . ",
                                            zoom: ".$this->zoom.",
                                            mapTypeId: google.maps.MapTypeId.".$this->map_type.",
                                            mapTypeControl: ".($this->type_controls?"true":"false").",
                                            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.".$this->type_controls_style."}
                                    };
                            ";
                            if(isset($this->center_lat) && isset($this->center_lon)) {
                                    // Special care for decimal point in lon and lat, would get lost if "wrong" locale is set; applies to (s)printf only
                                    $_script .= "
                                            mapOptions".$_key.".center = new google.maps.LatLng(
                                                    ".number_format($this->center_lat, 6, ".", "").",
                                                    ".number_format($this->center_lon, 6, ".", "")."
                                            );
                                    ";
                            }

                            if($this->street_view_controls){
                                    $_script .= "
                                            mapOptions".$_key.".streetViewControl= true;

                                    ";
                            }

                            $_script .= "
                                    map$_key = new google.maps.Map(mapObj$_key,mapOptions$_key);
                            ";

                            if($this->street_view_dom_id!=""){
                                    $_script .= "
                                            panorama".$this->street_view_dom_id."$_key = new  google.maps.StreetViewPanorama(document.getElementById('".$this->street_view_dom_id."'));
                                            map$_key.setStreetView(panorama".$this->street_view_dom_id."$_key);
                                    ";

                                    if(!empty($this->_markers)){

                                            //Add markers to the street view
                                            if($this->street_view_dom_id!=""){
                                                    $_script .= $this->getAddMarkersJS($this->map_id, $pano=true);
                                            }
                                            //set center to last marker
                                            $last_id = count($this->_markers)-1;

                                            $_script .= "
                                                    panorama".$this->street_view_dom_id."$_key.setPosition(new google.maps.LatLng(
                                                            ".$this->_markers[$last_id]["lat"].",
                                                            ".$this->_markers[$last_id]["lon"]."
                                                    ));
                                                    panorama".$this->street_view_dom_id."$_key.setVisible(true);
                                            ";
                                    }
                            }

                            if(!empty($this->_directions)){
                                    $_script .= $this->getAddDirectionsJS();
                            }

                            //TODO:add support for Google Earth Overlay once integrated with V3
                            //$_output .= "map.addMapType(G_SATELLITE_3D_MAP);\n";

                            // zoom so that all markers are in the viewport
                            if($this->zoom_encompass && (count($this->_markers) > 1 || count($this->_polylines) >= 1 || count($this->_overlays) >= 1)) {
                                    // increase bounds by fudge factor to keep
                                    // markers away from the edges
                                    $_len_lon = $this->_max_lon - $this->_min_lon;
                                    $_len_lat = $this->_max_lat - $this->_min_lat;
                                    $this->_min_lon -= $_len_lon * $this->bounds_fudge;
                                    $this->_max_lon += $_len_lon * $this->bounds_fudge;
                                    $this->_min_lat -= $_len_lat * $this->bounds_fudge;
                                    $this->_max_lat += $_len_lat * $this->bounds_fudge;

                                    $_script .= "var bds$_key = new google.maps.LatLngBounds(new google.maps.LatLng($this->_min_lat, $this->_min_lon), new google.maps.LatLng($this->_max_lat, $this->_max_lon));\n";
                                    $_script .= 'map'.$_key.'.fitBounds(bds'.$_key.');' . "\n";
                            }

                            /*
                             * TODO: Update controls to use new API v3 methods.(Not a priority, see below)
                             * default V3 functionality caters control display according to the
                             * device that's accessing the page, as well as the specified width
                             * and height of the map itself.
                            if($this->map_controls) {
                              if($this->control_size == 'large')
                                      $_output .= 'map.addControl(new GLargeMapControl(), new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,10)));' . "\n";
                              else
                                      $_output .= 'map.addControl(new GSmallMapControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,60)));' . "\n";
                            }
                            if($this->type_controls) {
                                    $_output .= 'map.addControl(new GMapTypeControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,10)));' . "\n";
                            }

                            if($this->scale_control) {
                                    if($this->control_size == 'large'){
                                            $_output .= 'map.addControl(new GScaleControl(), new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(35,190)));' . "\n";
                                    }else {
                                            $_output .= 'map.addControl(new GScaleControl(), new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(190,10)));' . "\n";
                                    }
                            }
                            if($this->overview_control) {
                                    $_output .= 'map.addControl(new GOverviewMapControl());' . "\n";
                            }

                             * TODO: Update with ads_manager stuff once integrated into V3
                            if($this->ads_manager){
                                    $_output .= 'var adsManager = new GAdsManager(map, "'.$this->ads_pub_id.'",{channel:"'.$this->ads_channel.'",maxAdsOnMap:"'.$this->ads_max.'"});
                       adsManager.enable();'."\n";

                            }
                             * TODO: Update with local search once integrated into V3
                            if($this->local_search){
                                    $_output .= "\n
                                            map.enableGoogleBar();
                                    ";
                            }
                            */

                            if($this->traffic_overlay){
                                    $_script .= "
                                            var trafficLayer = new google.maps.TrafficLayer();
                                            trafficLayer.setMap(map$_key);
                                    ";
                            }

                            if($this->biking_overlay){
                                    $_script .= "
                                            var bikingLayer = new google.maps.BicyclingLayer();
                                            bikingLayer.setMap(map$_key);
                                    ";
                            }

                            $_script .= $this->getAddMarkersJS();

                            $_script .= $this->getPolylineJS();
                            $_script .= $this->getPolygonJS();
                            $_script .= $this->getAddOverlayJS();

                            if($this->_kml_overlays!==""){
                              foreach($this->_kml_overlays as $_kml_key=>$_kml_file){
                                      $_script .= "
                                              kml_overlays$_key[$_kml_key]= new google.maps.KmlLayer('$_kml_file');
                                              kml_overlays$_key[$_kml_key].setMap(map$_key);
                                      ";
                              }
                              $_script .= "

                              ";
                            }

                             //end JS if mapObj != "undefined" block
                            $_script .= '}' . "\n";

                    }//end if $this->display_map==true

            if(!empty($this->browser_alert)) {
                //TODO:Update with new browser catch SEE ABOVE
               // $_output .= '} else {' . "\n";
               // $_output .= 'alert("' . str_replace('"','\"',$this->browser_alert) . '");' . "\n";
               // $_output .= '}' . "\n";
            }

            if($this->onload) {
               $_script .= '}' . "\n";
            }

                    $_script .= $this->getMapFunctions();

                    if($this->_minify_js && class_exists("JSMin")){
                            $_script = JSMin::minify($_script);
                    }

                    //Append script to output
                    $_output .= $_script;
                    $_output .= '//]]>' . "\n";
            $_output .= '</script>' . "\n";

            $document = JFactory::getDocument();
            $document->addScriptDeclaration($_script);

            return;
            return $_output;

        }
        /**
        * Return map
        */
        function getMap() 
        {
                $_output = '<script type="text/javascript" charset="utf-8">' . "\n" . '//<![CDATA[' . "\n";
                //$_output .= 'if (GBrowserIsCompatible()) {' . "\n";
                if(strlen($this->width) > 0 && strlen($this->height) > 0) {
                    $_output .= sprintf('document.write(\'<div id="%s" style="float:left;width: %s; height: %s; position:relative;"><\/div>\');',$this->map_id,$this->width,$this->height) . "\n";
                } else {
                    $_output .= sprintf('document.write(\'<div id="%s" style="position:relative;"><\/div>\');',$this->map_id) . "\n";
                }
                //$_output .= '}';

                //if(!empty($this->js_alert)) {
                //    $_output .= ' else {' . "\n";
                //    $_output .= sprintf('document.write(\'%s\');', str_replace('/','\/',$this->js_alert)) . "\n";
                //    $_output .= '}' . "\n";
                //}

                $_output .= '//]]>' . "\n" . '</script>' . "\n";

                if(!empty($this->js_alert)) {
                    $_output .= '<noscript>' . $this->js_alert . '</noscript>' . "\n";
                }

                return $_output;
        }
        
        /**
         * Fetch an URL overload. 
         *
         * @param string $url
         */
        function fetchURL($url) 
        {
                if (function_exists('curl_init'))
                {
                        $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";

                        $curl_handle = curl_init();
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                        //curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
                        //curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
                        //curl_setopt($curl_handle, CURLOPT_HEADER, 1);
                        //curl_setopt($curl_handle, CURLOPT_REFERER, $this->_host);
                        curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                        $buffer = curl_exec($curl_handle);
                        curl_close($curl_handle);

                        if (!empty($buffer))
                        {
                                return $buffer;
                        }
                }

                return @file_get_contents($url);
        }
}
?>