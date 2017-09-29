<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AppLanguageKeysTable extends Table {

    public function initialize(array $config) {
        $this->table('m_app_language_key');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }
    
    public function findAllKeys(){
        $query = $this->find('all', array(
           'fields' => array('id', 'name'),
           'order'  => array('id' => 'ASC')
        ));
        $arrayData = $query->toArray();
        $data = array();
        foreach ($arrayData as $datum) {
            $data[$datum->id] = $datum->name;
        }
        return $data;
    }
}
