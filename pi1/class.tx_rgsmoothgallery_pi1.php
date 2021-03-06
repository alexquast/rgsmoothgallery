<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Georg Ringer <http://www.just2b.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Plugin 'SmoothGallery' for the 'rgsmoothgallery' extension.
 *
 * @author  Georg Ringer <http://www.just2b.com>
 * @package TYPO3
 * @subpackage  tx_rgsmoothgallery
 */
class tx_rgsmoothgallery_pi1 extends tslib_pibase
{
    public $prefixId = 'tx_rgsmoothgallery_pi1'; // Same as class name
    public $scriptRelPath = 'pi1/class.tx_rgsmoothgallery_pi1.php'; // Path to this script relative to the extension dir.
    public $extKey = 'rgsmoothgallery'; // The extension key.
    public $pi_checkCHash = true;

    /**
     * Just some intialization, mainly reading the settings in the flexforms
     *
     * @param   array       $conf: The PlugIn configuration
     */
    public function init($conf)
    {
        $this->conf = $conf; // Storing configuration as a member var
        $this->pi_loadLL(); // Loading language-labels
        $this->pi_setPiVarDefaults(); // Set default piVars from TS
        $this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
        // Template code
        $this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
        $this->config['count'] = 0;

        // configuration flexforms
        $this->config['mode'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mode', 'sDEF') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'mode', 'sDEF') : $this->conf['mode'];
        $this->config['duration'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'time', 'sDEF')) ? intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'time', 'sDEF')) : intval($this->conf['duration']);
        $this->config['startingpoint'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startingpoint', 'sDEF') ? trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startingpoint', 'sDEF')) : trim($this->conf['startingpoint']);
        $pid = $this->conf['startingpointrecords'] ? $this->conf['startingpointrecords'] : $GLOBALS['TSFE']->id;
        $this->conf['startingpointrecords'] = $this->conf['startingpointrecords'] ? $this->conf['startingpointrecords'] : $pid;
        $this->config['startingpointrecords'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startingpointrecords', 'sDEF') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startingpointrecords', 'sDEF') : ($this->conf['startingpointrecords']);
        $this->config['startingpointdam'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startingpointdam', 'sDEF');
        $this->config['startingpointdamcat'] = $this->getFlexform('sDEF', 'startingpointdamcat', 'startingpointdamcat');
        $this->config['recursivedamcat'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'recursivedamcat', 'sDEF');
        $this->config['text'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'sDEF') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'sDEF') : $this->conf['text'];
        $this->config['id'] = $this->cObj->data['uid'] . $this->conf['id'];

        // size of images, overwritten by flexforms
        $this->config['width'] = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'width', 'sDEF')) ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'width', 'sDEF') : $this->conf['big.']['file.']['maxW'];
        $this->config['height'] = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'height', 'sDEF')) ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'height', 'sDEF') : $this->conf['big.']['file.']['maxH'];


        $this->config['heightGallery'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'heightgallery', 'sDEF')) ? intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'heightgallery', 'sDEF')) : $this->conf['heightGallery'];
        $this->config['widthGallery'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'widthgallery', 'sDEF')) ? intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'widthgallery', 'sDEF')) : $this->conf['widthGallery'];

        if (strpos($this->config['width'], 'c') || strpos($this->config['width'], 'm') || strpos($this->config['height'], 'c') || strpos($this->config['height'], 'm')) {
            $this->conf['big.']['file.']['width'] = $this->config['width'];
            $this->conf['big.']['file.']['height'] = $this->config['height'];
            $this->conf['big2.']['file.']['10.']['file.']['width'] = $this->config['width'];
            $this->conf['big2.']['file.']['10.']['file.']['height'] = $this->config['height'];

            unset($this->conf['big.']['file.']['maxW']);
            unset($this->conf['big.']['file.']['maxH']);
            unset($this->conf['big2.']['file.']['10.']['file.']['maxW']);
            unset($this->conf['big2.']['file.']['10.']['file.']['maxH']);
        } else {
            if ($this->config['width']) {
                $this->conf['big.']['file.']['maxW'] = $this->config['width'];
                $this->conf['big2.']['file.']['10.']['file.']['maxW'] = $this->config['width'];
            }
            if ($this->config['height']) {
                $this->conf['big.']['file.']['maxH'] = $this->config['height'];
                $this->conf['big2.']['file.']['10.']['file.']['maxH'] = $this->config['height'];
            }
            if (!$this->config['heightGallery']) {
                $this->config['heightGallery'] = $this->config['height'];
            }
            if (!$this->config['widthGallery']) {
                $this->config['widthGallery'] = $this->config['width'];
            }
        }

        // check starting point for missing slash
        if (substr($this->config['startingpoint'], -1) != '/') {
            $this->config['startingpoint'] = $this->config['startingpoint'] . '/';
        }

        if (substr($this->config['startingpoint'], 0, 1) == '/') {
            $size = strlen($this->config['startingpoint']);
            $this->config['startingpoint'] = substr($this->config['startingpoint'], 1, $size - 1);
        }

        /*
         * Advanced settings
         */
        $this->config['hideInfoPane'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'infopane', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'infopane', 'advanced') : $this->conf['hideInfoPane'];
        $this->config['thumbOpacity'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'thumbopacity', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'thumbopacity', 'advanced') : $this->conf['thumbOpacity'];
        $this->config['slideInfoZoneOpacity'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'slideinfozoneopacity', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'slideinfozoneopacity', 'advanced') : $this->conf['slideInfoZoneOpacity'];
        $this->config['thumbSpacing'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'thumbspacing', 'advanced')) ? intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'thumbspacing', 'advanced')) : $this->conf['thumbSpacing'];

        $this->config['watermarks'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'watermark', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'watermark', 'advanced') : $this->conf['watermarks'];
        $this->config['limitImagesDisplayed'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'limitImagesDisplayed', 'advanced')) ? intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'limitImagesDisplayed', 'advanced')) : intval($this->conf['limitImagesDisplayed']);

        $this->config['showThumbs'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'showThumbs', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'showThumbs', 'advanced') : $this->conf['showThumbs'];
        $this->config['showPlay'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'showPlay', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'showPlay', 'advanced') : $this->conf['showPlay'];
        $this->config['arrows'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'arrows', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'arrows', 'advanced') : $this->conf['arrows'];
        $this->config['advancedSettings'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'advancedsettings', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'advancedsettings', 'advanced') : $this->conf['advancedSettings'];
        $this->config['externalThumbs'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'externalthumbs', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'externalthumbs', 'advanced') : $this->conf['externalThumbs'];
        $this->config['externalControl'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'externalcontrol', 'advanced') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'externalcontrol', 'advanced') : $this->conf['externalControl'];

        /*
         * Split characters from Extension Manager
         */
        $tmp_confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rgsmoothgallery']);
        $tmp_confArr['splitRecord'] = ($tmp_confArr['splitRecord']) ? $tmp_confArr['splitRecord'] : '\n';
        $this->config['splitRecord'] = ($tmp_confArr['splitRecord'] == '\n') ? "\n" : $tmp_confArr['splitRecord'];

        $this->config['splitComment'] = ($tmp_confArr['splitComment']) ? $tmp_confArr['splitComment'] : '|';

        /*
         * StdWrap options for every value from flexforms merged with TS to override it again with TS and to manipulate it with stdWrap things
         */

        foreach ($this->config as $key => $value) {
            $this->config[$key] = $this->cObj->stdWrap($value, $this->conf[$key . '.']);
        }
    }

    /**
     * The main method of the PlugIn
     * for showing the SmoothGallery
     *
     * @param   string      $content: The PlugIn content
     * @param   array       $conf: The PlugIn configuration
     * @return  The gallery
     */
    public function main($content, $conf)
    {
        $this->init($conf);
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 0; // Configuring so caching is expected.
        $this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

        if ($this->conf['pathToJdgalleryJS'] == '') {
            return $this->pi_getLL('errorIncludeStatic');
        } else {
            // get the needed js to load the gallery and to start it
            $content .= $this->getJs(
                    $this->config['showThumbs'], $this->config['arrows'], $this->config['duration'], $this->config['widthGallery'], $this->config['heightGallery'], $this->config['advancedSettings'], $this->config['id'], $this->conf
            );

            // depending on the chosen settings the images come from different places
            $content .= $this->getImageDifferentPlaces($this->config['limitImagesDisplayed']);

            #return $this->pi_wrapInBaseClass($content);
            return '<div class="tx-rgsmoothgallery-pi1 rgsgnest' . $this->config['id'] . '">' . $content . '</div><div id="externalthumbs"></div>';
        }
    }

