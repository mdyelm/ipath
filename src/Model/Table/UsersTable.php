<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Entity\User;
use Cake\Validation\Validator;
//use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;

class UsersTable extends Table {

    public function initialize(array $config) {
        $this->table('m_users');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }

    public function findAuth(\Cake\ORM\Query $query, array $options) {
        $query
                ->select(['id', 'username', 'password', 'admin_flg'])
                ->where([
                    'Users.del_flg' => 0,
                    'Users.web_id' => DEFINE_WEB_ID,
        ]);
        return $query;
    }

//    protected function _setPassword($password) {
//        if (strlen($password) > 0) {
//            return (new DefaultPasswordHasher)->hash($password);
//        }
//    }

    public function beforeSave(Event $event) {
        $entity = $event->data['entity'];
//        if (strlen($entity->password) > 0 && !empty($entity->password)) {
//            $entity->password = $this->_setPassword($entity->password);
//        } else {
//            unset($entity->password);
//        }
        return true;
    }

    public function validationAdminEdit(Validator $validator) {
        $validator
                ->notEmpty('username', __('Please input here'))
                ->add('username', 'unique', [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __('Username already exists'),
                    'last' => true
                ])
                ->add('username', [
                    'match_halfwidth' => [
                        'rule' => array('custom', "/^[A-Za-z\d\-\_\^]+$/"),
                        'message' => __('Please input character by numbers or alphabet letters'),
                        'last' => true,
                    ]
        ]);
        $validator->allowEmpty('email')
//                ->notEmpty('email', __('Please input here'))
//                ->add('email', 'validFormat', [
//                    'rule' => 'email',
//                    'message' => __('Please input email\'s format'),
//                
//                ])
                ->add('email', [
                    'checkGmail' => [
                        'rule' => 'checkGmail',
                        'provider' => 'table',
                        'message' => __('Please input gmail\'s format')
                    ]
                ]);
        $validator
                ->notEmpty('company_email', __('Please input here'))
                ->add('company_email', 'validFormat', [
                    'rule' => 'email',
                    'message' => __('Please input email\'s format'),
        ]);
        $validator
                ->notEmpty('password', __('Please input here'))
                ->add('password', [
                    'min_length' => [
                        'rule' => ['minLength', 8],
                        'message' => __('Please input 8 to 15 character by numbers and letters'),
                        'allowEmpty' => true,
                        'last' => true,
                    ]
                ])
                ->add('password', [
                    'max_length' => [
                        'rule' => ['maxLength', 15],
                        'message' => __('Please input 8 to 15 character by numbers and letters'),
                        'allowEmpty' => true,
                        'last' => true,
                    ]
                ])->add('password', [
            'match_password' => [
                'rule' => array('custom', "/^\S*(?=.*[a-zA-Z])(?=.*[0-9])\S*$/"),
                'message' => __('Please input 8 to 15 character by numbers and letters'),
                'last' => true,
            ]
        ]);
        return $validator;
    }

    public function validationAdminAdd(Validator $validator) {
        $validator
                ->notEmpty('username', __('Please input here'))
                ->add('username', 'unique', [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __('Username already exists'),
                    'last' => true
                ])->add('username', [
            'match_halfwidth' => [
                'rule' => array('custom', "/^[A-Za-z\d\-\_\^]+$/"),
                'message' => __('Please input character by numbers or alphabet letters'),
                'last' => true,
            ]
        ]);

        $validator->allowEmpty('email')
//                ->notEmpty('email', __('Please input here'))
//                ->add('email', 'validFormat', [
//                    'rule' => 'email',
//                    'message' =>  __('Please input email\'s format')
//                ])
                ->add('email', [
                    'checkGmail' => [
                        'rule' => 'checkGmail',
                        'provider' => 'table',
                        'message' => __('Please input gmail\'s format')
                    ]
                ]);
        $validator
                ->notEmpty('company_email', __('Please input here'))
                ->add('company_email', 'validFormat', [
                    'rule' => 'email',
                    'message' => __('Please input email\'s format'),
        ]);
        $validator
                ->notEmpty('password', __('Please input here'))
                ->add('password', [
                    'min_length' => [
                        'rule' => ['minLength', 8],
                        'message' => __('Please input 8 to 15 character by numbers and letters'),
                        'allowEmpty' => true,
                        'last' => true,
                    ]
                ])
                ->add('password', [
                    'max_length' => [
                        'rule' => ['maxLength', 15],
                        'message' => __('Please input 8 to 15 character by numbers and letters'),
                        'allowEmpty' => true,
                        'last' => true,
                    ]
                ])->add('password', [
            'match_password' => [
                'rule' => array('custom', "/^\S*(?=.*[a-zA-Z])(?=.*[0-9])\S*$/"),
                'message' => __('Please input 8 to 15 character by numbers and letters'),
                'last' => true,
            ]
        ]);

        return $validator;
    }

