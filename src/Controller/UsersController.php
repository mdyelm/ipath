<?php

namespace App\Controller;

use App\Controller\UserBaseController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Network\Response;
use Cake\Network\Request;
use Cake\Mailer\Email;
use Cake\I18n\Time;
use Cake\I18n\Date;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;

//use App\Utils\HTML2Doc;

class UsersController extends UserBaseController {

    public function initialize() {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadModel('Users');
        $this->loadModel('Guide');
        $this->loadModel('Versions');
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $user = $this->viewVars['authUser'];
        $arrayActionAllow = array('datagps', 'dowloadCsv', 'createDowCsv');
        $actionNow = $this->request->action;
        if (!$user['admin_flg'] && !in_array($actionNow, $arrayActionAllow)) {
            $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
        }
        // START get data device 
        $userDevice = $this->Users->find('all')
                        ->hydrate(false)
                        ->select(['Users.username', 'Users.id'])
                        ->where([
                            'Users.admin_flg' => 0,
                            'Users.del_flg' => 0,
                            'Users.web_id' => DEFINE_WEB_ID//check WEB_ID
                        ])->all()->toArray();
        ;
        $this->set(['userDevice' => $userDevice]);
        // END get data device        
        $routesTable = TableRegistry::get('Routes');
        $locationTable = TableRegistry::get('Locations');

        // start check filter
        $filter = "";
        $vesion = "";
        $type_filter = "";
        $orderBy = array('Routes.id' => 'DESC');
        if (!empty($this->request->query['filter']) && !empty($this->request->query['type'])) {
            $filter = $this->request->query['filter'];
            $type_filter = $this->request->query['type'];
            if ($type_filter != "desc" && $type_filter != "asc") {
                $type_filter = "desc";
            }
            if ($filter == "user") {
                $orderBy = array('Users.username' => $type_filter, 'Routes.time_start' => 'DESC');
            } elseif ($filter == "id") {
                $orderBy = array('Routes.id' => $type_filter);
            } elseif ($filter == "date") {
                $orderBy = array('Routes.time_start' => $type_filter);
            }
        }
        // get vesion
        $version = $this->Versions->findWebLanguageVersion();
        $this->set(['filter' => $filter, 'type_filter' => $type_filter, 'versionWeb' => $version]);
        // end filter 
        
        // count
        $query = $routesTable->find('all')
                ->hydrate(false)
                ->join([
                    'locations' => [
                        'table' => 'm_locations',
                        'type' => 'LEFT OUTER',
                        'alias' => 'Locations',
                        'conditions' =>
                        [
                            'Locations.route_id = Routes.id',
                            'Locations.id = (SELECT MAX(id) FROM m_locations WHERE route_id = Routes.id)'
                        ],
                    ],
                    'devices' => [
                        'table' => 'm_devices',
                        'type' => 'LEFT',
                        'alias' => 'Devices',
                        'conditions' =>
                        [
                            'Routes.device_id = Devices.id',
                        ],
                    ],
                    'user' => [
                        'table' => 'm_users',
                        'type' => 'LEFT',
                        'alias' => 'Users',
                        'conditions' =>
                        [
                            'Users.id = Routes.user_id',
                        ],
                    ]
                ])
                ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.number', 'Routes.country', 'Devices.name'])
                ->distinct('Routes.id')
                ->order($orderBy)
                ->where(['Routes.delete_flg' => 0,'Users.web_id' => DEFINE_WEB_ID, 'Users.del_flg' => 0, 'Locations.latitude IS NOT' => null]);
        
        $routes = $query
                ->limit(10)
                ->offset(0);
        $arrayIndexReverse = $this->_getArrayIndexRoutes();
        $this->set(['routes' => $routes, 'arrayIndexReverse' => $arrayIndexReverse]);   
    }

    public function index() {

        $checkNo = 0;
        if (!empty($this->request->query['page'])) {
            $checkNo = $this->request->query['page'];
        }
        $config = [
            'fields' => [
                'Users.id',
                'Users.username',
                'Users.email',
                'Users.company_email',
                'Users.app_web_flg',
                'Users.admin_flg',
                'Users.created',
            ],
            'limit' => 20,
            'maxLimit' => 20,
            'conditions' => [
                'Users.del_flg' => 0,
                'Users.web_id' => DEFINE_WEB_ID, //check WEB_ID
            ],
            'order' => [
                'created' => 'desc'
            ],
        ];
//        Total number user
        $cnt = $this->Users->find('all', array(
                    'conditions' => ['Users.del_flg' => 0, 'Users.web_id' => DEFINE_WEB_ID]
                ))->count();
        $query = $this->Users->find();
        //search
        $name = $this->request->query('name');

        if ($name) {
            $query->where(['OR' => [
                    'Users.username like' => "%" . $name . "%", 'Users.email like' => "%" . $name . "%"
                ]]
            );
        }
        try {
            $results = $this->Paginator->paginate($query, $config);
        } catch (NotFoundException $e) {
            $paging = $this->request->query['page'];
            $this->redirect(array('action' => 'index', 'page' => $paging - 1));
        }
        $this->set(compact('results', 'name', 'checkNo', 'cnt'));
    }

