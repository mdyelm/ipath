<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GuideTable extends Table {

    public function initialize(array $config) {
        $this->table('m_guide');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }

    public function validationGuide(Validator $validator) {
        $validator
                ->notEmpty('guide_text', __('Please input here'))
                ->add('language', 'unique', [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __('Language already exists'),
                    'last' => true
        ]);
        return $validator;
    }

}
