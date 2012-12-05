<?php
include '../general.inc.php';
//error_reporting(0);
$options = array(
            'script_url' => $URL_ROOT.'admin/uploads/',
            'upload_dir' => $DIR_ROOT.'/uploads/',
            'upload_url' => $URL_ROOT.'uploads/',
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,
            'image_versions' => array(
                'medium' => array(
                    'upload_dir' => $DIR_ROOT.'/uploads/medium/',
                    'upload_url' => $URL_ROOT.'/uploads/medium/',
                    'max_width' => 248,
                    'max_height' => 248,
                    'jpeg_quality' => 95
                ),
                'thumbnail' => array(
                    'upload_dir' => $DIR_ROOT.'/uploads/thumbs/',
                    'upload_url' => $URL_ROOT.'/uploads/thumbs/',
                    'max_height' => 80
                )
            )
        );
$custom_options = json_decode($_REQUEST["custom_options"]);
if ($custom_options) {
    $options = array_replace_recursive($options, (array) $custom_options);
}
$upload_handler = new Upload($options);
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        break;
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            $upload_handler->delete();
        } else {
            $upload_handler->post();
        }
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
}
?>