    public function edit($id) {
        $config = [
            'fields' => [
                'Users.id',
                'Users.email',
                'Users.company_email',
                'Users.app_web_flg',
                'Users.password',
                'Users.username',
                'Users.admin_flg',
                'Users.del_flg',
            ],
            'conditions' => [
                'Users.id' => $id,
                'Users.del_flg' => 0,
                'Users.web_id' => DEFINE_WEB_ID, //check WEB_ID
            ],
        ];
        $result = $this->Users->find('all', $config)->first();
        if (!$result) {
            $this->redirect(array('action' => 'index'));
        }
        
        $cnt = $this->Users->find('all', array(
                    'conditions' => ['Users.del_flg' => 0, 'Users.web_id' => DEFINE_WEB_ID, 'Users.id <=' => $id,]
                ))->count();
        $this->set('userIndex', $cnt);
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data()['username'] = str_replace(' ', '', $this->request->data()['username']);

            $data = $this->request->data();
            if (isset($data['del_flg']) && $data['del_flg'] == 1) {
                $id = $data['id'];
                //del author
                $dateNow = new \DateTime();
                $dateFormat = $dateNow->format('YmdHis');
                $userNameDel = $result['username'] . "_DEL" . $dateFormat;
                $query = $this->Users->query();
                $query->update()
                        ->set(['username' => $userNameDel, 'del_flg' => true])
                        ->where([
                            'id' => $data['id'],
                            'web_id' => DEFINE_WEB_ID//check WEB_ID
                        ])
                        ->execute();
                return $this->redirect(['action' => 'index']);
            }
            if ($data['admin_flg'] == 1) {
                $data['app_web_flg'] = 1;
            }
            if ($data['app_web_flg'] == 0) {
                $this->Users->patchEntity($result, $data, [
                    'validate' => 'OnlyCheckEdit'
                ]);
            } else {
                $this->Users->patchEntity($result, $data, [
                    'validate' => 'adminEdit'
                ]);
            }
            if ($this->Users->save($result)) {
                $this->Flash->success(__('The user has been saved'));
                $this->redirect(array('action' => 'edit', $id));
            }
        }
        $this->set(compact('result'));
    }

    public function add() {
        $result = $this->Users->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data();
            $data['username'] = str_replace(' ', '', $data['username']);
            if ($data['admin_flg'] == 1) {
                $data['app_web_flg'] = 1;
            }
            if ($data['app_web_flg'] == 0) {
                $this->Users->patchEntity($result, $data, [
                    'validate' => 'OnlyCheckAdd'
                ]);
            } else {
                $this->Users->patchEntity($result, $data, [
                    'validate' => 'adminAdd'
                ]);
            }
            //check WEB_ID
            $result['web_id'] = DEFINE_WEB_ID;
            if ($this->Users->save($result)) {
                // update table send mail user, config default send mail when create survey
                $this->loadModel('SendMailUsers');
                $sendMailUser = $this->SendMailUsers->newEntity();
                $sendMailUser->sender = $result->id;
                $sendMailUser->receiver = $result->id;
                $sendMailUser->active_flg = 1;
                $this->SendMailUsers->save($sendMailUser);

                $Email = new Email('default');
                //send email company
                $Email->to($data['company_email'])
                        ->template('register')
                        ->emailFormat('html')
                        ->subject('Your account has been registered at CSM')
                        ->viewVars(array('data' => array('username' => $data['username'], 'password' => $data['password'])))
                        ->send();
                if (!empty($data['email'])) {
                    //send gmail
                    $Email->to($data['email'])
                            ->template('register')
                            ->emailFormat('html')
                            ->subject('Your account has been registered at CSM')
                            ->viewVars(array('data' => array('username' => $data['username'], 'password' => $data['password'])))
                            ->send();
                }
//                $this->Flash->success('The user has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->set(compact('result'));
    }

    public function delete() {
        $data = $this->request->data;
        $id = $data['id'];
        //del author
        $query = $this->Users->query();
        $query->update()
                ->set(['del_flg' => true])
                ->where([
                    'id' => $id,
                    'web_id' => DEFINE_WEB_ID//check WEB_ID
                ])
                ->execute();

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Show version android, ios, web
     */
    public function version() {
        $versionsTable = TableRegistry::get('Versions');
        $data = $versionsTable->findAllVersion();
        $this->set('web', $data[0]);
        $this->set('iOS', $data[1]);
        $this->set('android', $data[2]);
    }

    public function datagps() {
        $filtergps = "";
        $type_filtergps = "";
        $orderBy = array('Routes.id' => 'DESC');
        if (!empty($this->request->query['filtergps']) && !empty($this->request->query['typegps'])) {
            $filtergps = $this->request->query['filtergps'];
            $type_filtergps = $this->request->query['typegps'];
            if ($type_filtergps != "desc" && $type_filtergps != "asc") {
                $type_filtergps = "desc";
            }
            if ($filtergps == "user") {
                $orderBy = array('Users.username' => $type_filtergps, 'Routes.time_start' => 'DESC');
            } elseif ($filtergps == "date") {
                $orderBy = array('Routes.time_start' => $type_filtergps);
            } else {
                $orderBy = array('Routes.id' => $type_filtergps);
            }
        }
        $config = [
            'fields' => [
                'Routes.id',
                'Routes.time_start',
                'Users.username',
                'Routes.country',
                'Routes.delete_flg',
                'Devices.name',
            ],
            'join' => [
                'users' => [
                    'table' => 'm_users',
                    'type' => 'LEFT',
                    'alias' => 'Users',
                    'conditions' =>
                    [
                        'Routes.user_id = Users.id',
                    ],
                ],
                'devices' => [
                    'table' => 'm_devices',
                    'type' => 'LEFT',
                    'alias' => 'Devices',
                    'conditions' =>
                    [
                        'Routes.device_id = Devices.id',
                    ],
                ],
            ],
            'conditions' => [
                'Users.del_flg' => 0,
                'Users.web_id' => DEFINE_WEB_ID//check WEB_ID
            ],
            'order' => $orderBy
        ];
        $this->loadModel('Routes');
        $result = $this->Routes->find('all', $config)->all()->toArray();
        $arrayIndex = $this->_getArrayIndexRoutes();
        $this->set(compact('result', 'arrayIndex'));
        $this->set(['filtergps' => $filtergps, 'typegps' => $type_filtergps]);
    }

    public function dowloadCsv() {
        if (!empty($this->request->data['idRoute'])) {
            $dataID = $this->request->data['idRoute'];
            $config = [
                'fields' => [
                    //'id_location' =>'Locations.id',
                    'Users.username',
                    'Devices.name',
                    'id_route' => 'Routes.id',
                    'time_end_route' => 'Routes.time_end',
                    'Locations.longitude',
                    'Locations.latitude',
                    'Locations.catch_time',
                    'PHOTO' => 'IF(Locations.type=1,"NO","YES")'
                ],
                'join' => [
                    'images' => [
                        'table' => 'm_images',
                        'type' => 'LEFT',
                        'alias' => 'Images',
                        'conditions' =>
                        [
                            'Locations.image_id = Images.id',
                        ],
                    ],
                    'routes' => [
                        'table' => 'm_routes',
                        'type' => 'LEFT',
                        'alias' => 'Routes',
                        'conditions' =>
                        [
                            'OR' => array(
                                array('Routes.id = Locations.route_id'),
                                array('Routes.id = Images.route_id'),
                            )
                        ],
                    ],
                    'devices' => [
                        'table' => 'm_devices',
                        'type' => 'LEFT',
                        'alias' => 'Devices',
                        'conditions' =>
                        [
                            'Devices.id = Routes.device_id',
                        ],
                    ],
                    'users' => [
                        'table' => 'm_users',
                        'type' => 'LEFT',
                        'alias' => 'Users',
                        'conditions' =>
                        [
                            'Users.id = Routes.user_id',
                        ],
                    ],
                ],
                'conditions' => [
                    'OR' => array(
                        array('Routes.id IN' => $dataID),
                        array('Images.route_id IN' => $dataID),
                    ),
                    'Users.web_id' => DEFINE_WEB_ID//check WEB_ID
                ],
                'group' => 'Locations.id',
                'order' => array('Routes.id', 'Locations.catch_time')
            ];
            $this->loadModel('Locations');
            $result = $this->Locations->find('all', $config)->all()->toArray();
            //pr($result);die;
            $this->createDowCsv($result);
        } else {
            $this->Flash->success(__('Please select survey'));
            $this->redirect(array('action' => 'datagps'));
        }
    }

    private function createDowCsv($result) {
        
        $arrayIndex = $this->_getArrayIndexRoutes();
        
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('SURVEY NO', 'TIME', 'USER', 'LAT', 'LON', 'DEVICE ID', 'PHOTO'));
        // output the column content
        $count = count($result);
        $checkCount = 0;
        foreach ($result as $key => $value) {
            $catchTimeSet = Time::parse($value['catch_time']);
            $catchTime = $catchTimeSet->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $routeId = $value['id_route'];
            if (isset($arrayIndex[$value['id_route']])) {
                $routeId = $arrayIndex[$value['id_route']];
            }
            fputcsv($output, array(
                $routeId,
                $catchTime,
                $value['Users']['username'],
                $value['latitude'],
                $value['longitude'],
                $value['Devices']['name'],
                $value['PHOTO'],
            ));
            // check row last route id
            $checkCount = $checkCount + 1;
            if ($count == $checkCount || (!empty($result[$key + 1]['id_route']) && $result[$key + 1]['id_route'] != $value['id_route'])) {
                $timeEndRouteSet = Time::parse($value['time_end_route']);
                $timeEndRoute = $timeEndRouteSet->i18nFormat('yyyy-MM-dd HH:mm:ss');
                fputcsv($output, array(
                    $routeId,
                    $timeEndRoute,
                    $value['Users']['username'],
                    $value['latitude'],
                    $value['longitude'],
                    $value['Devices']['name'],
                    "NO",
                ));
            }
            //end check row last data
        }
        fclose($output);
        die;
    }

    /**
     * Manage app language version
     * @return template
     */
    public function appLanguageVersion() {
        // get version
        $this->loadModel('Versions');
        $version = $this->Versions->findAppLanguageVersion();

        // get language
        $this->loadModel('AppLanguages');
        $arrayLanguages = $this->AppLanguages->findAllLanguages();
        $this->set(compact("version", "arrayLanguages"));
    }

    /**
     * Edit app language
     */
    public function editAppLanguage($id = null) {
        // get version
        $this->loadModel('AppLanguages');
        // check 
        if ($id == null) {
            return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
        }
        $config = [
            'fields' => [
                'AppLanguages.id',
                'AppLanguages.language',
                'AppLanguages.shortname',
                'AppLanguages.publish_flg',
            ],
            'conditions' => [
                'AppLanguages.id' => $id,
                'AppLanguages.del_flg' => 0,
            ],
        ];
        $result = $this->AppLanguages->find('all', $config)->first();
        if (!$result) {
            return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data();
            if (isset($data['del_flg']) && $data['del_flg'] == 1) {
                //START DELETE check id language default 
                if (in_array($result['id'], Configure::read('id_language_fix'))
                ) {
                    return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
                }
                //END DELETE check id language default
                $query = $this->AppLanguages->query();
                $query->update()
                        ->set(['del_flg' => true, 'language' => $data['language'] . "_DEL" . date('YmdHis')])
                        ->where(['id' => $data['id']])
                        ->execute();
                return $this->redirect(['action' => 'appLanguageVersion']);
            }
            //START EDIT check id language default 
            if (in_array($result['id'], Configure::read('id_language_fix')) && !empty($result['language']) && !empty($data['language']) && $result['language'] != $data['language']
            ) {
                return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
            }
            //END EDIT check id language default
            $this->AppLanguages->patchEntity($result, $this->request->data(), [
                'validate' => 'adminAdd'
            ]);
            if ($this->AppLanguages->save($result)) {

                // update version 
                $this->loadModel('Versions');
                $this->Versions->updateAppLanguageVersion();
                // end update version

                $this->Flash->success(__("The language has been saved"), ["key" => "editAppLanguage"]);
                $this->redirect(array('action' => 'appLanguageVersion'));
            }
        }
        $this->set(compact('result'));
    }

    /**
     * Register app language
     */
    public function registerAppLanguage() {
        $this->loadModel('AppLanguages');
        $result = $this->AppLanguages->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->AppLanguages->patchEntity($result, $this->request->data(), [
                'validate' => 'adminAdd'
            ]);
            if ($this->AppLanguages->save($result)) {
                // update version 
                $this->loadModel('Versions');
                $this->Versions->updateAppLanguageVersion(1);
                // end update version
                $this->Flash->success(__('The user has been saved'), ['key' => 'registerAppLanguage']);
                $this->redirect(array('controller' => 'users', 'action' => 'appLanguageVersion'));
            }
        }
        $this->set(compact('result'));
    }

    /**
     * Edit app language
     */
    public function editAppLanguageDetail($id = null) {
        // get version
        $this->loadModel('AppLanguages');
        $this->loadModel('AppLanguageDetails');
        // check 
        if ($id == null) {
            return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
        }
        $config = [
            'fields' => [
                'AppLanguages.id',
                'AppLanguages.language',
                'AppLanguages.shortname',
                'AppLanguages.publish_flg',
            ],
            'conditions' => [
                'AppLanguages.id' => $id,
                'AppLanguages.del_flg' => 0,
            ],
        ];
        $result = $this->AppLanguages->find('all', $config)->first();
        if (!$result) {
            return $this->redirect(['controller' => 'users', 'action' => 'appLanguageVersion']);
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $data = $this->request->data();
            // check have data index
            if (isset($data['data'])) {

                //convert data to save
                $dataConvert = array();
                foreach ($data['data'] as $key => $value) {
                    $datum = array(
                        'app_language_id' => $id,
                        'app_language_key_id' => $key,
                        'value' => $value['value'],
                    );
                    if (isset($value['id'])) {
                        $datum['id'] = $value['id'];
                    }
                    array_push($dataConvert, $datum);
                }

                $entities = $this->AppLanguageDetails->newEntities($dataConvert);

//                $this->AppLanguages->patchEntity($result, $this->request->data(), [
//                    'validate' => 'adminAdd'
//                ]);
                $check = true;
                foreach ($entities as $entity) {
                    if (!$this->AppLanguageDetails->save($entity)) {
                        $check = FALSE;
                    }
                }
                if ($check) {
                    // update version 
                    $this->loadModel('Versions');
                    $this->Versions->updateAppLanguageVersion();
                    // end update version

                    $this->Flash->success(__('The language has been saved'), ['key' => 'editAppLanguageDetail']);
                    $this->redirect(array('action' => 'appLanguageVersion'));
                }
            }
        }

        // get version
        $this->loadModel('Versions');
        $version = $this->Versions->findAppLanguageVersion();

        // get all key 
        $this->loadModel('AppLanguageKeys');
        $arrayKey = $this->AppLanguageKeys->findAllKeys();

        // get all language translate by key en        
        if ($id != 1) {
            $arrayEnglish = $this->AppLanguageDetails->findAllKeyAndValue(1);
            $this->set('arrayEnglish', $arrayEnglish);
        }

        // get all language translate by key
        $arrayValue = $this->AppLanguageDetails->findAllKeyAndValue($id);

        $this->set(compact('id', 'result', 'version', 'arrayKey', 'arrayValue'));
    }

    /**
     * Register Guide language
     */
    public function registerAppGuide($type = null, $id = null) {
        if (!empty($id) && !empty($type)) {
            $result = $this->Guide->newEntity();
            //get language
            $this->loadModel('AppLanguages');
            $language = $this->AppLanguages->find('all', array(
                        'fields' => 'AppLanguages.language',
                        'conditions' => ['AppLanguages.id' => $id],
                    ))->first();

            if ($this->request->is('post') || $this->request->is('put')) {
                $this->Guide->patchEntity($result, $this->request->data(), [
                    'validate' => 'Guide'
                ]);
                $image = $this->request->data();
                // check img error validate

                if (is_uploaded_file($image['image']["tmp_name"])) {
                    $main_file = $image['image']["tmp_name"];
//                    upload image tmp
                    $ext = strtolower(substr($image['image']["name"], strrpos($image['image']["name"], '.') + 1));
                    $dir = WWW_ROOT . "tmp";
                    $file_name = date("YmdHis") . mt_rand(1, 99) . "." . $ext;

                    if (file_exists($dir)) {
                        move_uploaded_file($image['image']["tmp_name"], $dir . DS . $file_name);
                        chmod($dir . DS . $file_name, 0755);
                        if (!isset($image['image_old'])) {
                            $image['image_old'] = $file_name;
                        }
                        $result['image'] = $file_name;
                    }
                }
                if (empty($result->errors())) {
//                // save data
                    if (!empty($result['image']) && !empty($main_file && !isset($image['img_old']))) {
                        $result['image'] = $this->uploadImageSave($result['image']);
                    } elseif (!empty($result['image'] || !empty($image['image_old']))) {
                        $result['image'] = $this->uploadImageSave($image['image_old']);
                    } elseif (empty($result['image']) && isset($result['image_old'])) {
                        $result['image'] = $this->uploadImageSave($image['image_old']);
                    }
                    if ($this->Guide->save($result)) {
                        $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $result->language_id));
                    }
                } else {
                    if (isset($image['image_old']) && !empty($image['image']['tmp_name'])) {
                        $imgError = $file_name;
                    } elseif (!isset($image['image_old'])) {
                        
                    } else {
                        $imgError = $image['image_old'];
                    }
                    $this->set(compact('result', 'id', 'language', 'imgError', 'type'));
                }
            }
            $this->set(compact('result', 'id', 'language', 'type'));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'listGuide'));
        }
    }

    /**
     * ListGuide Guide
     */
    public function listGuide($type = null, $id = null) {
        if (!empty($id) && $type) {
            $allGuide = $this->Guide->find('all', array(
                        'conditions' => ['Guide.language_id' => $id, 'del_flg' => 0, 'Guide.type' => $type]
                    ))->toArray();
            //get language
            $this->loadModel('AppLanguages');
            $language = $this->AppLanguages->find('all', array(
                        'fields' => 'AppLanguages.language',
                        'conditions' => ['AppLanguages.id' => $id],
                    ))->first();
            $this->set(compact('allGuide', 'id', 'language', 'type'));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'appLanguageVersion'));
        }
    }

    /**
     * Edit Guide
     */
    public function editGuide($type = null, $id = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $result = $this->request->data;
            // find image old
            $imgOld = $this->Guide->find('all', array(
                        'fields' => ['Guide.image', 'Guide.language_id'],
                        'conditions' => ['Guide.id' => $result['id']]
                    ))->first();
            // check image exist
            if (!empty($result['image']["tmp_name"])) {
                $main_file = $result['image']["tmp_name"];
                if (is_uploaded_file($result['image']["tmp_name"])) {
                    $ext = strtolower(substr($result['image']["name"], strrpos($result['image']["name"], '.') + 1));
                    $dir = WWW_ROOT . "files" . DS . "slide-app";
                    $file_name = date("YmdHis") . mt_rand(1, 99) . "." . $ext;

                    if (file_exists($dir)) {
                        move_uploaded_file($result['image']["tmp_name"], $dir . DS . $file_name);
                        chmod($dir . DS . $file_name, 0755);
                        $result['image'] = $file_name;
                    }
                }
            }
            // save data edit
            $resultSave = $this->Guide->newEntity();
            if (!empty($result['image']) && !empty($main_file)) {
                $resultSave['image'] = $result['image'];
            }
            $resultSave['id'] = $id;
            $resultSave['guide_text'] = $result['guide_text'];
            if ($this->Guide->save($resultSave)) {
                $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $imgOld->language_id));
                // delete image old
                if (file_exists(WWW_ROOT . "files" . DS . 'slide-app' . DS . $imgOld->image) && !empty($imgOld->image) && $resultSave->image) {
                    unlink(WWW_ROOT . "files" . DS . 'slide-app' . DS . $imgOld->image);
                }
            }
        }
        if (!empty($id)) {
            $editGuide = $this->Guide->find('all', array(
                        'conditions' => ['Guide.id' => $id]
                    ))->first();
            $this->set(compact('id', 'editGuide', 'type'));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'listGuide'));
        }
    }

    /**
     * Delete Guide
     */
    public function deleteGuide($type = null, $id = null) {
        if (!empty($id) && !empty($type)) {
            $allGuide = $this->Guide->find('all', array(
                        'conditions' => ['Guide.id' => $id, 'Guide.type' => $type]
                    ))->first();
            if (isset($allGuide)) {
                $SaveGuide = $this->Guide->newEntity();
                $SaveGuide->del_flg = 1;
                $SaveGuide->id = $id;
                $this->Guide->save($SaveGuide);

                $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $allGuide->language_id));
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $allGuide->language_id));
        }
    }

    /**
     * Delete image guide
     */
    public function deleteImageGuide($type = null, $id = null) {
        if (!empty($id) && !empty($type)) {
            $allGuide = $this->Guide->find('all', array(
                        'conditions' => ['Guide.id' => $id, 'Guide.type' => $type]
                    ))->first();
            if (isset($allGuide) && !empty($allGuide['image'])) {
                if (file_exists(WWW_ROOT . "files" . DS . "slide-app" . DS . $allGuide['image'])) {
                    $SaveImageGuide = $this->Guide->newEntity();
                    $SaveImageGuide->image = null;
                    $SaveImageGuide->id = $id;
                    $this->Guide->save($SaveImageGuide);
                    if (file_exists(WWW_ROOT . "files" . DS . "slide-app" . DS . $allGuide['image'])) {
                        unlink(WWW_ROOT . "files" . DS . "slide-app" . DS . $allGuide['image']);
                    }
                }
            }
            $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $allGuide->language_id));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'listGuide', $type, $allGuide->language_id));
        }
    }

    /**
     * Upload image Guide Save
     */
    public function uploadImageSave($file_image) {
        if (!empty($file_image)) {
            $upload_folder = WWW_ROOT . "files" . DS . "slide-app";
            $upload_folder_specific = $upload_folder . DS;
            if (!is_dir($upload_folder)) {
                mkdir($upload_folder, 0755, true);
            }
            if (!is_dir($upload_folder_specific)) {
                mkdir($upload_folder_specific, 0755, true);
            }
            $tmp_file = WWW_ROOT . "tmp" . DS . $file_image;
            if (file_exists($tmp_file)) {
                $image = new File($tmp_file, true, 0755);
                $file_name = md5(time() + rand(0, 1000));
                $ext = $image->ext();
                $mime_type = $image->mime();
                $size = $image->size();
                $aft_file = $upload_folder_specific . DS . $file_name . "." . $ext;
                $image->copy($aft_file);
                array_map('unlink', glob(WWW_ROOT . "tmp" . DS . '*'));
            }
        }
        return $file_name . "." . $ext;
    }

    //START VERSION
    /**
     * register version
     */
    public function registerVersion() {
        $this->loadModel('Versions');
        $result = $this->Versions->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Versions->patchEntity($result, $this->request->data(), [
                'validate' => 'adminAdd'
            ]);
            if ($this->Versions->save($result)) {
                $this->redirect(array('controller' => 'users', 'action' => 'version'));
            }
        }
        $this->set(compact('result'));
    }

    /**
     * edit version
     */
    public function editVersion($id = null) {
        $Versions = TableRegistry::get('Versions');
        $result = $Versions->find('all')
                ->where(array('id' => $id, 'del_flg' => 0))
                ->first();
        if (!empty($result)) {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->loadModel('Versions');
                $this->Versions->patchEntity($result, $this->request->data(), [
                    'validate' => 'adminAdd'
                ]);
                if ($this->Versions->save($result)) {
                    $this->redirect(array('controller' => 'users', 'action' => 'version'));
                }
            }
            $this->set(compact('result'));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'version'));
        }
    }

    /**
     * delete version
     */
    public function deleteVersion($id = null) {
        $Versions = TableRegistry::get('Versions');
        $result = $Versions->find('all')
                ->where(array('id' => $id))
                ->first();
        if (!empty($result)) {
            $this->loadModel('Versions');
            $result['del_flg'] = 1;
            $result['version'] = $result['version'] . "_DEL" . date('YmdHis');
            $this->Versions->save($result);
            $this->Flash->success(__('Delete success'));
            $this->redirect(array('controller' => 'users', 'action' => 'version'));
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'version'));
        }
    }

    //END VERSION

    /**
     * translateLanguage 
     */
    public function translateLanguage($id = null) {
        $Language = TableRegistry::get('AppLanguages');
        $language = $Language->find('all')
                ->where(array('id' => $id))
                ->first();
        if (!empty($language)) {
            $translateTable = TableRegistry::get('AppTranslateLanguages');
            if ($this->request->is('post') || $this->request->is('put')) {
                $dataForm = $this->request->data;
                $this->loadModel('AppTranslateLanguages');
                $this->AppTranslateLanguages->saveDataTranslate($id, $dataForm);
                $this->Flash->success(__('Update success'));
                $this->redirect(array('controller' => 'users', 'action' => 'translateLanguage', $id));
            }
            $languageAll = $Language->find('all')
                            ->select(['id', 'language'])
                            ->where(array('del_flg' => 0))
                            ->order(array('id' => 'ASC'))
                            ->all()->toArray();
            //pr($languageAll);die;
            $translate = $translateTable->find('all')
                            ->select(['source_language_id', 'need_language_id', 'translate'])
                            ->where(array('source_language_id' => $id))
                            ->all()->toArray();
            //pr($translate);die;
            $this->set(['language' => $language, 'languageAll' => $languageAll, 'translate' => $translate]);
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'appLanguageVersion'));
        }
    }
    
    
    /**
     * editgps
     * @param type $id
     */
    public function editgps($id) {
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        //pr($arrayIndex);die;
        $name = '';
        if (isset($arrayIndex[$id])) {
            $route = $this->Routes->checkRoute($arrayIndex[$id]);
            if (empty($route)) {
                $this->redirect($this->referer());
            }
            $this->loadModel('Locations');
//            if ($this->request->is('post') || $this->request->is('put')) {
//                $data = $this->request->data();
//                pr($data);die();
//            }

            $data = $this->Locations->getDataByRouteId($route['id']);
            $this->set('data', $data);
            $this->set('id', $id);
            
        } else {
            $this->redirect($this->referer());
        }
        $this->set('name', $name);
    }
    
    /**
     * Edit lat/long gps by ajax
     * @param type $id
     */
    public function editLatLongGPS($id) {
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        $r = array('code' => 0, 'message' => 'error');
        if (isset($arrayIndex[$id])) {
            $route = $this->Routes->checkRoute($arrayIndex[$id]);
            if (!empty($route)) {
               $this->loadModel('Locations');
                if ($this->request->is('post') || $this->request->is('put')) {
                    $dataAjax = $this->request->data();
                    if($dataAjax['dataSave'] == "latitude"){
                        $val = $this->validateLatitude($dataAjax['valSave']);
                    }else{
                        $val = $this->validateLongitude($dataAjax['valSave']);
                    }
                    if($val==1){
                        if ($this->Locations->editLocation($dataAjax)) {
                            $r = array('code' => 1, 'message' => 'success');
                        }
                    }else{
                        $r = array('code' => 2, 'message' => __('The format is not correct'));
                    }
                }
            }
        } 
        echo json_encode($r);die;
    }
    
    /**
     * Edit show/hide gps by ajax
     * @param type $id
     */
    public function editShowHideGPS($id) {
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        $r = array('code' => 0, 'message' => 'error');
        if (isset($arrayIndex[$id])) {
            $route = $this->Routes->checkRoute($arrayIndex[$id]);
            if (empty($route)) {
                echo json_encode($r);
                die;
            }
            $this->loadModel('Locations');
            if ($this->request->is('post') || $this->request->is('put')) {
                $dataAjax = $this->request->data();
                if (isset($dataAjax['valSave']) && isset($dataAjax['dataId'])) {
                    $location = $this->Locations->get($dataAjax['dataId']);
                    $location->delete_flg = $dataAjax['valSave'] == 0?1:0;
                    if ($this->Locations->save($location)) {
                        $r = array('code' => 1, 'message' => 'success');
                    }
                }
                echo json_encode($r);
                die;
            }

        } else {
            echo json_encode($r);
            die;
        }
    }
    
    /**
     * delete route
     * @param type $id
     */
    public function deleteRoute() {
        $r = array('code' => 0, 'message' => 'error');
        if ($this->request->is('post') || $this->request->is('put')) {
            $dataAjax = $this->request->data();
            if (!empty($dataAjax['dataId'])) {
                $route = $this->Routes->checkRoute($dataAjax['dataId']);
                if (!empty($route)) {
                    if ($this->Routes->updateDelFlg($dataAjax['dataId'],$dataAjax['check'])) {
                        $r = array('code' => 1, 'message' => 'success');
                    }
                }
            }
        }
        echo json_encode($r);die;
    }
    
    /**
     * hidden route
     * @param type $id
     */
    public function hidden($id) {
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        if(isset($arrayIndex[$id])){
            $route = $this->Routes->checkRoute($arrayIndex[$id]);
            if(!empty($route)){
                $data = $this->request->data();
                $this->Routes->hiddenRoute($route['id'],$data['hidden_flg']);
            }
        }
        $this->redirect($this->referer());
    }
    /**
     * Validates a given latitude $lat
     *
     * @param float|int|string $lat Latitude
     * @return bool `true` if $lat is valid, `false` if not
     */
    function validateLatitude($lat) {
      return preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,8})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,8})?))$/', $lat);
    }
    /**
     * Validates a given longitude $long
     *
     * @param float|int|string $long Longitude
     * @return bool `true` if $long is valid, `false` if not
     */
    function validateLongitude($long) {
      return preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,8})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,8})?))$/', $long);
    }
}
