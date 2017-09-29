<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Routing\Router;


class ApiController extends AppController {

    public function beforeFilter(Event $event) {
        //header('Access-Control-Allow-Origin: *');
        //header("Content-Type: application/json;charset=utf-8");
        $this->viewBuilder()->layout('');
    }

    /**
     * Create new reponse json by array $response
     * @param type $response
     */
    private function responseJson($response) {
        if ($response['code'] == 0) {
            $this->log('ERROR API: ' . $response['message']);
        }
        $this->viewClass = 'Json';
        $this->set('response', $response);
        $this->set('_serialize', array('response'));
    }

    /**
     * Check user login, if exist, create token and return success and token for client
     * @param varchar $device
     * @param varchar $password
     * @param varchar $username
     * @return type json
     */
    public function loginApp() {
        $result = array('code' => 0, 'message' => "error login");
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['username']) && !empty($data['password']) && !empty($data['device'])) {
                $userTable = TableRegistry::get('Users');
                $checkUser = $userTable->find('all')
                        ->where(['username' => $data['username']])
                        ->first();
//                $checkUser = $userTable->find('all')
//                        ->where(['username' => $data['username'], 'password' => $passwordHasher->hash($data['password'])])
//                        ->first();
                if (!empty($checkUser)) {
                    if (($data['password'] == $checkUser->password) && ($checkUser->del_flg == 0) && ($checkUser->app_web_flg == 1)) {
                        $tokenTable = TableRegistry::get('Tokens');
                        $device_id = $this->getDeviceId($data['device']);
                        $checkToken = $tokenTable->find('all')
                                ->where(array('device_id' => $device_id, 'user_id' => $checkUser->id))
                                ->first();
                        
                        //START get domain by web_id
                        $webId = $checkUser['web_id'];
                        $domainByWebId = "";
                        if(!empty(Configure::read('DomainByWebId')[$webId])){
                            $domainByWebId = Configure::read('DomainByWebId')[$webId];
                        }
                        //END get domain by web id
                        if (!empty($checkToken)) {
                            $result = array('code' => 1, 'token' => $checkToken->token, 'user_id' => $checkUser->id,'domainByWebId'=>$domainByWebId);
                        } else {
                            $token = $tokenTable->newEntity();
                            $token_convert = $checkUser->id . md5($data['device']);
                            $token->device_id = $device_id;
                            $token->user_id = $checkUser->id;
                            $token->token = $token_convert;
                            if ($tokenTable->save($token)) {
                                $result = array('code' => 1, 'token' => $token_convert, 'user_id' => $checkUser->id,'domainByWebId'=>$domainByWebId);
                            } else {
                                $result = array('code' => 0, 'message' => "Error save data");
                            }
                        }
                    } else {
                        $result = array('code' => 0, 'message' => "Invalid username or password");
                    }
                } else {
                    $result = array('code' => 0, 'message' => "Invalid username or password");
                }
            } else {
                $result = array('code' => 0, 'message' => "error param");
            }
        }
        return $this->responseJson($result);
    }

    /**
     * Receive data survey from client, create new survey, return token route. With token route, client continues to send image
     * @return type json
     */
    public function createRoute() {

        $this->loadModel('Routes');

//        $array = [[
//        'token_login' => '22b06ce6d3ea3714ed5beee41351b2bfa8',
//        'token_route' => '1',
//        'time_start' => '2016-09-29 15:53:38',
//        'time_end' => '2016-09-29 15:53:38',
//        'country' => 'vn',
//        'device' => '5b5b921b71bd169d'
//        ]];
//        echo json_encode($array);die;
        $result = array('code' => 0, 'message' => "error create route");
        if ($this->request->is('post')) {
            $dataJson = $this->request->data;
            // data index: file_json
            if (!empty($dataJson['file_json'])) {
                // decode for get data
                $dataDecode = json_decode($dataJson['file_json']); 
                // check data after decode
                if (!empty($dataDecode)) {
                    $checkE = 0;
                    // data is many route, check each route
                    foreach ($dataDecode as $data) { 
                        if (!empty($data->token_login) // check token
                                && !empty($data->token_route)  // check token route
                                && !empty($data->device) // check device
                                && !empty($data->country) // check country
                                && !empty($data->time_start) // check time start
                                && !empty($data->time_end) // check time end
                        ) {
                           
                            $checkLogin = $this->checkTokenLogin($data->token_login);
                            if (!empty($checkLogin->id)) {
                                if($this->checkExistRoute($checkLogin->id, $data->token_route)){
                                    $result = array('code' => 1, 'message' => "success");
                                    return $this->responseJson($result);
                                }
                                $routeTable = TableRegistry::get('Routes');
                                $route = $routeTable->newEntity();
                                $route->token_id = $checkLogin->id;
                                $route->token_route = $data->token_route;
                                $route->device_id = $this->getDeviceId($data->device);
                                $route->time_start = $data->time_start;
                                $route->time_end = $data->time_end;
                                $route->country = $data->country;
                                $route->user_id = $checkLogin->user_id;
                                if (!empty($data->last_address)) {
                                    $route->last_address = $data->last_address;
                                }else{
                                    $route->last_address = "";
                                }
                                //tạo 調査NO
                                $config = [
                                    'fields' => [
                                        'max' => 'MAX(Routes.number)',
                                    ],
                                    'conditions' => [
                                        'Routes.device_id' => $this->getDeviceId($data->device)
                                    ],
                                ];
                                $query = $this->Routes->find('all', $config)->first();
                                if ($query->max) {
                                    $route->number = $query->max + 1;
                                } else {
                                    $route->number = 1;
                                }
//                                echo 'test';
//                                debug($route);
                                //
                                  
                                if ($routeTable->save($route)) {
//                                    debug($route);
//                                    die;
                                    $route_id = $route->id;
                                    $locationTable = TableRegistry::get('Locations');
                                    $datalocation = json_decode($data->location);
                                    // each route have many locations
                                    if (!empty($datalocation)) {
                                        // check each location at route
                                        foreach ($datalocation as $value) {
                                            if (isset($value->lat) && isset($value->lng)) {
                                                $location = $locationTable->newEntity();
                                                $location->latitude = $value->lat;
                                                $location->longitude = $value->lng;
                                                $location->route_id = $route_id;
                                                $location->image_id = 0;
                                                $location->type = 1;
                                                // new catch time (for save time when catch location
                                                if (isset($value->catch_time)) {
                                                    $location->catch_time = $value->catch_time;
                                                }
                                                // end new                                                
                                                $locationTable->save($location);
                                            }
                                        }
                                    }
                                    
                                } else {
                                    $checkE = 1;
                                    $result = array('code' => 0, 'message' => "error save route");
                                }
                            } else {
                                $checkE = 1;
                                $result = array('code' => 0, 'message' => "Login required");
                            }
                        } else {
                            $checkE = 1;
                            $result = array('code' => 0, 'message' => "error param");
                        }
                    }
                    if ($checkE == 0) {
                        $result = array('code' => 1, 'message' => "success");
                    }
                }
            }
        }
        return $this->responseJson($result);
    }

    /**
     * From data image at device, send and update to uploaded survey through `token_route`
     * @return type json
     */
    public function updateLocation() {
        $result = array('code' => 0, 'message' => "error update location");
        if ($this->request->is('post')) {
            $data = $this->request->data;
            //pr($data);die;
            if (!empty($data['token_route']) && !empty($data['token_login']) && !empty($data['image_upload']) && !empty($data['lat']) && !empty($data['lng']) && !empty($data['rotation'])
            ) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $routeTable = TableRegistry::get('Routes');
                    $checkRoute = $routeTable->find('all')
                            ->where(array('token_route' => $data['token_route'], 'token_id' => $checkLogin->id))
                            ->first();
                    if (!empty($checkRoute->id)) {
                        $route_id = $checkRoute->id;
                        $folder_save = "image";
                        $upload = $this->upload_file_api($data['image_upload'], $folder_save, $route_id);
                        if (!empty($upload)) {
                            $imageTable = TableRegistry::get('Images');
                            $image = $imageTable->newEntity();
                            $image->name = $upload['file_name'];
                            $image->width = $upload['width'];
                            $image->height = $upload['height'];
                            $image->size = $upload['size'];
                            $image->rotation = $data['rotation'];
                            $image->route_id = $route_id;
                            $image->comment = $data['comment'];
                            if ($imageTable->save($image)) {
//                                $this->log('6');
                                $locationTable = TableRegistry::get('Locations');
                                $location = $locationTable->newEntity();
                                $location->latitude = $data['lat'];
                                $location->longitude = $data['lng'];
                                if (!empty($data['catch_time'])) {
                                    $location->catch_time = $data['catch_time'];
                                }
                                $location->longitude = $data['lng'];
                                $location->route_id = 0;
                                $location->image_id = $image->id;
                                $location->type = 2;
                                $locationTable->save($location);
                                $result = array('code' => 1, 'message' => "success");
                            } else {
                                $result = array('code' => 0, 'message' => "Error save image");
                            }
                        } else {
                            $result = array('code' => 0, 'message' => "Error upload file");
                        }
                    } else {
                        $result = array('code' => 0, 'message' => "Not exist token_route");
                    }
                } else {
                    $result = array('code' => 0, 'message' => "Login required");
                }
            } else {
                $result = array('code' => 0, 'message' => "error param");
            }
        }
        return $this->responseJson($result);
    }
    
    public function completeSurvey(){
        $result = array('code' => 0, 'message' => "error token");
        $this->loadModel('Routes');
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login']) && !empty($data['token_route'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $routeTable = TableRegistry::get('Routes');
                    $checkRoute = $routeTable->find('all')
                            ->where(array('token_route' => $data['token_route'], 'token_id' => $checkLogin->id))
                            ->first();
                    if (!empty($checkRoute->id)) {
                        $language = 'English';
                        if (isset($data['language'])) {
                            $language = $data['language'];
                        }
                        
                        // send mail
                        $shell = Configure::read('Shell.SendMailAfterCompleteSurvey') . ' ' . $checkLogin->user_id . ' ' . $checkRoute->id . ' ' . $language;
                        $this->log($shell);
                        shell_exec($shell . ' > /dev/null 2>/dev/null &');                        
                        $result = array('code' => 1, 'message' => 'success');
                    }                    
                }                
            }
        }        
        return $this->responseJson($result);
    }
    
    /**
     * Get language version for app
     */
    public function appLanguageVersion(){
        $result = array('code' => 0, 'message' => "error token");
        $this->loadModel('Versions');
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $version = $this->Versions->findAppLanguageVersion();
                    $result = array('code' => 1, 'version' => $version);
                }                
            }
        }
        return $this->responseJson($result);
    }
    
    /**
     * Get all language data for app at last version
     * @return type
     */
    public function appLanguageData() {
        $result = array('code' => 0, 'message' => "error token");
        $this->loadModel('AppLanguageDetails');
        $this->loadModel('AppLanguageKeys');
        $this->loadModel('AppLanguages');
        $this->loadModel('AppTranslateLanguages');
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $languageData = $this->AppLanguageDetails->findAllDataLanguage();
                    $allLanguage= $this->AppLanguages->findAllLanPublish();
                    $translateData= $this->AppTranslateLanguages->findAllTranslateLanguage($allLanguage);
                    $dataTranslate = array();
                    $dataTranslate[] = $translateData['dataTranslate'];
                    $laguageDefaultApp = Configure::read('language_default_app',4);
                    $result = array(
                        'code' => 1,
                        'laguageDefaultApp' => $laguageDefaultApp,
                        'dataKeyLanguage'=>$translateData['dataKeyLanguage'], 
                        'translateLanguage'=>$dataTranslate, 
                        'data' => $languageData
                    );
                }                
            }
        }        
        return $this->responseJson($result);
    }
    
    /**
     * Get all user and setting send user
     * @return type
     */
    public function settingSendMailUsers() {
        $result = array('code' => 0, 'message' => "error token");
        $this->loadModel('Users');
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $data = $this->Users->getAllActiveUser($checkLogin->user_id);
                    $result = array('code' => 1, 'data' => $data);
                }                
            }
        }        
        return $this->responseJson($result);
    }
    
    /**
     * Set active or not active send mail for specific user
     * @return type
     */
    public function setSendMailUsers() {
        $result = array('code' => 0, 'message' => "error token");
        $this->loadModel('SendMailUsers');                
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login']) && !empty($data['user_id']) && isset($data['flag'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    if ($data['flag'] != 1) $data['flag'] = 0;
                    $sendMailUserId = $this->SendMailUsers->findBySenderAndReceiver($checkLogin->user_id, $data['user_id']);
                    $dataSave = array(
                        'sender' => $checkLogin->user_id,
                        'receiver' => $data['user_id'],
                        'active_flg' => $data['flag']
                    );
                    if ($sendMailUserId){
                        $dataSave['id'] = $sendMailUserId;
                    }
                    $entity = $this->SendMailUsers->newEntity($dataSave);
                    if ($this->SendMailUsers->save($entity)){
                        $result = array('code' => 1, 'message' => 'Save successfully');
                    }
                }                
            }
        }        
        return $this->responseJson($result);
    }
    /**
     * Get all version
     * @return type
     */
    public function getAllVersion() {
        $result = array('code' => 0, 'message' => "error");
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login']) && isset($data['version_type'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $deviceTable = TableRegistry::get('Versions');
                    $version = $deviceTable->find('all')
                            ->select(['version', 'released','content'])
                            ->where(['device' => $data['version_type'],'del_flg' => 0])
                            ->order(['released'=>'DESC'])
                            ->all()->toArray();
                    $result = array('code' => 1, 'data' => $version);
                }else {
                    $result = array('code' => 0, 'message' => "Login required");
                }                
            }else {
                $result = array('code' => 0, 'message' => "error param");
            }
        }        
        return $this->responseJson($result);
    }
    /**
     * Get slide
     * @return type
     */
    public function getSlide() {
        $result = array('code' => 0, 'message' => "error");
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['token_login']) && !empty($data['key_language']) && isset($data['type_language'])) {
                $checkLogin = $this->checkTokenLogin($data['token_login']);
                if (!empty($checkLogin->id)) {
                    $language = $data['key_language'];
                    $type = $data['type_language'];
                    $this->loadModel('Guide');
                    $config = array(
                        'join' => [
                            'users' => [
                                'table' => 'm_app_language',
                                'type' => 'LEFT',
                                'alias' => 'AppLanguages',
                                'conditions' =>
                                [
                                    'AppLanguages.id = Guide.language_id',
                                ],
                            ],
                        ],
                        'conditions' => array(
                            'AppLanguages.language'=>$language,
                            'AppLanguages.del_flg'=>0,
                            'Guide.del_flg'=>0,
                            'Guide.type' =>$type
                        ),
                        'fields' => array('Guide.id', 'Guide.guide_text', 'Guide.image'),
                        'order' => array('Guide.created'),
                    );
                    $data = $this->Guide->find('all', $config)->all()->toArray();
                    $path_img = DEFINE_DOMAIN_NAME."files/slide-app/";
                    $result = array('code' => 1, 'data' => $data,'path_img'=>$path_img);
                }else {
                    $result = array('code' => 0, 'message' => "Login required");
                }                
            }else {
                $result = array('code' => 0, 'message' => "error param");
            }
        }        
        return $this->responseJson($result);
    }
    /**
     * Get traslate language
     * @return type
     */
