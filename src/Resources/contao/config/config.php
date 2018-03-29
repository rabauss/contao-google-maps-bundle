<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['google_maps'] = [
    'tables' => ['tl_google_map', 'tl_google_map_overlay'],
    'stylesheet' => 'bundles/heimrichhannotcontaogooglemaps/css/backend.google-maps-bundle.css',
];

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['maps'] = [
    'google_map' => 'HeimrichHannot\GoogleMapsBundle\Element\ContentGoogleMap',
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_google_map']         = 'HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel';
$GLOBALS['TL_MODELS']['tl_google_map_overlay'] = 'HeimrichHannot\GoogleMapsBundle\Model\OverlayModel';

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundles';
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundlep';