# end main
    /**
     * Just some divs needed for the gallery
     *
     * @param   string/int   $uniqueId: A unique ID to have more than 1 galleries on 1 page
     * @return  The opened divs
     */
    public function beginGallery($uniqueId, $limitImages = 0, $imagePath)

    {
        //echo '<script>console.log("' . $foo . '")</script>';
        if ($limitImages == 1) {
            $content = '<div class="rgsgcontent" data-image-path="' . $imagePath .'"><div class="myGallery-NoScript" id="myGallery-NoScript' . $uniqueId . '">';
        } else {
            $content = '<div class="rgsgcontent" data-image-path="' . $imagePath .'"><div class="myGallery" id="myGallery' . $uniqueId . '">';
        }

        // Save button && Print button
        $content .= '<div class="rgsg-btn" style="display:none">' . $this->conf['enableSaveButton'] . $this->conf['enablePrintButton'] . '</div>';

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraBeginGalleryHook'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraBeginGalleryHook'] as $_classRef) {
                $_procObj = & t3lib_div::getUserObj($_classRef);
                $content = $_procObj->extraBeginGalleryProcessor($content, $limitImages, $this);
            }
        }

        return $content;
    }

# end beginGallery
    /**
     * Just some divs needed for the gallery
     *
     * @return  The closed divs
     */
    public function endGallery()
    {
        $content = '</div></div>';
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraEndGalleryHook'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraEndGalleryHook'] as $_classRef) {
                $_procObj = & t3lib_div::getUserObj($_classRef);
                $content = $_procObj->extraEndGalleryProcessor($content, $this);
            }
        }

        return $content;
    }

