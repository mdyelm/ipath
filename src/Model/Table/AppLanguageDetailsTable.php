<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AppLanguageDetailsTable extends Table {

    public function initialize(array $config) {
        $this->table('m_app_language_detail');
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
     * Find all key and value
     * @param type $id
     * @return type
     */
    public function findAllKeyAndValue($id){
        $query = $this->find('all', array(
           'fields' => array('id', 'app_language_key_id', 'value'),
           'conditions' => array('app_language_id' => $id),
           'order'  => array('app_language_key_id' => 'ASC')
        ));
        $arrayData = $query->toArray();
        $data = array();
        foreach ($arrayData as $datum) {
            $data[$datum->app_language_key_id] = array($datum->id, $datum->value);
        }
        return $data;
    }

    /**
     * Find all data language to app
     */
    public function findAllDataLanguage() {
        $query = $this->find('all')
                ->hydrate(false)
                ->join([
                    'appLanguageKey' => [
                        'table' => ' m_app_language_key',
                        'type' => 'INNER',
                        'alias' => 'AppLanguageKeys',
                        'conditions' =>
                        [
                            'AppLanguageKeys.id = AppLanguageDetails.app_language_key_id',
                        ],
                    ],
                    'appLanguage' => [
                        'table' => 'm_app_language',
                        'type' => 'INNER',
                        'alias' => 'AppLanguages',
                        'conditions' =>
                        [
                            'AppLanguages.id = AppLanguageDetails.app_language_id',
                        ],
                    ],
                ])
                ->select(['AppLanguageDetails.id', 'AppLanguages.language', 'AppLanguageKeys.name', 'AppLanguageDetails.value'])
                ->where(['AppLanguages.del_flg' => 0, 'AppLanguages.publish_flg' => 0])
                ->order(array('AppLanguageDetails.app_language_id','AppLanguageDetails.app_language_key_id' => 'ASC'));
        $result = $query->toArray();
        $data = array();
        foreach($result as $datum){
            $values = array();
            $values['id'] = $datum['id'];
            $values['value'] = $datum['value'];
            $values['language'] = $datum['AppLanguages']['language'];
            $values['key'] = $datum['AppLanguageKeys']['name'];
            array_push($data, $values);
        }
        return $data;
    }
}
