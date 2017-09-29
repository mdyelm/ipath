<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AppLanguagesTable extends Table {

    public function initialize(array $config) {
        $this->table('m_app_language');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
        $this->hasMany('AppLanguageDetails');
//            ->setForeignKey('app_language_id')
//            ->setDependent(true);        
    }
    
    /**
     * Validate create
     */
    public function validationAdminAdd(Validator $validator) {
        return $validator
                ->notEmpty('language',  __('Please input here'))
                ->add('language', 'unique', [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' =>  __('Language already exists'),
                    'last' => true
                ])
        ;   
    }    
    
    /**
     * Get all languages
     * @return type
     */
    public function findAllLanguages(){
        $query = $this->find('all')->select(array('id', 'language', 'publish_flg', 'created'))
                ->where(['del_flg' => 0]);
        if (!empty($query)){
            return $query->toArray();
        }
        else {
            return null;
        }
    }
    
    /**
     * Check exist key
     */
    public function hasExistLanguage($id) {
        $query = $this->find('all')->select(array('id'))->where(array('id' => $id, 'del_flg' => 0))->first();
        if ($query) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Check exist key
     */
    public function findShortNameByName($language) {
        $query = $this->find('all')->select(array('shortname'))->where(array('language' => $language, 'del_flg' => 0))->first();
        if ($query) {
            return $query->shortname;
        }
        else {
            return 'en_US';
        }
    }
    /**
     * Get all languages
     * @return type
     */
    public function findAllLanPublish(){
        $query = $this->find('all')->select(array('id', 'language', 'publish_flg', 'created'))
                ->hydrate(false)
                ->where(['del_flg' => 0,'publish_flg'=>0]);
        if (!empty($query)){
            return $query->toArray();
        }
        else {
            return null;
        }
    }
    /**
     * Get all languages
     * @return type
     */
    public function findRowByLanguagePublicNone($language){
        $query = $this->find('all')->select(array('id', 'language', 'publish_flg', 'created'))
                ->hydrate(false)
                ->where(['del_flg' => 0,'language'=>$language])
                ->first();
        return $query;
    }
}