# end endGallery
    /**
     * get the images out of a directory
     *
     * @param   int     $limitImages: How many images to return; default=0 list all
     * @return  image(s)
     */
    public function getImagesDirectory($limitImages = 0)
    {
        if (is_dir($this->config['startingpoint'])) {
            $images = array();
            $images = $this->getFiles($this->config['startingpoint']);
            // randomise and limit image items returned from images array
            // also useful to limit items in array to 1 item for use when no javascript in browser
            // if $limitImages=0 then this if statement is bypassed and all images in images array returned for processing
            if ($limitImages > 0) {
                $images = $this->getSlicedRandomArray($images, 0, $limitImages);
            }
            // ADDED!
            // pass the path to the original images also
            $content .= $this->beginGallery($this->config['id'], $limitImages, $this->config['startingpoint']);

            // read the description from field in flexforms
            if ($this->config['text'] != '') {
                $caption = explode($this->config['splitRecord'], $this->config['text']);
            }

            // add the images
            foreach ($images as $key => $value) {
                $path = $this->config['startingpoint'] . $value;
                //echo "<script>console.log('" . $this . "')</script>";
                // caption text
                if ($caption[$key]) {
                    $text = explode($this->config['splitComment'], $caption[$key]);
                } else {
                    // update of Xavier Perseguers (typo3@perseguers.ch) thx!
                    $text = $this->readImageInfo($this->config['startingpoint'] . $value);
                }

                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraGetImagesDirectoryHook'])) {
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraGetImagesDirectoryHook'] as $_classRef) {
                        $_procObj = & t3lib_div::getUserObj($_classRef);
                        $text = $_procObj->extraGetImagesDirectoryHook($text, $this->config['startingpoint'] . $value, $this);
                    }
                }

                // add element to slideshow
                $content .= $this->addImage(
                        $path, $text[0], $text[1], $this->config['showThumbs'], $path, $limitImages
                );
            } # end foreach file


            $content .= $this->endGallery();
        } # end is_dir
        return $content;
    }

# end getImagesDirectory
    /**
     * get the images out of records a user created in the backend before
     *
     * @param   int     $limitImages: How many images to return; default=0 list all
     * @return  image(s)
     */
    public function getImagesRecords($limitImages = 0)
    {
        //prepare query
        $sort = 'sorting';
        $fields = 'title,image,description,l18n_parent';
        $tables = 'tx_rgsmoothgallery_image';
        $temp_where = 'pid IN (' . $this->config['startingpointrecords'] . ') AND hidden=0 AND deleted=0 AND sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_content;

        $content .= $this->beginGallery($this->config['id'], $limitImages);

        // add the images
        // randomise and limit image items returned from images array
        // also useful to limit items in array to 1 item for use when no javascript in browser
        // if $limitImages=0 then this if statement is bypassed and all images in images array returned for processing
        if ($limitImages > 0) {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', 'rand()', $limitImages);
        } else {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', $sort, '');
        }
        $this->sys_language_mode = $this->conf['sys_language_mode'] ? $this->conf['sys_language_mode'] : $GLOBALS['TSFE']->sys_language_mode;
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            if ($GLOBALS['TSFE']->sys_language_content) {
                $OLmode = ($this->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
                $row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tx_rgsmoothgallery_image', $row, $GLOBALS['TSFE']->sys_language_content, $OLmode);
            }

            if ($row['image'] == '') {
                $res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('image', $tables, 'uid=' . $row['l18n_parent']);
                $row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);
                $row['image'] = $row2['image'];
            }

            $path = 'uploads/tx_rgsmoothgallery/' . $row['image'];
            //$content .="<script>alert(1); console.log('" . $path ."'')</script>";
            // add element to slideshow
            $content .= $this->addImage(
                    $path, $row['title'], $row['description'], $this->config['showThumbs'], $path, $limitImages
            );
        } # end foreach file


        $content .= $this->endGallery();

        return $content;
    }

