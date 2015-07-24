<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'SmoothGallery for TYPO3',
    'description' => 'Slideshow & Gallery. Shows images from directory folders, from records and inside tt_content (Element "Text with images") DAM and tt_news. +Thumbnail-Browser.',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '1.6.1',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => 'uploads/tx_rgsmoothgallery/',
    'modify_tables' => '',
    'clearcacheonload' => 0,
    'lockType' => '',
    'author' => 'Georg Ringer (just2b)',
    'author_email' => 'http://www.just2b.com',
    'author_company' => 'http://www.just2b.com',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-7.99.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
);
