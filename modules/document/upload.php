<?php

/* ========================================================================
 * Open eClass 3.0
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2014  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */

/**
 * @file upload.php
 * @brief upload form for subsystem documents
 */

$require_admin = defined('COMMON_DOCUMENTS');
$require_current_course = !(defined('COMMON_DOCUMENTS') or defined('MY_DOCUMENTS'));
$require_login = true;

require_once '../../include/baseTheme.php';
require_once 'modules/document/doc_init.php';
require_once 'modules/drives/clouddrive.php';
require_once 'include/lib/fileDisplayLib.inc.php';
require_once 'include/course_settings.php';

doc_init();

$can_upload_replacement = $can_upload;
if ($subsystem == MAIN and get_config('enable_docs_public_write') and
    setting_get(SETTING_DOCUMENTS_PUBLIC_WRITE)) {
        $can_upload = true;
}

if (defined('COMMON_DOCUMENTS')) {
    $data['menuTypeID'] = 3;
    $toolName = $langCommonDocs;
} elseif (defined('MY_DOCUMENTS')) {
    if ($session->status == USER_TEACHER and !get_config('mydocs_teacher_enable')) {
        redirect_to_home_page();
    }
    if ($session->status == USER_STUDENT and !get_config('mydocs_student_enable')) {
        redirect_to_home_page();
    }
    $data['menuTypeID'] = 1;
    $toolName = $langMyDocs;
} else {
    $data['menuTypeID'] = 2;
    $toolName = $langDoc;
}

enableCheckFileSize();

if (defined('EBOOK_DOCUMENTS')) {
    $navigation[] = array('url' => 'edit.php?course=' . $course_code . '&amp;id=' . $ebook_id, 'name' => $langEBookEdit);
}

if (isset($_GET['uploadPath'])) {
    $data['uploadPath'] = q($_GET['uploadPath']);
} else {
    $data['uploadPath'] = '';
}

$data['can_upload'] = $can_upload;
$data['backUrl'] = documentBackLink($data['uploadPath']);
$data['upload_target_url'] = $upload_target_url;

if ($can_upload) {
    $navigation[] = array('url' => $data['backUrl'], 'name' => $pageName);

    $data['languages'] = $fileLanguageNames;
    $data['copyrightTitles'] = $copyright_titles;

    $data['pendingCloudUpload'] = CloudDriveManager::getFileUploadPending();

    $data['externalFile'] = false;
    if ($data['pendingCloudUpload']) {
        $pageName = $langDownloadFile;
    } else if (isset($_GET['ext'])) {
        $data['externalFile'] = true;
        $pageName = $langExternalFile;
    } else {
        $pageName = $langDownloadFile;
    }
    $data['backButton'] = action_bar(array(
        array('title' => $langBack,
            'url' => $data['backUrl'],
            'icon' => 'fa-reply',
            'level' => 'primary-label')));
}

view('modules.document.upload', $data);