# getImagesRecords
    /**
     * get the images out of DAM
     *
     * @param   int     $limitImages: How many images to return; default=0 list all
     * @return  image(s)
     */
    public function getImagesDam($limitImages = 0)
    {
        // update of ian (ian@webian.it) thx!
        // check if there's a localized version of the current content object
        $uid = $this->cObj->data['uid'];
        if ($this->cObj->data['_LOCALIZED_UID']) {
            $uid = $this->cObj->data['_LOCALIZED_UID'];
        }
        $sys_language_uid = $GLOBALS['TSFE']->sys_language_content;

        // get all the files
        $images = tx_dam_db::getReferencedFiles('tt_content', $uid, 'rgsmoothgallery', 'tx_dam_mm_ref');

        // randomise and limit image items returned from images array
        if ($limitImages > 0) {
            $test = ($images['files']);
            $test = $this->getSlicedRandomArray($test, 0, $limitImages);
            $images['files'] = $test;
        }

        // begin gallery
        $content .= $this->beginGallery($this->config['id'], $limitImages);

        // add image
        foreach ($images['files'] as $key => $path) {
            // get data from the single image
            $fields = 'title,description';
            $tables = 'tx_dam';

            // now i check the tx_dam table to see if there's a localization for the current DAM record (image)
            $temp_where = 'l18n_parent = ' . $key . ' AND sys_language_uid = ' . $sys_language_uid;
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $tables, $temp_where);
            // if i find a localized record i overwrite the default language $key with the localized language $key
            if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $key = $row['uid'];
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res);

            $temp_where = 'uid = ' . $key;
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where);
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            // add element to slideshow
            $content .= $this->addImage(
                    $path, $row['title'], $row['description'], $this->config['showThumbs'], $path, $limitImages
            );
        }

        $content .= $this->endGallery();

        return $content;
    }

