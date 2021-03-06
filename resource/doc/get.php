<?php
/**
 * Created by PhpStorm.
 * User: mvillalobos
 * Date: 5/6/2018
 * Time: 4:00 PM
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../object/Auth.php';
include_once '../../object/User.php';
include_once '../../object/Document.php';
include_once '../../object/KnowledgeBase.php';
include_once '../../object/Folder.php';
include_once '../../object/Register.php';
include_once '../../service/AuthService.php';
include_once '../../service/UserService.php';
include_once '../../service/DocumentService.php';
include_once '../../service/KnowledgeBaseService.php';
include_once '../../service/FolderService.php';
include_once '../../service/RegisterService.php';
include_once '../../util/Constants.php';
include_once '../../util/ValidateHelper.php';

$id = $_REQUEST['id'];
$headers = apache_request_headers();
$permission = null;

try {
    $db = new Database();
    $conn = $db->getConnection();

    if(!validateInteger($id)){
        throw new Exception("Not a valid document identifier");
    }

    $docService =  new DocumentService($conn);
    $doc = $docService->getById($id);

    $kbService = new KnowledgeBaseService($conn);
    $kb = $kbService->getById($doc->getKnowledgeBase());

    $folder = $docService->getRoute($doc->getFolder());

    $token = $headers['Authorization'];
    if(is_null($token)){
        throw new Exception("Users need to be logged in");
    }
    $authService = new AuthService($conn);
    $auth = $authService->getAuth($token);

    $userService = new UserService($conn);
    $user = $userService->getById($auth->getUser());

    $permission = $kbService->getUserPermission($user->getId(), $kb->getId());

    if($kb->getPrivacy() != 1)
    {
        if(!$kbService->checkPermission($user->getId(), $kb->getId(), "read")){
            throw new Exception("User has no permission for this operation");
        }
    }

    echo json_encode(array(
        'document' => array(
            'id' => $doc->getId(),
            'name' => $doc->getName(),
            'description' => $doc->getDescription(),
            'content' => $doc->getContent(),
            'tags' => $doc->getTags()
        ),
        'knowledge_base' => array(
            'id' => $kb->getId(),
            'name' => $kb->getName()
        ),
        'folder' => $folder,
        'role' => $permission,
        'success' => true
    ));
} catch (Exception $e) {
    echo json_encode(array('title' => 'Error', 'message' => $e->getMessage(), 'success' => false));
}