//    public function getTranslateLanguage() {
//        $result = array('code' => 0, 'message' => "error");
//        if ($this->request->is('post')) {
//            $data = $this->request->data;
//            if (!empty($data['token_login']) && isset($data['key_language'])) {
//                $checkLogin = $this->checkTokenLogin($data['token_login']);
//                if (!empty($checkLogin->id)) {
//                    $this->loadModel('AppLanguages');
//                    $language = $this->AppLanguages->findRowByLanguagePublicNone($data['key_language']);
//                    $translate = array();
//                    if(!empty($language['id'])){
//                        $this->loadModel('AppTranslateLanguages');
//                        $translate = $this->AppTranslateLanguages->getTranslateByLanguage($language);
//                    }
//                    $result = array('code' => 1, 'data' => $translate);
//                }else {
//                    $result = array('code' => 0, 'message' => "Login required");
//                }                
//            }else {
//                $result = array('code' => 0, 'message' => "error param");
//            }
//        }        
//        return $this->responseJson($result);
//    }

    
    
    // FUNCTION PRIVATE
    /**
     * Get device name
     * @param type $device_name
     * @return type
     */
    private function getDeviceId($device_name) {
        $deviceTable = TableRegistry::get('Devices');
        //$exists = $deviceTable->exists(array('name' => $data['device']));
        $checkDevice = $deviceTable->find('all')->where(array('name' => $device_name))->first();
        $device_id = 1;
        if (empty($checkDevice)) {
            $device = $deviceTable->newEntity();
            $device->name = $device_name;
            if ($deviceTable->save($device)) {
                $device_id = $device->id;
            }
        } elseif (!empty($checkDevice->id)) {
            $device_id = $checkDevice->id;
        }
        return $device_id;
    }

    /**
     * Check exist token login
     * @param type $token_login
     * @return type
     */
    private function checkTokenLogin($token_login) {
        $result = 0;
        $tokenTable = TableRegistry::get('Tokens');
        $checkToken = $tokenTable->find('all')
                ->where(array('token' => $token_login))
                ->first();
        if (!empty($checkToken)) {
            $result = $checkToken;
        }
        
        return $result;
    }
    /**
     * Check exit survey
     * @param type $token_id
     * @param type $token_route
     * @return boolean
     */
    private function checkExistRoute($token_id,$token_route) {
        $routeTable = TableRegistry::get('Routes');
        $checkRoute = $routeTable->find('all')
                ->where(array('token_id' => $token_id,'token_route'=>$token_route))
                ->first();
        if (!empty($checkRoute)) {
            return true;
        }else{
            return false;
        }
    }
    
    public function saveImageDownload() {
        $data = $this->request->data;
        $result = array('status' => 1);
        if (isset($data['link']) && isset($data['route'])) {
            if (count($data['link']) == 1) {
                $result['link'] = DEFINE_DOMAIN_NAME . '/files/image/' . $data['route'] . '/' . $data['link'][0];
                $result['name'] = $data['link'][0];
                echo json_encode($result);
                die;
            } else {
                // zip file
                $dir = '/var/www/html/webroot/files/image/' . $data['route'] . '/download';
                @mkdir($dir, 0777, true);
//                echo $dir;die;
                $files = glob($dir . '/*'); // get all file names
                foreach ($files as $file) { // iterate files
                    if (is_file($file))
                        unlink($file); // delete file
                }

                foreach ($data['link'] as $img_name) {
                    @copy(DEFINE_DOMAIN_NAME . '/files/image/' . $data['route'] . '/' . $img_name, $dir . '/' . $img_name);
                }

                $zipFile = 'download_image_' . $data['route'] . '.zip';
                $this->zipFile($dir, $dir . '/' . $zipFile);
                $linkHomePage = Router::url('/', true);
                $result['link'] = $linkHomePage . 'files/image/' . $data['route'] . '/download/' . $zipFile;
                $result['name'] = $zipFile;
                echo json_encode($result);
                die;
                // return
            }
        } else {
            $result['status'] = 0;
            echo json_encode($result);
            die;
        }
    }

    private function zipFile($diffDir, $zipFile) {
        $rootPath = realpath($diffDir);
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($rootPath), \RecursiveIteratorIterator::LEAVES_ONLY
        );
        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
//            if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            if (!preg_match('/\/\.{1,2}$/', $file)) {
                $zip->addFile($filePath, $relativePath);
            }
//            }
        }
//        die;
        // Zip archive will be created only after closing object
        $zip->close();
    }
}
