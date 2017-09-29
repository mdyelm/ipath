<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class RoutesTable extends Table {

    public function initialize(array $config) {
        $this->table('m_routes');
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
     * checkRoute
     * @param type $id
     * @param type $idWeb
     * @return type
     */
    public function checkRoute($id) {
        $config = [
            'join' => [
                'user' => [
                    'table' => 'm_users',
                    'type' => 'LEFT',
                    'alias' => 'Users',
                    'conditions' =>
                        [
                        'Users.id = Routes.user_id',
                    ],
                ]
            ],
            'conditions' => [
                'Users.del_flg' => 0,
                'Users.web_id' => DEFINE_WEB_ID, //check WEB_ID
                'Routes.id' => $id
            ],
        ];
        $result = $this->find('all', $config)->first();
        return $result;
    }

    /**
     * deleteRoute
     * @param type $id
     * @return boolean
     */
    public function updateDelFlg($id,$flg) {
        $data = $this->get($id);
        if (!empty($data)) {
            $data->delete_flg = $flg;
            $this->save($data);
        }
        return true;
    }
    
    
    public function hiddenRoute($id,$flg) {
        $data = $this->get($id);
        if (!empty($data)) {
            $data->hidden_flg = $flg;
            $this->save($data);
        }
        return true;
    }
}
