<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class SendMailUsersTable extends Table {

    public function initialize(array $config) {
        $this->table('t_send_mail_user');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }

    /**
     * Find record by sender value and receiver value
     * @param type $sender
     * @param type $receiver
     * @return int
     */
    public function findBySenderAndReceiver($sender, $receiver) {
        $query = $this->find('all')
                ->select(['id'])
                ->where(['sender' => $sender, 'receiver' => $receiver])
                ->first();
        if ($query)
            return $query->id;
        else
            return 0;
        ;
    }

    public function getAllEmailsAtSettingUser($user_id) {
        
        $userTable = TableRegistry::get('Users');
        $checkUser = $userTable->find('all')
                ->where(['id' => $user_id])
                ->first();
        $webId = $checkUser['web_id'];
        
        $data = array();
        $query = $this->find('all')
                ->select(['SendMailUsers.id', 'Users.username', 'Users.email', 'Users.company_email'])
                ->join([
                    'user' => [
                        'table' => 'm_users',
                        'type' => 'LEFT',
                        'alias' => 'Users',
                        'conditions' =>
                        [
                            'SendMailUsers.receiver = Users.id',
                        ],
                    ]
                ])
                ->where(['sender' => $user_id,'active_flg' => 1,'Users.del_flg' => 0, 'Users.web_id' => $webId])
        ;
        if ($query) {
            $result = $query->toArray();
            foreach ($result as $row) {
                if ($row['Users']['email']) {
                    array_push($data, array('username' => $row['Users']['username'], 'mail' => $row['Users']['email']));
                }
                if ($row['Users']['company_email']) {
                    array_push($data, array('username' => $row['Users']['username'], 'mail' => $row['Users']['company_email']));
                }
            }
        }
//        $data = array_unique($data);
        return $data;
    }

}
