<?php

namespace App\Shell;

use Cake\Mailer\Email;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;
use Psy\Shell as PsyShell;
use Cake\Core\Configure;
use Cake\I18n\I18n;

class SendMailAfterCompleteSurveyShell extends Shell {

    protected function _loadAllModel() {
        $this->loadModel('Users');
        $this->loadModel('SendMailUsers');
        $this->loadModel('Routes');
        $this->loadModel('Images');
        $this->loadModel('AppLanguages');
    }

    public function main() {

        $this->_loadAllModel();
        $input = $this->args;
        if (count($input) >= 2) {
            $user_id = $input[0];
            $route_id = $input[1];
            if (isset($input[2])) {
                $language = $input[2];
            } else {
                $language = 'English';
            }
            if ($user_id !== '' && $route_id != '') {
                // get user name author
                $author = $this->Users->findUserNameById($user_id);
                // find all email by user_id
                $emails = $this->SendMailUsers->getAllEmailsAtSettingUser($user_id);
                // get language
                $shortName = $this->AppLanguages->findShortNameByName($language);
                
                $arrayRouteIndex = $this->_getArrayIndexRoutes($user_id);

                // check shortname
                $folder = __DIR__ . '/../Locale/' . $shortName;
                if (!is_dir($folder)) {
                    $shortName = 'en_US';
                }
                I18n::locale($shortName);

                // process user name
                $listName = '';
                $first = TRUE;
                $arrayName = array();
                foreach ($emails as $value) {
                    if ($value['username'] != $author && !in_array($value['username'], $arrayName)) {
                        if ($first) {
                            if ($shortName == 'ja_JP') {
                                $listName .= $value['username'] . "様";
                            } else {
                                $listName .= "Mr. " . $value['username'];
                            }
                            $first = FALSE;
                        } else {
                            if ($shortName == 'ja_JP') {
                                $listName .= ", " . $value['username'] . "様";
                            } else {
                                $listName .= ", Mr. " . $value['username'];
                            }
                        }
                    }
                    array_push($arrayName, $value['username']);
                }
                if ($listName != '') {
                    if ($shortName == 'ja_JP') {
                        $listName .= ", " . $author . "様";
                    } else {
                        $listName .= ", Mr. " . $author;
                    }
                } else {
                    if ($shortName == 'ja_JP') {
                        $listName .= $author . "様";
                    } else {
                        $listName .= "Mr. " . $author;
                    }
                }
                // get info route by route id
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
                        ->where(['Routes.id' => $route_id, 'Locations.latitude IS NOT' => null])
                        ->select(['Users.username', 'Users.del_flg', 'LocationsMin.id', 'LocationsMin.longitude', 'LocationsMin.latitude', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.country', 'Routes.last_address', 'Devices.name'])
                        ->distinct('Routes.id')
                        ->first();

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
                        ->where(['Images.route_id' => $route_id, 'Locations.type' => 2, 'Locations.latitude IS NOT' => null])
                        ->select(['Locations.id'])
                ;
                $image_number = $images->count();

                // get template email and subject email
                $subject = Configure::read('TitleSendMailAfterCompleteSurvey');
                $template = Configure::read('TemplateSendMailAfterCompleteSurvey');
                
                if (isset($arrayRouteIndex[$route_id])) {
                    $route_id = $arrayRouteIndex[$route_id];
                    $route['id'] = $route_id;
                }
                
                // send mail 
                foreach ($emails as $value) {
                    $emailSend = new Email('default');
                    $emailSend->to($value['mail'])
                            ->template(__($template))
                            ->emailFormat('html')
                            ->subject(__($subject))
                            ->viewVars(array('data' => array('username' => $listName, 'route_id' => $route_id, 'author' => $author, 'image_number' => $image_number)
                                , 'route' => $route))
                            ->send();
                }
                die;
            } else {
                die;
            }
        }
        $this->out('Hello world.');
    }

    protected function _getArrayIndexRoutes($user_id = 0) {
        $this->loadModel('Routes');
        $this->loadModel('Users');
        $arrayIndex = array();
        $user = $this->Users->find('all', array(
            'fields' => array('web_id'),
            'conditions' => array('id' => $user_id)
        ))->first();
        if (!empty($user)) {
            $defindWebId = $user->web_id;
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
                    ->where(['Users.web_id' => $defindWebId, 'Locations.latitude IS NOT' => null, 'Users.del_flg' => 0]); //check WEB_ID

            $arrayQuery = $query->toArray();
//            if ($route == 0) {
                foreach ($arrayQuery as $datum) {
                    $arrayIndex[$datum['id']] = $datum['rownumber'];
                }
//            } else {
//                foreach ($arrayQuery as $datum) {
//                    $arrayIndex[$datum['rownumber']] = $datum['id'];
//                }
//            }
        }
        return $arrayIndex;
    }     
}
