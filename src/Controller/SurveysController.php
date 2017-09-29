<?php

namespace App\Controller;

require_once(ROOT . DS . 'vendor' . DS . "phpexcel" . DS . "BriteExcel.php");

use App\Controller\UserBaseController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Network\Response;
use Cake\Network\Request;
use BriteExcel;
use Cake\Core\Configure;

class SurveysController extends UserBaseController {

    public static $limit = 10;

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->viewBuilder()->layout('users');
        $this->loadModel('Images');
        $this->loadModel('Users');
        $this->loadModel('Routes');
        $this->loadModel('Locations');
        $this->loadModel('Versions');

        // get vesion
        $version = $this->Versions->findWebLanguageVersion();
        $this->set(array('versionWeb' => $version));
    }

    public function index($id = NULL) {
        // start check filter
        $filter = "";
        $type_filter = "";
        $orderBy = array('Routes.time_start' => 'DESC', 'Routes.id' => 'DESC');
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

        $this->set(['filter' => $filter, 'type_filter' => $type_filter]);
        // end filter 
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        $routesTable = TableRegistry::get('Routes');
        $locationTable = TableRegistry::get('Locations');                
        
        $offset = -1;
        if ($id != null && !isset($arrayIndex[$id])) {
            return $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
        }
        else if (isset($arrayIndex[$id])){
            $this->set('id', $id);
            $id = $arrayIndex[$id];
        }
        if ($id) {
            $currentRoute = $this->Routes->get($id);
            if ($currentRoute) {
                $time_start = $currentRoute->time_start;
                $time_start = $time_start->format('Y-m-d H:i:s');            
                $routes = $this->Routes->find('all')
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
                        ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.number', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Devices.name'])
                        ->distinct('Routes.id')
                        ->where([
                            'Routes.delete_flg' => 0,
                            'Routes.time_start >=' => $time_start, 
                            'Locations.latitude IS NOT' => null,
                            'Users.web_id' => DEFINE_WEB_ID,
                            'Users.del_flg' => 0
                        ])
                        ->order($orderBy)
                //                ->limit(self::$limit)
                //                ->offset(0)
                ;
                $offset = $routes->count() - self::$limit;
            }
        }
        // check count < limit for repeat query
        if ($offset < 0) {
            $routes = $this->Routes->find('all')
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
                    ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.number', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Devices.name'])
                    ->distinct('Routes.id')
                    ->where([
                        'Routes.delete_flg' => 0,
                        'Locations.id IS NOT' => null,
                        'Users.web_id' => DEFINE_WEB_ID
                        , 'Users.del_flg' => 0
                    ])
                    ->order($orderBy)
                    ->limit(self::$limit)
                    ->offset(0)
            ;
            $offset = 0;
        }
        
        $this->set('offset', $offset);
        $this->set('offset', $offset);
        $arrayIndexReverse = array_flip($arrayIndex);
        $this->set(['routes' => $routes, 'arrayIndexReverse' => $arrayIndexReverse]);
    }

    public function view($id) {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }

    public function viewRoute($id = NULL) {
        // start check filter
        $filter = "";
        $type_filter = "";
        $orderBy = array('Routes.time_start' => 'DESC', 'Routes.id' => 'DESC');
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
        $this->set(['filter' => $filter, 'type_filter' => $type_filter]);
        // end filter               

        // count routes
        $query = $this->Routes->find('all')
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
                ->where([
                    'Routes.delete_flg' => 0,
                    'Users.web_id' => DEFINE_WEB_ID, 
                    'Locations.latitude IS NOT' => null,
                    'Users.del_flg' => 0
                ]);

        $number = $query->count();        
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        if ($id != null && !isset($arrayIndex[$id])) {
            $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
        }
        else if (isset($arrayIndex[$id])){
            $this->set('id', $id);
            $id = $arrayIndex[$id];
        }
        $offset = -1;
        $currentRoute = $this->Routes->get($id);
        if ($currentRoute) {        
            $time_start = $currentRoute->time_start;
            $time_start = $time_start->format('Y-m-d H:i:s');        
            $routes = $this->Routes->find('all')
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
                    ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.number', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Routes.last_address', 'Devices.name'])
                    ->distinct('Routes.id')
                    ->where([
                        'Routes.delete_flg' => 0,
                        'Routes.id >=' => $id, 
                        'Locations.latitude IS NOT' => null,
                        'Users.web_id' => DEFINE_WEB_ID,
                        'Users.del_flg' => 0
                    ])
                    ->order($orderBy)
    //                ->limit(self::$limit)
    //                ->offset(0)
            ;
            $offset = $routes->count() - self::$limit;
        }
        // check count < limit for repeat query
        if ($offset < 0) {
            $routes = $this->Routes->find('all')
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
                    ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.number', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Routes.last_address', 'Devices.name'])
                    ->distinct('Routes.id')
                    ->where([
                        'Routes.delete_flg' => 0,
                        'Locations.latitude IS NOT' => null,
                        'Users.web_id' => DEFINE_WEB_ID,
                        'Users.del_flg' => 0
                    ])
                    ->order($orderBy)
                    ->limit(self::$limit)
                    ->offset(0)
            ;
            $offset = 0;
        }
        $this->set('number', $number);
        $this->set('offset', $offset);
        $this->set(['users' => $this->Users->find('all'), 'routes' => $routes]);
        if ($id) {
            $route = $this->Routes->find('all')
                    ->hydrate(false)
                    ->join([
                        [
                            'table' => 'm_devices',
                            'type' => 'INNER',
                            'alias' => 'Devices',
                            'conditions' =>
                            [
                                'Routes.device_id = Devices.id',
                            ],
                        ],
                        'Locations' => [
                            'table' => 'm_locations',
                            'type' => 'LEFT OUTER',
                            'alias' => 'Locations',
                            'conditions' =>
                            [
                                'Locations.route_id = Routes.id',
                                'Locations.id = (SELECT MAX(id) FROM m_locations WHERE route_id = Routes.id)'
                            ],
                        ],
                        'LocationsMin' => [
                            'table' => 'm_locations',
                            'type' => 'LEFT OUTER',
                            'alias' => 'LocationsMin',
                            'conditions' =>
                            [
                                'LocationsMin.route_id = Routes.id',
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
                    ->where([
                        'Routes.delete_flg' => 0,
                        'Routes.id' => $id, 
                        'Locations.latitude IS NOT' => null,
                        'Users.web_id' => DEFINE_WEB_ID,
                        'Users.del_flg' => 0
                    ])
                    ->select(['Users.username', 'Users.del_flg', 'LocationsMin.id', 'LocationsMin.longitude', 'LocationsMin.latitude', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.country', 'Routes.last_address', 'Devices.name'])
                    ->distinct('Routes.id')
                    ->first();
//            var_dump($route);die;
            if (!empty($route)) {
                $images = $this->Images->find('all')
                        ->hydrate(false)
                        ->join([
                            'locations' => [
                                'table' => 'm_locations',
                                'type' => 'LEFT',
                                'alias' => 'Locations',
                                'conditions' =>
                                [
                                    'Locations.image_id = Images.id',
                                ],
                            ],
                        ])
                        ->where([
                            'Images.route_id' => $id,
                            'Locations.type' => 2, 
                            'Locations.latitude IS NOT' => null,
                        ])
                        ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Images.id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height', 'Images.size', 'Images.created', 'Locations.catch_time', 'Images.comment'])
                        ->order(['Locations.id' => 'ASC']);
                $cnt = 0;
                foreach ($images as $key => $value) {
                    if ($value) {
                        $cnt++;
                    }
                }
                $route['cnt'] = $cnt;
                //pr($route);die;
                $arrayIndexReverse = array_flip($arrayIndex);
                $this->set('arrayIndexReverse',$arrayIndexReverse);
                $this->set(['route_rd' => $route, 'images' => $images]);
            } else {
                $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
            }
        } else {
            $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
        }
    }
   
    public function getDataAjax() {
        $routesTable = TableRegistry::get('Routes');
        $locationTable = TableRegistry::get('Locations');
        $imageTable = TableRegistry::get('Images');

        $results = array('status' => TRUE, 'dataLocal' => array(), 'dataImg' => array());
        $arrayIndex = $this->_getArrayIndexRoutes(1);
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['id']) && isset($arrayIndex[$data['id']])) {
                $data['id'] = $arrayIndex[$data['id']];
                $results['dataLocal'] = array();
                $results['dataImg'] = array();
                $results['all'] = array();
//                $results['dataLocal'] = $locationTable->find('all')
//                                ->where([
//                                    'Locations.route_id' => $data['id'],
////                                    'Locations.type' => 1
//                                        ]
//                                )
//                                ->select(['Locations.route_id', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time'])
//                                ->order(['Locations.id' => 'DESC'])->toArray();
//
//                $results['dataImg'] = $imageTable->find('all')
//                                ->join([
//                                    'locations' => [
//                                        'table' => 'm_locations',
//                                        'type' => 'INNER',
//                                        'alias' => 'Locations',
//                                        'conditions' =>
//                                        [
//                                            'Locations.image_id = Images.id',
//                                        ],
//                                    ],
//                                ])
//                                ->where([ 'Images.route_id' => $data['id']])
//                                ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time', 'Images.id', 'Images.route_id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height'])
//                                ->order(['Locations.id' => 'ASC'])->toArray();

                $config = [
                    'fields' => [
//'id_location' =>'Locations.id',
                        'id_route1' => 'Locations.route_id',
                        'id_route2' => 'Images.route_id',
                        'Locations.id', 'Locations.type',
                        'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time',
                        'Images.id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height'
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
                    ],
                    'conditions' => [
                        'OR' => array(
                            array('Locations.route_id' => $data['id'],'Locations.delete_flg' => 0),
                            array('Images.route_id' => $data['id']),
                        )
                    ],
                    'group' => 'Locations.id',
                    'order' => 'Locations.catch_time'
                ];
                $this->loadModel('Locations');
                $dataQuery = $this->Locations->find('all', $config)->all()->toArray();
                foreach ($dataQuery as $row) {
// route            
                    // add temp time
                    if(empty($row['catch_time'])){
                        $row['catch_time'] = date('Y-m-d H:i:s');
                    }
                    if ($row['type'] == 1) {
                        $datum = array();
                        $datum['route_id'] = $row['id_route1'];
                        $datum['id'] = $row['id'];
                        $datum['longitude'] = $row['longitude'];
                        $datum['latitude'] = $row['latitude'];
                        $datum['catch_time'] = $row['catch_time'];
                        array_push($results['dataLocal'], $datum);
                    } else if ($row['type'] == 2) {
                        $datum = array();
                        $datum['Locations']['id'] = $row['id'];
                        $datum['Locations']['longitude'] = $row['longitude'];
                        $datum['Locations']['latitude'] = $row['latitude'];
                        $datum['Locations']['catch_time'] = $row['catch_time'];
                        $datum['id'] = $row['Images']['id'];
                        $datum['route_id'] = $row['id_route2'];
                        $datum['rotation'] = $row['Images']['rotation'];
                        $datum['name'] = $row['Images']['name'];
                        $datum['width'] = $row['Images']['width'];
                        $datum['height'] = $row['Images']['height'];
                        array_push($results['dataImg'], $datum);
                    }
// get all longtide, latitude
                    array_push($results['all'], array('longitude' => $row['longitude'], 'latitude' => $row['latitude']));
                }

                return new Response(array('body' => json_encode($results)));
            } else {
                $routes = $routesTable->find('all')
                        ->hydrate(false)
                        ->join([
                            'locations' => [
                                'table' => 'm_locations',
                                'type' => 'LEFT OUTER',
                                'alias' => 'Locations',
                                'conditions' =>
                                [
                                    'Locations.route_id = Routes.id',
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
                        ->where(['Routes.delete_flg' => 0,'Locations.latitude IS NOT' => null, 'Users.web_id' => DEFINE_WEB_ID, 'Users.del_flg' => 0])
                        ->select(['Routes.id', 'Routes.time_start', 'Routes.time_end'])
                        ->order(['Routes.time_start' => 'DESC'])
                        ->first();
                if (!empty($routes['id'])) {
                    $results['dataLocal'] = array();
                    $results['dataImg'] = array();
                    $results['all'] = array();
//                    $results['dataLocal'] = $this->Locations->find('all', [
//                                'conditions' => [
//                                    'Locations.route_id' => $routes['id'],
////                                    'Locations.type' => 1
//                                ],
//                                'fields' => ['Locations.route_id', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time'],
//                                'order' => ['Locations.id' => 'DESC']
//                            ])->toArray();
//                    $results['dataImg'] = $imageTable->find('all')
//                                    ->hydrate(false)
//                                    ->join([
//                                        'locations' => [
//                                            'table' => 'm_locations',
//                                            'type' => 'INNER',
//                                            'alias' => 'Locations',
//                                            'conditions' =>
//                                            [
//                                                'Locations.image_id = Images.id',
//                                            ],
//                                        ],
//                                    ])
//                                    ->where([ 'Images.route_id' => $routes['id']])
//                                    ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time', 'Images.id', 'Images.route_id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height'])
//                                    ->order(['Locations.id' => 'ASC'])->toArray();

                    $config = [
                        'fields' => [
//'id_location' =>'Locations.id',
                            'id_route1' => 'Locations.route_id',
                            'id_route2' => 'Images.route_id',
                            'Locations.id', 'Locations.type',
                            'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Locations.catch_time', 'Locations.catch_time',
                            'Images.id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height'
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
                        ],
                        'conditions' => [
                            'OR' => array(
                                array('Locations.route_id' => $routes['id'],'Locations.delete_flg' => 0),
                                array('Images.route_id' => $routes['id']),
                            )
                        ],
                        'group' => 'Locations.id',
                        'order' => 'Locations.catch_time'
                    ];
                    $this->loadModel('Locations');
                    $dataQuery = $this->Locations->find('all', $config)->all()->toArray();
                    foreach ($dataQuery as $row) {
// route
                        if ($row['type'] == 1) {
                            $datum = array();
                            $datum['route_id'] = $row['id_route1'];
                            $datum['id'] = $row['id'];
                            $datum['longitude'] = $row['longitude'];
                            $datum['latitude'] = $row['latitude'];
                            $datum['catch_time'] = $row['catch_time'];
                            array_push($results['dataLocal'], $datum);
                        } else if ($row['type'] == 2) {
                            $datum = array();
                            $datum['Locations']['id'] = $row['id'];
                            $datum['Locations']['longitude'] = $row['longitude'];
                            $datum['Locations']['latitude'] = $row['latitude'];
                            $datum['Locations']['catch_time'] = $row['catch_time'];
                            $datum['id'] = $row['Images']['id'];
                            $datum['route_id'] = $row['id_route2'];
                            $datum['rotation'] = $row['Images']['rotation'];
                            $datum['name'] = $row['Images']['name'];
                            $datum['width'] = $row['Images']['width'];
                            $datum['height'] = $row['Images']['height'];
                            array_push($results['dataImg'], $datum);
                        }
// get all longtide, latitude
                        array_push($results['all'], array('longitude' => $row['longitude'], 'latitude' => $row['latitude']));
                    }

                    return new Response(array('body' => json_encode($results)));
                }
            }
            $results['status'] = false;
            return new Response(array('body' => json_encode($results)));
        } else {
            $results['status'] = false;
            return new Response(array('body' => json_encode($results)));
        }
    }

    public function loadMore() {
        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (!empty($data['offset'])) {
// start check filter
                $orderBy = array('Routes.time_start' => 'DESC', 'Routes.id' => 'DESC');
                if (!empty($data['filter']) && !empty($data['type_filter'])) {
                    if ($data['type_filter'] != "desc" && $data['type_filter'] != "asc") {
                        $data['type_filter'] = "desc";
                    }
                    if ($data['filter'] == "user") {
                        $orderBy = array('Users.username' => $data['type_filter'], 'Routes.time_start' => 'DESC');
                    } elseif ($data['filter'] == "id") {
                        $orderBy = array('Routes.id' => $data['type_filter']);
                    } elseif ($data['filter'] == "date") {
                        $orderBy = array('Routes.time_start' => $data['type_filter']);
                    }
                }

                // count routes
                $query = $this->Routes->find('all')
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
                        ->where([
                            'Routes.delete_flg' => 0,
                            'Locations.id IS NOT' => null,
                            'Users.web_id' => DEFINE_WEB_ID,
                            'Users.del_flg' => 0
                        ])
                        ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.number', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Devices.name'])
                        ->order($orderBy)
                        ->distinct('Routes.id');//check WEB_ID

                $arrayIndex = $this->_getArrayIndexRoutes();                              
// end filter 
                $routes = $query
                        ->limit(self::$limit)
                        ->offset($data['offset']);
                if ($routes->count() > $data['offset']) {                    
                    $html = "";
                    foreach ($routes as $key => $route) {  

                        if (isset($arrayIndex[$route['id']])) {
                            $route_id = $arrayIndex[$route['id']];
                        }
                        else {
                            $route_id = $route['id'];
                        }                         
                        
                        $html .= '<div class="item mb10 ov" id="' . $arrayIndex[$route['id']] . '">';
                        $html .= '<div class="col-lg-8 showMap" data-bind="' . $arrayIndex[$route['id']] . '">';
                        if ($route['Users']['del_flg'] == 1) {
                            $html .= '<span>' . __('DELETED USER:') . ' </span>' . $route['Users']['username'] . '<br>';
                        } else {
                            $html .= '<span>' . __('USER:') . ' </span>' . $route['Users']['username'] . '<br>';
                        }
                        $html .= '<span>' . __('Device ID:') . '  </span>' . $route['Devices']['name'] . '<br>';
//$html .= '<span>最終調査箇所: </span>' . $route['Locations']['longitude'] . '/' . $route['Locations']['latitude'] . '<br>';
                        $html .= '<span>' . __('SURVEY ID:') . '  </span>' . $route_id . '<br>';
                        $html .= '<span>' . __('DATE:') . ' </span>' . date('Y/m/d', strtotime($route['time_start'])) . '<br>';
                        $html .= '<span>' . __('LOCATION:') . ' </span>' . $route['country'] . '<br>';
                        $html .= '</div>';
                        $html .= '<div class="col-lg-4 showData">';
                        $html .= '<a rel="' . Router::url(['controller' => 'surveys', 'action' => 'viewRoute', $arrayIndex[$route['id']]]) . '" class="target targetOnly">';
                        $html .= $this->_getImageFirst($route['id']);
                        $html .= '</a>';
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    echo json_encode(array('status' => true, 'html' => $html));
                    die;
                }
            }
        }
        echo json_encode(array('status' => false));
        die;
    }

    /**
     * get first image in route
     * @param type $id
     * @return string
     */
    public function _getImageFirst($id = null) {
        $imageTable = TableRegistry::get('Images');
        $url = '';
        if ($id) {
            $image = $imageTable->find('all')
                    ->where(['Images.route_id' => $id])
                    ->order(['Images.created' => 'ASC'])
                    ->first();
            if (!empty($image['name'])) {
                $url = "<img src='" . $this->request->webroot . "files/image/" . $id . "/" . $image['name'] . "'  class='imgFirst' data-width='" . $image['width'] . "' data-height='" . $image['height'] . "'/> ";
            } else {
                $url = "<img src='" . $this->request->webroot . "img/default.png' class='imgFirst' data-width='225' data-height='225'/>";
            }
        } else {
            $url = "<img src='" . $this->request->webroot . "img/default.png' class='imgFirst' data-width='225' data-height='225'/>";
        }

        return $url;
    }

    /**
     * get count surveys in Device
     * @param type $dev_id
     * @return type
     */
    public function _getCountDevice($dev_id = NULL) {
        $routesTable = TableRegistry::get('Routes');
        $cnt = 0;
        $cnt = $routesTable->find('all')
                ->hydrate(false)
                ->where(['device_id' => $dev_id])
                ->order(['Routes.id' => 'DESC'])
                ->count();
        return $cnt;
    }

    public function createExcel($id) {
        $id_array = $this->request->query['id_array'];
        if ($id) {
            $route = $this->Routes->find('all')
                    ->hydrate(false)
                    ->join([
                        [
                            'table' => 'm_devices',
                            'type' => 'INNER',
                            'alias' => 'Devices',
                            'conditions' =>
                            [
                                'Routes.device_id = Devices.id',
                            ],
                        ],
                        'Locations' => [
                            'table' => 'm_locations',
                            'type' => 'LEFT OUTER',
                            'alias' => 'Locations',
                            'conditions' =>
                            [
                                'Locations.route_id = Routes.id',
                                'Locations.id = (SELECT MAX(id) FROM m_locations WHERE route_id = Routes.id)'
                            ],
                        ],
                        'LocationsMin' => [
                            'table' => 'm_locations',
                            'type' => 'LEFT OUTER',
                            'alias' => 'LocationsMin',
                            'conditions' =>
                            [
                                'LocationsMin.route_id = Routes.id',
                            ],
                        ],
                         'user' => [//check WEB_ID
                            'table' => 'm_users',
                            'type' => 'LEFT',
                            'alias' => 'Users',
                            'conditions' =>
                            [
                                'Users.id = Routes.user_id',
                            ],
                        ]//check WEB_ID
                    ])
                    ->where([
                        'Routes.delete_flg' => 0,
                        'Routes.id' => $id,
                        'Users.web_id' => DEFINE_WEB_ID,
                        'Users.del_flg' => 0
                    ])
                    ->select(['LocationsMin.id', 'LocationsMin.longitude', 'LocationsMin.latitude', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.country', 'Devices.name'])
                    ->distinct('Routes.id')
                    ->first();
            if (!empty($route)) {
                $conditions = ['Images.route_id' => $id, 'Locations.type' => 2, 'Locations.latitude IS NOT' => null];
                if (!empty($id_array)) {
                    $conditions['Images.id in'] = $id_array;
                }
                $images = $this->Images->find('all')
                        ->hydrate(false)
                        ->join([
                            'locations' => [
                                'table' => 'm_locations',
                                'type' => 'LEFT',
                                'alias' => 'Locations',
                                'conditions' =>
                                [
                                    'Locations.image_id = Images.id',
                                ],
                            ],
                        ])
                        ->where($conditions)
                        ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height', 'Images.size', 'Images.created', 'Locations.catch_time', 'Images.comment'])
                        ->order(['Locations.id' => 'ASC']);
                $cnt = 0;
                foreach ($images as $key => $value) {
                    if ($value) {
                        $cnt++;
                    }
                }
                $route['cnt'] = $cnt;

                // download
                $dateTimeTitle = new \DateTime($route['time_start']);
                $dateTitle = $dateTimeTitle->format('Y/m/d');
                //$SURVEY = $route['id'];
                $survey = "SURVEY";
                $titleExcel = $dateTitle . "_" . $route['country'] . "_" . $survey;

                //$this->set(['route_rd' => $route, 'images' => $images]);
                $briteExcel = new BriteExcel(Configure::read('info'), $route, $images->toArray(), $titleExcel);
                $briteExcel->createExcelFile();
            }
        }
    }

}
