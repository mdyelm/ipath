<?php

// src/Model/Table/UsersTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class LocationsTable extends Table {

    public function initialize(array $config) {
        $this->table('m_locations');
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
     * getDataByRouteId
     * @param type $routeId
     * @return type
     */
    public function getDataByRouteId($routeId) {
        $config = [
            'fields' => ['Locations.id','Locations.longitude','Locations.latitude','Locations.catch_time', 'Locations.delete_flg', 'Locations.image_id'],
            'join' => [
                'images' => [
                    'table' => 'm_images',
                    'type' => 'LEFT',
                    'alias' => 'Images',
                    'conditions' =>
                        [
                        'Images.id = Locations.image_id',
                    ],
                ]
            ],            
            'conditions' => [
//                'delete_flg' => 0,
//                'route_id' => $routeId
                'OR' => array(
                    array('Locations.route_id' => $routeId),
                    array('Images.route_id' => $routeId),
                ),                
            ],
            'order' => ['catch_time' => 'ASC']
        ];
        $result = $this->find('all', $config)->all()->toArray();
        return $result;
    }
    /**
     * editLocation
     * @param type $data
     * @return type
     */
    public function editLocation($data) {
        $r = false;
        if (!empty($data['dataSave']) && !empty($data['dataId'])) {
            $dataSave = $this->get($data['dataId']);
            if($dataSave){
                if($data['dataSave']=="latitude"){
                    $dataSave->latitude = $data['valSave'];
                }else{
                    $dataSave->longitude = $data['valSave'];
                }
                $this->save($dataSave);
                $r = true;
            }
        }
        return $r;
    }
}
