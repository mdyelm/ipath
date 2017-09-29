<?php
namespace App\Model\Table;
use Cake\ORM\Table;

class AppTranslateLanguagesTable extends Table {

    public function initialize(array $config) {
        $this->table('m_app_language_translate');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }
    public function saveDataTranslate($idTranslate,$dataTranslate){
        if(!empty($dataTranslate['language'])){
            foreach ($dataTranslate['language'] as $key => $value) {
                $translate = $this->find('all')
                    ->select(array('id','source_language_id','need_language_id','translate'))
                    ->where(['source_language_id' => $idTranslate,'need_language_id'=>$key])
                    ->first();
                if(empty($translate)){
                    $translate = $this->newEntity();
                    $translate->source_language_id = $idTranslate;
                    $translate->need_language_id = $key;
                }
                $translate->translate = $value;
                $this->save($translate);
            }
        }
        return true;
    }
    /**
     *  get all translate language
     * @param type $dataLanguage
     * @return type
     */
    public function findAllTranslateLanguage($dataLanguage){
        $dataKeyLanguage = array();
        $dataTranslate = array();
        //$dataTranslateCheck = array();
        if(!empty($dataLanguage)){
            foreach ($dataLanguage as $valLan) {
                //set default translte language
                foreach ($dataLanguage as $valDefault) {
                    $dataTranslate[$valLan['language']][$valDefault['language']]="";
                }
                //
                $dataKeyLanguage[] = $valLan['language'];
                $translate = $this->find('all')
                ->hydrate(false)
                ->select(array(
                    'AppTranslateLanguages.id','AppTranslateLanguages.translate',
                    'AppLanguages.language',
                ))
                ->join([
                    'm_app_language' => [
                        'table' => ' m_app_language',
                        'type' => 'LEFT',
                        'alias' => 'AppLanguages',
                        'conditions' =>
                        [
                            'AppLanguages.id = AppTranslateLanguages.need_language_id',
                        ],
                    ],
                ])
                ->where([
                    'AppTranslateLanguages.source_language_id' => $valLan['id'],
                    'AppLanguages.del_flg' => 0,
                    'AppLanguages.publish_flg' => 0,
                ])
                ->all()->toArray();
                if(!empty($translate)){
                    foreach ($translate as $valTran) {
                        $dataTranslate[$valLan['language']][$valTran['AppLanguages']['language']] = $valTran['translate'];
                    }
                }
                //$dataTranslateCheck[] = $dataTranslate[$valLan['language']];
            }
        }
        $r = array('dataKeyLanguage'=>$dataKeyLanguage,'dataTranslate' =>$dataTranslate);
        return $r;
    }
    /**
     *  get translate by language
     * @param type $dataLanguage
     * @return type
     */
    public function getTranslateByLanguage($dataLanguage){
        $data = array();
        if(!empty($dataLanguage)){
            $translate = $this->find('all')
            ->select(array(
                'AppTranslateLanguages.id','AppTranslateLanguages.translate',
                'AppLanguages.language',
            ))
            ->join([
                'm_app_language' => [
                    'table' => ' m_app_language',
                    'type' => 'LEFT',
                    'alias' => 'AppLanguages',
                    'conditions' =>
                    [
                        'AppLanguages.id = AppTranslateLanguages.need_language_id',
                    ],
                ],
            ])
            ->where([
                'AppTranslateLanguages.source_language_id' => $dataLanguage['id'],
                'AppLanguages.del_flg' => 0,
                //'AppLanguages.publish_flg' => 0,
            ])
            ->all()->toArray();
            if(!empty($translate)){
                foreach ($translate as $valTran) {
                    $data[$dataLanguage['language']][$valTran['AppLanguages']['language']] = $valTran['translate'];
                }
            }
        }
        $r = array();
        $r[] = $data;
        return $r;
    }
}
