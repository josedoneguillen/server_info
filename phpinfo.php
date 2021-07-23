<?php






$response = [];

$response['extensions'] = get_loaded_extensions();

$response['directive']['allow_url_fopen'] = ini_get_all('core')['allow_url_fopen'];
$response['directive']['allow_url_include'] = ini_get_all('core')['allow_url_include'];
$response['directive']['max_execution_time'] = ini_get_all('core')['max_execution_time'];
$response['directive']['display_errors'] = ini_get_all('core')['display_errors'];
$response['directive']['max_input_vars'] = ini_get_all('core')['max_input_vars'];
$response['directive']['max_input_time'] = ini_get_all('core')['max_input_time'];
$response['directive']['memory_limit'] = ini_get_all('core')['memory_limit'];
$response['directive']['post_max_size'] = ini_get_all('core')['post_max_size'];
$response['directive']['upload_max_filesize'] = ini_get_all('core')['upload_max_filesize']; 
$response['directive']['enable_dl'] = ini_get_all('core')['enable_dl'];
$response['directive']['file_uploads'] = ini_get_all('core')['file_uploads'];

$response['directive']['session.gc_maxlifetime'] = ini_get_all('session')['session.gc_maxlifetime'];
$response['directive']['session.save_path'] = ini_get_all('session')['session.save_path'];

$response['directive']['zlib.output_compression'] = ini_get_all('zlib')['zlib.output_compression'];
$response['directive']['opcache.enable'] = opcache_get_configuration()['directives']['opcache.enable'];
$response['directive']['opcache.enable_cli'] = opcache_get_configuration()['directives']['opcache.enable_cli'];



echo json_encode($response);





