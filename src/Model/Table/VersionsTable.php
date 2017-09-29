<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Entity\User;
use Cake\Validation\Validator;
//use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Core\Configure;

class VersionsTable extends Table {

    public function initialize(array $config) {
        $this->table('m_versions');
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
     * Find all of version web, android, ios
     * @return type array
     */
    public function findAllVersion() {
        $query = $this->find('all', array(
            'fields' => ['id', 'device', 'version', 'released', 'content'],
            'conditions' => [
                'Versions.device IN' => [Configure::read('Version.web'), Configure::read('Version.iOS'), Configure::read('Version.android')],
                'Versions.del_flg' => 0
            ],
            'order' => ['Versions.device' => 'ASC', 'Versions.released' => 'DESC']
        ));
        $data = $query->toArray();
        $web = $iOS = $android = array();
        if (!empty($data)) {
            foreach ($data as $datum) {
                if ($datum->device == Configure::read('Version.web')) {
                    array_push($web, $datum);
                } else if ($datum->device == Configure::read('Version.iOS')) {
                    array_push($iOS, $datum);
                } else if ($datum->device == Configure::read('Version.android')) {
                    array_push($android, $datum);
                }
            }
        }

        return array($web, $iOS, $android);
    }

    /**
     * Find app language version
     * @return string
     */
    public function findAppLanguageVersion() {
        // get version
        $query = $this->find('all', array(
                    'fields' => ['version'],
                    'conditions' => [
                        'Versions.device' => Configure::read('Version.app_language')
                    ],
                    'order' => ['Versions.released' => 'DESC']
                ))->first();
        if (!empty($query)) {
            $data = $query->toArray();
            $version = $data['version'];
        }
        // do not version set first version 1.0
        else {
            $entity = $this->newEntity();
            $entity->version = '1.0';
            $entity->device = Configure::read('Version.app_language');
            $entity->released = date('Y-m-d');
            if ($this->save($entity)) {
                $version = '1.0';
            }
        }
        return $version;
    }

    /**
     * Type = 1, update before ., type != 1, update after .
     * @param type $type
     */
    public function updateAppLanguageVersion($type = null) {

        // get version
        $query = $this->find('all', array(
                    'fields' => ['id', 'version'],
                    'conditions' => [
                        'Versions.device' => Configure::read('Version.app_language')
                    ],
                    'order' => ['Versions.released' => 'DESC']
                ))->first();
        if (!empty($query)) {
            $data = $query->toArray();
            $versionArray = explode('.', $data['version']);
            if ($type == 1) {
                $versionArray[0] ++;
                $versionArray[1] = 0;
            } else {
                $versionArray[1] ++;
            }
            $version = $versionArray[0] . "." . $versionArray[1];
            $updateData = array('version' => $version);
            $this->patchEntity($query, $updateData);
            $this->save($query);
        }
    }

    /**
     * Validate create
     */
    public function validationAdminAdd(Validator $validator) {
        return $validator
                        ->notEmpty('version', __('Please input here'))
        ;
    }

    /**
     * Find web language version
     * @return string
     */
    public function findWebLanguageVersion() {
        // get version
        $query = $this->find('all', array(
                    'fields' => ['version'],
                    'conditions' => [
                        'Versions.device' => Configure::read('Version.web'),
                        'Versions.del_flg' => 0
                    ],
                    'order' => ['Versions.released' => 'DESC']
                ))->first();
        if (!empty($query)) {
            $data = $query->toArray();
            $version = $data['version'];
        }
        // do not version set first version 1.0
        else {
            $entity = $this->newEntity();
            $entity->version = '1.0';
            $entity->device = Configure::read('Version.web');
            $entity->released = date('Y-m-d');
            if ($this->save($entity)) {
                $version = '1.0';
            }
        }
        return $version;
    }

}