    public function validationOnlyCheckAdd(Validator $validator) {
        $validator = $this->validationAdminAdd($validator);
        $validator->remove('email');
        $validator->allowEmpty('email')
//                ->add('email', 'validFormat', [
//                    'rule' => 'email',
//                    'message' => __('Please input email\'s format'),
//                ])
                ->add('email', [
                    'checkGmail' => [
                        'rule' => 'checkGmail',
                        'provider' => 'table',
                        'message' => __('Please input gmail\'s format')
                    ]
                ]);
        return $validator;
    }

    public function validationOnlyCheckEdit(Validator $validator) {
        $validator = $this->validationAdminEdit($validator);
        $validator->remove('email');
        $validator->allowEmpty('email')
//                ->add('email', 'validFormat', [
//                    'rule' => 'email',
//                    'message' => __('Please input email\'s format'),
//                ])
                ->add('email', [
                    'checkGmail' => [
                        'rule' => 'checkGmail',
                        'provider' => 'table',
                        'message' => __('Please input gmail\'s format')
                    ]
                ]);
        return $validator;
    }

    public function findUserNameById($id) {
        $query = $this->find('all')
                ->select(['username'])
                ->where(['id' => $id])
                ->first();
        return $query->username;
    }

    /**
     * Get all active users
     * @return array
     */
    public function getAllActiveUser($id = null) {

        $checkUser = $this->find('all')
                ->select(['Users.id','Users.web_id'])
                ->where(['Users.id' => $id])
                ->first();        
        $webId = $checkUser['web_id'];
        
        $data = array();
        $query = $this->find('all')
                ->select(['Users.id', 'Users.username', 'SendMailUsers.active_flg'])
                ->where(['del_flg' => 0, 'web_id' => $webId])
                ->join([
                    'send_mail_user' => [
                        'table' => 't_send_mail_user',
                        'type' => 'LEFT',
                        'alias' => 'SendMailUsers',
                        'conditions' =>
                        [
                            'SendMailUsers.receiver = Users.id',
                            'SendMailUsers.sender' => $id
                        ],
                    ]
                ])
                ->order(['Users.username' => 'ASC'])
        ;
        if ($query) {
            $data[0] = array();
            $result = $query->toArray();
            foreach ($result as $value) {
                $datum = array();
                $datum['id'] = $value['id'];
                $datum['username'] = $value['username'];
                $datum['flag'] = $value['SendMailUsers']['active_flg'] == 1 ? 1 : 0;
                // if id user search equal $id => push to top
                if ($value['id'] == $id) {
                    $data[0] = $datum;
                } else {
                    array_push($data, $datum);
                }
            }
        }
        return $data;
    }

    public function checkGmail($check, array $context) {
        $r = false;
        $checkTrim = trim($check)." ";
        $checkGmail = substr($checkTrim,-11,-1);
//        if(filter_var(trim($check), FILTER_VALIDATE_EMAIL) && $checkGmail=="@gmail.com"){
//            $r = true;
//        }
        if ($check == '' || (preg_match("/^[a-z0-9](\.?[a-z0-9]){5,}@gmail\.com$/", $check) === 1 && strlen($check) >= 16 && strlen($check) <= 40)){
            $r = true;
        }
        return $r;
    }

}