# end getImagesDam
    /**
     * get the images out of DAM
     *
     * @param   int     $limitImages: How many images to return; default=0 list all
     * @return  image(s)
     */
    public function getImagesDamCat($limitImages = 0)
    {
        $content .= $this->beginGallery($this->config['id'], $limitImages);

        // add image
        $list = str_replace('tx_dam_cat_', '', $this->config['startingpointdamcat']);

        $listRecursive = $this->getRecursiveDamCat($list, $this->config['recursivedamcat']);
        $listArray = explode(',', $listRecursive);
        $files = array();
        foreach ($listArray as $cat) {

            // add images from categories
            $fields = 'tx_dam.uid,tx_dam.title,tx_dam.description,tx_dam.file_name,tx_dam.file_path';
            $tables = 'tx_dam,tx_dam_mm_cat';
            $temp_where = 'tx_dam.deleted = 0 AND tx_dam.file_mime_type=\'image\' AND tx_dam.hidden=0 AND tx_dam_mm_cat.uid_foreign=' . $cat . ' AND tx_dam_mm_cat.uid_local=tx_dam.uid';
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $temp_where, '', 'tx_dam.sorting');

            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                $files[$row['uid']] = $row; # just add the image to an array
            }

            $GLOBALS['TYPO3_DB']->sql_free_result($res);
        }

        if ($limitImages > 0) {
            $files = $this->getSlicedRandomArray($files, 0, $limitImages);
        }

        // add the image for real
        foreach ($files as $key => $row) {
            $path = $row['file_path'] . $row['file_name'];

            // add element to slideshow
            $content .= $this->addImage(
                    $path, $row['title'], nl2br($row['description']), $this->config['showThumbs'], $path, $limitImages
            );
        }

        $content .= $this->endGallery();

        return $content;
    }

    /**
     * Loads all the needed javascript stuff and
     * does the configuration of the gallery
     *
     * @param   boolean  $thumbsVal: Thumbnail preview activated?
     * @param   boolean  $arrowsVal: Arrows to neighbour images activated?
     * @param   string   $durationVal: If automatic slideshow the value of the delay
     * @param   string   $advancedSettings: Advanced configuration
     * @param   string/int   $uniqueId: A unique ID to have more than 1 galleries on 1 page
     * $param array    $conf: $configuration-array
     * @return  The gallery
     */
    public function getJs($thumbsVal, $arrowsVal, $durationVal, $widthGallery, $heightGallery, $advancedSettings, $uniqueId, $conf, $overrideJS = '')
    {
        $this->conf = $conf;

        if (t3lib_extMgm::isLoaded('t3mootools')) {
            require_once t3lib_extMgm::extPath('t3mootools') . 'class.tx_t3mootools.php';
        }
        if (defined('T3MOOTOOLS')) {
            tx_t3mootools::addMooJS();
        } else {
            $header .= $this->getPath($this->conf['pathToMootools']) ? '<script src="' . $this->getPath($this->conf['pathToMootools']) . '" type="text/javascript"></script>' : '';
            $header .= $this->getPath($this->conf['pathToMootoolsMore']) ? '<script src="' . $this->getPath($this->conf['pathToMootoolsMore']) . '" type="text/javascript"></script>' : '';
        }

        // path to js + css
        $GLOBALS['TSFE']->additionalHeaderData['rgsmoothgallery'] = $header . '
        <script src="' . $this->getPath($this->conf['pathToJdgalleryJS']) . '" type="text/javascript"></script>
        <link rel="stylesheet" href="' . $this->getPath($this->conf['pathToJdgalleryCSS']) . '" type="text/css" media="screen" />
      ';

        if ($this->config['externalControl'] == 1) {
            $externalControl1 = 'var myGallery' . $uniqueId . ';';
        } else {
            $externalControl2 = 'var';
        }

        // inline CSS for different size of gallery
        $widthGallery = $widthGallery ? 'width:' . $widthGallery . 'px;' : '';
        $heightGallery = $heightGallery ? 'height:' . $heightGallery . 'px;' : '';
        if ($heightGallery != '' || $widthGallery != '') {
            $GLOBALS['TSFE']->additionalCSS['rgsmoothgallery' . $uniqueId] = '#myGallery' . $uniqueId . ' {' . $widthGallery . $heightGallery . '}';
        }

        // inline CSS for the loading bar if plugin not loaded and for the given height of the gallery
        $GLOBALS['TSFE']->additionalCSS['rgsmoothgallery' . $uniqueId] .= ' .rgsgnest' . $uniqueId . ' { ' . $widthGallery . $heightGallery . ' }';

        if ($this->conf['rgsmoothgallerylinks'] == 1) {
            $GLOBALS['TSFE']->additionalCSS['rgsmoothgallery' . $uniqueId] .= ' .rgsglinks' . $uniqueId . ' { ' . $widthGallery . ' }';
        }

        // configuration of gallery
        $duration = ($durationVal) ? 'timed:true,delay: ' . $durationVal : 'timed:false';
        $thumbs = ($thumbsVal == 1) ? 'true' : 'false';
        $arrows = ($arrowsVal == 1) ? 'true' : 'false';

        // advanced settings (from TS + tab flexform configuration)
        $advancedSettings .= ($this->config['hideInfoPane']) ? 'showInfopane: false,' : '';
        if ($this->config['thumbOpacity'] && $this->config['thumbOpacity'] > 0 && $this->config['thumbOpacity'] <= 1) {
            $advancedSettings .= 'thumbOpacity: ' . $this->config['thumbOpacity'] . ',';
        }
        if (!$this->config['hideInfoPane'] && $this->config['slideInfoZoneOpacity'] && $this->config['slideInfoZoneOpacity'] > 0 && $this->config['slideInfoZoneOpacity'] <= 1) {
            $advancedSettings .= 'slideInfoZoneOpacity: ' . $this->config['slideInfoZoneOpacity'] . ',';
        }
        $advancedSettings .= ($this->config['thumbSpacing']) ? 'thumbSpacing: ' . $this->config['thumbSpacing'] . ',' : '';
        $advancedSettings .= ($this->config['showPlay']) ? 'showPlay: true,' : '';

        // external thumbs
        $advancedSettings .= ($this->config['externalThumbs']) ? 'useExternalCarousel:true,carouselElement:$("' . $this->config['externalThumbs'] . '"),' : '';
        #
        // js needed to load the gallery and to get it started
        if ($overrideJS != '') {
            $js = $overrideJS;
        } else {
            $js .= '
            <script type="text/javascript">' . $externalControl1 . '
                function startGallery' . $uniqueId . '() {
                    if(window.gallery' . $uniqueId . ') {
                        ' . $externalControl2 . ' myGallery' . $uniqueId . ' = new gallery($(\'myGallery' . $uniqueId . '\'), {
                        ' . $duration . ',
                        showArrows: ' . $arrows . ',
                        showCarousel: ' . $thumbs . ',
                        textShowCarousel: \'' . $this->pi_getLL('textShowCarousel') . '\',
                        embedLinks: false,
                        ' . $advancedSettings . '
                        });
                    } else {
                        window.gallery' . $uniqueId . '=true;
                        if(this.ie)
                        {
                            window.setTimeout("startGallery' . $uniqueId . '();",3000);
                        } else {
                            window.setTimeout("startGallery' . $uniqueId . '();",100);
                        }
                    }
                }
                window.addEvent("domready", startGallery' . $uniqueId . ');
            </script>';
            if ($this->conf['noscript'] == 1) {
                $js .= '<noscript>
            ' . $this->getImageDifferentPlaces(1) . '
            </noscript>';
            }
        }

        return $js;
    }

    /**
     * depending on the chosen settings the images come from different places
     *
     * @param   string  $limitImages: How many images to return; default=0 list all
     * @return  The image(s)
     */
    public function getImageDifferentPlaces($limitImages = 0)
    {
        if ($this->config['mode'] == 'DIRECTORY') {
            $content .= $this->getImagesDirectory($limitImages);
        } elseif ($this->config['mode'] == 'RECORDS') {
            $content .= $this->getImagesRecords($limitImages);
        } elseif ($this->config['mode'] == 'DAM') {
            $content .= $this->getImagesDam($limitImages);
        } elseif ($this->config['mode'] == 'DAMCAT') {
            $content .= $this->getImagesDamCat($limitImages);
        }

        // hook
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraDifferentPlaces'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraDifferentPlaces'] as $_classRef) {
                $_procObj = & t3lib_div::getUserObj($_classRef);
                $content = $_procObj->extraBeginGalleryProcessor($content, $limitImages, $this);
            }
        }

        return $content;
    }

    /**
     * Adds a single image to the gallery
     *
     * @param   string  $path: Path to the image
     * @param   string  $title: Title for the image
     * @param   string  $description: Description for the image
     * @param   string  $thumb: Url to the thumbnail image
     * @param   string  $uniqueID: Unique-ID to identify an image (uid or path)
     * @param   string  $limitImages: How many images to return; default=0 list all
     * @return  The single image
     */
    private function addImage($path, $title, $description, $thumb, $uniqueID, $limitImages = 0)
    {
        // count of images
        if ($limitImages > 1 || $limitImages == 0) {
            $this->config['count'] ++;
        }

        //  generate images
        if ($this->config['watermarks']) {
            $imgTSConfigBig = $this->conf['big2.'];
            $imgTSConfigBig['file.']['10.']['file'] = $path;
        } else {
            $imgTSConfigBig = $this->conf['big.'];
            $imgTSConfigBig['file'] = $path;
        }
        $bigImage = $this->cObj->IMG_RESOURCE($imgTSConfigBig);

        if ($thumb) {
            $imgTSConfigThumb = $this->conf['thumb.'];
            $imgTSConfigThumb['file'] = $path;
            $thumbImage = '<img src="' . $this->cObj->IMG_RESOURCE($imgTSConfigThumb) . '" class="thumbnail" />';
        }

        $text = (!$title) ? '' : "<h3>$title</h3>";
        $text .= (!$description) ? '' : "<p>$description</p>";

        // if just 1 image should be returned
        if ($limitImages == 1) {
            return '<img src="' . $bigImage . '" class="full" />';
        }

        // build the image element
        $singleImage .= '
      <div class="imageElement" data-org="'.$path.'">' . $text . '
        <img src="' . $bigImage . '" class="full" />
        ' . $thumbImage . '
      </div>';

        // Adds hook for processing the image
        $config['path'] = $path;
        $config['title'] = $title;
        $config['description'] = $description;
        $config['uniqueID'] = $uniqueID;
        $config['thumb'] = $thumb;
        $config['limitImages'] = $limitImages;
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraImageHook'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rgsmoothgallery']['extraImageHook'] as $_classRef) {
                $_procObj = & t3lib_div::getUserObj($_classRef);
                $singleImage = $_procObj->extraImageProcessor($singleImage, $config, $this);
            }
        }
        return $singleImage;
    }

    /**
     * Gets all image files out of a directory
     *
     * @param   string  $path: Path to the directory
     * @return Array with the images
     */
    public function getFiles($path, $extra = "")
    {
        $files = array();

        // check for needed slash at the end
        $length = strlen($path);
        if ($path {$length - 1} != '/') {
            $path .= '/';
        }

        $imagetypes = $this->conf["filetypes"] ? explode(',', $this->conf["filetypes"]) : array(
            'jpg',
            'jpeg',
            'gif',
            'png',
        );

        if ($dir = dir($path)) {
            while (false !== ($file = $dir->read())) {
                if ($file != '.' && $file != '..') {
                    $ext = strtolower(substr($file, strrpos($file, '.') + 1));
                    if (in_array($ext, $imagetypes)) {
                        array_push($files, $extra . $file);
                    } elseif ($this->conf["recursive"] == '1' && is_dir($path . "/" . $file)) {
                        $dirfiles = $this->getFiles($path . "/" . $file, $extra . $file . "/");
                        if (is_array($dirfiles)) {
                            $files = array_merge($files, $dirfiles);
                        }
                    }
                }
            }

            $dir->close();
            // sort files, thx to all
            sort($files);

            return $files;
        }
    }

