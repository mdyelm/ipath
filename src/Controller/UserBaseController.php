<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Auth\LegacyPasswordHasher;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class UserBaseController extends AppController {

    //public $helpers = array('Html', 'Form');

    public function initialize() {
        parent::initialize();
//        $this->loadComponent('Security');
        $this->loadComponent('Cookie');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'loginRedirect' => [
                'controller' => 'Surveys',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'UserAuth',
                'action' => 'login',
            ],
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password',
                    ],
                    'passwordHasher' => [
                        'className' => 'Legacy',
                    ],
                    'userModel' => 'Users',
                    'finder' => 'auth'
                ]
            ],
            'loginAction' => [
                'controller' => 'UserAuth',
                'action' => 'login'
            ],
            'logoutAction' => [
                'controller' => 'UserAuth',
                'action' => 'logout'
            ],
        ]);

        $this->Cookie->config([
            'expires' => '+12 months',
        ]);
    }

    public function beforeFilter(Event $event) {
        if ($this->Auth->user()) {
            $this->set('authUser', $this->Auth->user());
        }
        $this->set('arrayIndex', $this->_getArrayIndexRoutes());
    }

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
                ->where(['Users.web_id' => DEFINE_WEB_ID, 'Locations.latitude IS NOT' => null, 'Users.del_flg' => 0]); //check WEB_ID

        $arrayQuery = $query->toArray();
        $arrayIndex = array();
        if ($route == 0) {
            foreach ($arrayQuery as $datum) {
                $arrayIndex[$datum['id']] = $datum['rownumber'];
            }
        }
        else {
            foreach ($arrayQuery as $datum) {
                $arrayIndex[$datum['rownumber']] = $datum['id'];
            }
        }
        return $arrayIndex;
    }
}