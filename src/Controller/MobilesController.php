<?php

namespace App\Controller;

use App\Controller\UserBaseController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Network\Response;
use Cake\Network\Request;
use Cake\Core\Configure;
use Cake\I18n\I18n;

class MobilesController extends AppController {

    public static $limit = 10;

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->viewBuilder()->layout('mobile');
        $this->loadModel('Images');
        $this->loadModel('Users');
        $this->loadModel('Routes');
        $this->loadModel('Locations');
        $this->loadModel('Tokens');
        $this->loadModel('Versions');
        $this->loadModel('AppLanguages');

        // get vesion
        $version = $this->Versions->findWebLanguageVersion();
        $this->set(array('versionWeb' => $version));

        $arrayIndex = $this->_getArrayIndexRoutes();
        $this->set('arrayIndex', $arrayIndex);
    }

    public function index() {
        // get token
        $token = $this->request->query('token');
        $language = $this->request->query('language');
        // check token exist
        if (!empty($token)) {
            // check token exist at table m_token_user_devices
            // get user_id if exist token
            $tokenOld = $this->Tokens->find('all', array(
                        'fields' => ['Tokens.token', 'Tokens.user_id'],
                        'conditions' => [
                            'Tokens.token' => $token,
                        ],
                    ))->first();

            if ($language != NULL) {
                // get language
                $shortName = $this->AppLanguages->findShortNameByName($language);
                // check shortname
                $folder = __DIR__ . '/../Locale/' . $shortName;
                if (!is_dir($folder)) {
                    $shortName = 'en_US';
                }
            } else {
                $shortName = 'en_US';
            }
            $this->Cookie->write('Config.language', $shortName);
            I18n::locale($shortName);

            if (!empty($tokenOld)) {
                $user_id = $tokenOld['user_id'];
                $routesTable = TableRegistry::get('Routes');
                $locationTable = TableRegistry::get('Locations');

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
                            ],
                            'tokens' => [
                                'table' => 'm_token_user_devices',
                                'type' => 'LEFT',
                                'alias' => 'Tokens',
                                'conditions' =>
                                [
                                    'Users.id = Tokens.user_id',
                                ],
                            ],
                        ])
                        ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.number', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Devices.name', 'Tokens.token'])
                        ->distinct('Routes.id')
                        ->where([
                            'Routes.delete_flg' => 0,
                            'Routes.user_id' => $user_id,
                            'Locations.latitude IS NOT' => null,
                            'Users.web_id' => DEFINE_WEB_ID,
                            'Users.del_flg' => 0
                        ])
                        ->order(['Routes.time_start' => 'DESC', 'Routes.id' => 'DESC']);

                // get data route follow $user_id
                $routes = $query
                        ->limit(self::$limit)
                        ->offset(0)
                ;
                $offset = 0;
                $this->set('offset', $offset);
                $this->set(['routes' => $routes, 'token' => $token]);
            } else {
                return $this->redirect(['controller' => 'UserAuth', 'action' => 'login']);
            }
        } else {
            return $this->redirect(['controller' => 'UserAuth', 'action' => 'login']);
        }
    }

    public function detail($id = NULL) {
        $token = $this->request->query('token');
        if (!empty($token)) {
            // check token exist at table m_token_user_devices
            // get user_id if exist token
            $tokenOld = $this->Tokens->find('all', array(
                        'fields' => ['Tokens.token', 'Tokens.user_id'],
                        'conditions' => [
                            'Tokens.token' => $token,
                        ],
                    ))->first();
            if (!empty($tokenOld)) {
                if ($id) {
                    $arrayIndex = $this->_getArrayIndexRoutes(1);
                    if ($id != null && !isset($arrayIndex[$id])) {
                        $this->redirect(array('controller' => 'UserAuth', 'action' => 'login'));
                    } else if (isset($arrayIndex[$id])) {
                        $this->set('id', $id);
                        $id = $arrayIndex[$id];
                    }
                    // get data route follow id
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
                                'Users.web_id' => DEFINE_WEB_ID
                            ])
                            ->select(['Users.username', 'Users.del_flg', 'LocationsMin.id', 'LocationsMin.longitude', 'LocationsMin.latitude', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.country', 'Routes.last_address', 'Devices.name'])
                            ->distinct('Routes.id')
                            ->first();
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
                                ->where(['Images.route_id' => $id, 'Locations.type' => 2])
                                ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Images.id', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height', 'Images.size', 'Images.created', 'Locations.catch_time', 'Images.comment'])
                                ->order(['Locations.id' => 'ASC']);
                        $cnt = 0;
                        foreach ($images as $key => $value) {
                            if ($value) {
                                $cnt++;
                            }
                        }
                        $route['cnt'] = $cnt;
                        $this->set(['route_rd' => $route, 'images' => $images, 'token' => $token, 'id' => $id]);
                    }
                }
            } else {
                return $this->redirect(['controller' => 'UserAuth', 'action' => 'login']);
            }
        } else {
            return $this->redirect(['controller' => 'UserAuth', 'action' => 'login']);
        }
    }

    /**
     * load data Router
     * @return type json
     */
    public function loadMore() {
        // check token exist at table m_token_user_devices
        // get user_id if exist token
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $tokenOld = $this->Tokens->find('all', array(
                        'fields' => ['Tokens.token', 'Tokens.user_id'],
                        'conditions' => [
                            'Tokens.token' => $data['token'],
                        ],
                    ))->first();
            if (!empty($tokenOld)) {
                $user_id = $tokenOld['user_id'];
                if (!empty($data['offset'])) {

                    // get count
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
                                'Routes.user_id' => $user_id,
                                'Locations.latitude IS NOT' => null,
                                'Users.web_id' => DEFINE_WEB_ID
                                , 'Users.del_flg' => 0
                            ])
                            ->select(['Users.username', 'Users.del_flg', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.number', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.main', 'Routes.device_id', 'Routes.country', 'Devices.name'])
                            ->order(['Routes.time_start' => 'DESC', 'Routes.id' => 'DESC'])
                            ->distinct('Routes.id');

                    $arrayIndex = $this->_getArrayIndexRoutes();

                    // get data route
                    $routes = $query
                            ->limit(self::$limit)
                            ->offset($data['offset']);
                    if ($routes->count() > $data['offset']) {
                        $html = "";
                        foreach ($routes as $key => $route) {
                            if (isset($arrayIndex[$route['id']])) {
                                $route_id = $arrayIndex[$route['id']];
                            } else {
                                $route_id = $route['id'];
                            }
                            $html .= '<div class="item mb10 ov" id="' . $arrayIndex[$route['id']] . '">';
                            $html .= '<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 showMapMB" data-bind="' . $arrayIndex[$route['id']] . '">';
                            if ($route['Users']['del_flg'] == 1) {
                                $html .= '<span>' . __('DELETED USER:') . ' </span>' . $route['Users']['username'] . '<br>';
                            } else {
                                $html .= '<span>' . __('USER:') . ' </span>' . $route['Users']['username'] . '<br>';
                            }
                            $html .= '<span>' . __('Device ID:') . ' </span>' . $route['Devices']['name'] . '<br>';
                            //$html .= '<span>最終調査箇所: </span>' . $route['Locations']['longitude'] . '/' . $route['Locations']['latitude'] . '<br>';
                            $html .= '<span>' . __('SURVEY ID:') . ' </span>' . $route_id . '<br>';
                            $html .= '<span>' . __('DATE:') . '  </span>' . date('Y/m/d', strtotime($route['time_start'])) . '<br>';
                            $html .= '<span>' . __('LOCATION:') . ' </span>' . $route['country'] . '<br>';
                            $html .= '</div>';
                            $html .= '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 showData showDataMB">';
                            $html .= '<a class="target targetOnly" href="' . Router::url(['controller' => 'mobiles', 'action' => 'detail', $arrayIndex[$route['id']]]) . '?token=' . $data['token'] . '">';
                            $html .= $this->_getImageFirst($route['id']);
                            $html .= '</a>';
                            $html .= '</div>';
                            $html .= '</div>';
                        }
                        echo json_encode(array('status' => true, 'html' => $html));
                        die;
                    }
                }
            } else {
                echo json_encode(array('status' => false));
                die;
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
    protected function _getImageFirst($id = null) {
        $domain_name = DEFINE_DOMAIN_NAME;
        $imageTable = TableRegistry::get('Images');
        $url = '';
        if ($id) {
            $image = $imageTable->find('all')
                    ->where(['Images.route_id' => $id])
                    ->order(['Images.created' => 'ASC'])
                    ->first();
            if (!empty($image['name'])) {
                $url = "<amp-img src='" . $domain_name . "files/image/" . $id . "/" . $image['name'] . "'  class='' data-width='" . $image['width'] . "' data-height='" . $image['height'] . "' width='225' height='225' layout='responsive'/> </amp-img>";
            } else {
                $url = "<amp-img src='" . $domain_name . "img/default.png' class='' width='225' height='225' layout='responsive'/></amp-img>";
            }
        } else {
            $url = "<amp-img src='" . $domain_name . "img/default.png' class=''  width='225' height='225' layout='responsive'/></amp-img>";
        }

        return $url;
    }

    /**
     * Get array index route
     * @return array index route
     */
    protected function _getArrayIndexRoutes($route = 0) {
        $this->loadModel('Routes');
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
                ->select(['Routes.id', 'rownumber' => '(select @rownum := @rownum + 1 from ( select @rownum := 0 ) d2 )'])
                ->distinct('Routes.id')
                ->order(array('Routes.time_start' => 'DESC'))
                ->where(['Users.web_id' => DEFINE_WEB_ID, 'Locations.latitude IS NOT' => null, 'Users.del_flg' => 0]);

        $arrayQuery = $query->toArray();
        $arrayIndex = array();
        if ($route == 0) {
            foreach ($arrayQuery as $datum) {
                $arrayIndex[$datum['id']] = $datum['rownumber'];
            }
        } else {
            foreach ($arrayQuery as $datum) {
                $arrayIndex[$datum['rownumber']] = $datum['id'];
            }
        }
        return $arrayIndex;
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
                            array('Locations.route_id' => $data['id'], 'Locations.delete_flg' => 0),
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
                    if (empty($row['catch_time'])) {
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
                        ->where(['Routes.delete_flg' => 0, 'Locations.latitude IS NOT' => null, 'Users.web_id' => DEFINE_WEB_ID, 'Users.del_flg' => 0])
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
                                array('Locations.route_id' => $routes['id'], 'Locations.delete_flg' => 0),
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

}