# end getFiles
    /**
     * Gets the path to a file, needed to translate the 'EXT:extkey' into the real path
     *
     * @param   string  $path: Path to the file
     * @return the real path
     */
    public function getPath($path)
    {
        if (substr($path, 0, 4) == 'EXT:') {
            $keyEndPos = strpos($path, '/', 6);
            $key = substr($path, 4, $keyEndPos - 4);
            $keyPath = t3lib_extMgm::siteRelpath($key);
            $newPath = $keyPath . substr($path, $keyEndPos + 1);

            return $newPath;
        } else {
            return $path;
        }
    }

# end getPath
    /**
     * Random view of an array and slice it afterwards, preserving the keys
     *
     * @param   array  $array: Array to modify
     * @param   array  $offset: Where to start the slicing
     * @param   array  $length: Length of the sliced array
     * @return the randomized and sliced array
     */
    public function getSlicedRandomArray($array, $offset, $length)
    {
        // shuffle
        $new_arr = array();
        while (count($array) > 0) {
            $val = array_rand($array);
            $new_arr[$val] = $array[$val];
            unset($array[$val]);
        }
        $result = $new_arr;

        // slice
        $result2 = array();
        $i = 0;
        if ($offset < 0) {
            $offset = count($result) + $offset;
        }
        if ($length > 0) {
            $endOffset = $offset + $length;
        } elseif ($length < 0) {
            $endOffset = count($result) + $length;
        } else {
            $endOffset = count($result);
        }

        // collect elements
        foreach ($result as $key => $value) {
            if ($i >= $offset && $i < $endOffset) {
                $result2[$key] = $value;
            }
            $i ++;
        }

        return $result2;
    }

    /**
     * get a list of recursive categories
     *
     * @param   string      $id: comma seperated list of ids
     * @param   int     $level: the level for recursion
     * @return  image(s)
     */
    public function getRecursiveDamCat($id, $level = 0)
    {
        $result = $id . ','; # add id of 1st level
        $idList = explode(',', $id);

        if ($level > 0) {
            $level --;

            foreach ($idList as $key => $value) {
                $where = 'hidden=0 AND deleted=0 AND parent_id=' . $id;
                $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_cat', $where);
                while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                    $rec = $this->getRecursiveDamCat($row['uid'], $level);
                    if ($rec != '') {
                        $result .= $rec . ',';
                    }
                }
            } # end for each
        } # end if level


        $result = str_replace(',,', ',', $result);
        $result = substr($result, 0, - 1);

        return $result;
    }

    /**
     * Get the value out of the flexforms and if empty, take if from TS
     *
     * @param   string      $sheet: The sheed of the flexforms
     * @param   string      $key: the name of the flexform field
     * @param   string      $confOverride: The value of TS for an override
     * @return  string  The value of the locallang.xml
     */
    public function getFlexform($sheet, $key, $confOverride = '')
    {
        // Default sheet is sDEF
        $sheet = ($sheet == '') ? $sheet = 'sDEF' : $sheet;
        $flexform = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $key, $sheet);

        // possible override through TS
        if ($confOverride == '') {
            return $flexform;
        } else {
            $value = $flexform ? $flexform : $this->conf[$confOverride];

            return $value;
        }
    }

    /**
     * Read data from an image or an associated text file.
     *
     * @param   string      $image: path to the image
     * @return  array       title, description and author
     */
    public function readImageInfo($image)
    {
        $exifData = $this->readExif($image);
        $iptcData = $this->readIptc($image);
        $textData = $this->readTextComment($image);

        $this->getPrioritizedContent($exifData, 'author', 'description');

        // Text data has precedence, then EXIF, then IPTC
        $title = $this->getPrioritizedContent(array($textData['title'], $exifData['title'], $iptcData['title']));
        $description = $this->getPrioritizedContent(array($textData['description'], $exifData['description'], $iptcData['description']));
        $author = $this->getPrioritizedContent(array($textData['author'], $exifData['author'], $iptcData['author']));

        // Append author to the description
        if ($author && $this->conf['author']) {
            if ($description) {
                $description .= ' ';
            }
            $description .= str_replace('|', $author, $this->pi_getLL('authorWrap'));
        }

        return array($title, $description);
    }

    /**
     * Read EXIF data from an image.
     *
     * @param   string      $image: path to the image
     * @return  array       title, description and author
     */
    public function readExif($image)
    {
        if (!t3lib_div::isAbsPath($image)) {
            $image = t3lib_div::getFileAbsFileName($image);
        }

        $data = array('title' => '', 'description' => '', 'author' => '');

        if (!t3lib_div::inArray(get_loaded_extensions(), 'exif') || $this->conf['exif'] != 1) { // If there is no EXIF Support at your installation
            return $data;
        }

        if (file_exists($image)) {
            $image_info = getimagesize($image);
        }
        if ($image_info[2] == 2) { // check for correct image-type
            //ini_set('exif.encode_unicode', 'UTF-8'); //Set encoding to Unicode, if not set in php.ini
            $exif_array = exif_read_data($image, true, false); // Load all EXIF informations from the original Pic in an Array
            $exif_array['Comments'] = htmlentities(str_replace("\n", '<br />', $exif_array['Comments'])); // Linebreak


            $data['title'] = $this->getPrioritizedContent($exif_array, 'Title', 'Subject');
            $data['description'] = $this->getPrioritizedContent($exif_array, 'Comments', 'ImageDescription');
            $data['author'] = $this->getPrioritizedContent($exif_array, 'Author', 'Artist');
        }

        return $data;
    }

    /**
     * Read IPTC data from an image.
     *
     * @param   string      $image: path to the image
     * @return  array       title, description and author
     */
    public function readIptc($image)
    {
        if (!t3lib_div::isAbsPath($image)) {
            $image = t3lib_div::getFileAbsFileName($image);
        }

        $data = array('title' => '', 'description' => '', 'author' => '');

        if ($this->conf['iptc'] != 1) { // If there is no EXIF Support at your installation
            return $data;
        }

        $info = null;
        getimagesize($image, $info);
        if (is_array($info)) {
            $iptc = iptcparse($info["APP13"]);

            $data['title'] = $this->getPrioritizedContent($iptc, '2#005', '2#105'); // Title then Headline
            // Array is returned, use first item
            $data['title'] = $data['title'][0];

            $data['description'] = $iptc['2#120'][0];
            $data['author'] = $iptc['2#080'][0];
        }

        return $data;
    }

    /**
     * Read image information from a associated text file.
     * The text file should have the same name as the image but
     * should end with '.txt'. Format
     * <pre>
     * {{title}}
     * {{description}}
     * {{author}}
     * </pre>
     *
     * @param   string      $image: path to the image
     * @return  array       title, description and author
     */
    public function readTextComment($image)
    {
        if (!t3lib_div::isAbsPath($image)) {
            $image = t3lib_div::getFileAbsFileName($image);
        }

        $data = array('title' => '', 'description' => '', 'author' => '');

        $textfile = substr($image, 0, strrpos($image, '.')) . '.txt';

        if (file_exists($textfile)) {
            $lines = file($textfile);

            if (count($lines)) {
                $data['title'] = $lines[0];
                $data['description'] = $lines[1];
                $data['author'] = $lines[2];
            }
        }

        return $data;
    }

    /**
     * Extract content from an array. First key that is
     * associated to a content that is not empty will be
     * returned. If keys are null then it returns the first
     * non-empty element.
     *
     * @param   array       $array: array whose content should be extracted
     * @param   mixed       key1
     * @param   mixed       key2
     * @param   mixed       ...
     * @return  string
     */
    public function getPrioritizedContent($array)
    {
        $keys = func_get_args();
        array_shift($keys);

        if (!count($keys)) {
            $keys = array_keys($array);
        }

        foreach ($keys as $key) {
            if ($array[$key]) {
                return $array[$key];
            }
        }

        return '';
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgsmoothgallery/pi1/class.tx_rgsmoothgallery_pi1.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgsmoothgallery/pi1/class.tx_rgsmoothgallery_pi1.php'];
}
