<?php

namespace App\View\Helper;

use Cake\ORM\TableRegistry;
use Cake\View\Helper;

class AppHelper extends Helper {

    public $helpers = ['Html'];
    
    /**
     * get first image in route
     * @param type $id
     * @return string
     */
    public function getImageFirst($id = null) {
        $imageTable = TableRegistry::get('Images');
        $url = '';
        if ($id) {
            $image = $imageTable->find('all')
                    ->where(['Images.route_id' => $id])
                    ->order(['Images.created' => 'ASC'])
                    ->first();
            if (!empty($image['name'])) {
                $url = "<img src='" . $this->request->webroot . "files/image/" . $id . "/" . $image['name'] . "'  class='imgFirst' data-width='" . $image['width'] . "' data-height='" . $image['height'] . "' /> ";
            } else {
                $url = "<img src='" . $this->request->webroot . "img/default.png' class='imgFirst' data-width='225' data-height='225' />";
            }
        } else {
            $url = "<img src='" . $this->request->webroot . "img/default.png' class='imgFirst' data-width='225' data-height='225'/>";
        }

        return $url;
    }
    
    public function getImageFirstApp($id = null) {
        $imageTable = TableRegistry::get('Images');
        $url = '';
        if ($id) {
            $image = $imageTable->find('all')
                    ->where(['Images.route_id' => $id])
                    ->order(['Images.created' => 'ASC'])
                    ->first();
            if (!empty($image['name'])) {
                $url = "<amp-img src='" . $this->request->webroot . "files/image/" . $id . "/" . $image['name'] . "'  class='' data-width='" . $image['width'] . "' data-height='" . $image['height'] . "' width='225' height='225' layout='responsive'/></amp-img>";
            } else {
                $url = "<amp-img src='" . $this->request->webroot . "img/default.png' class='' data-width='225' data-height='225' width='225' height='225' layout='responsive'/></amp-img>";
            }
        } else {
            $url = "<amp-img src='" . $this->request->webroot . "img/default.png' class='' data-width='225' data-height='225' width='225' height='225' layout='responsive'/></amp-img>";
        }

        return $url;
    }
    
    /**
     * get count surveys in Device
     * @param type $dev_id
     * @return type
     */
    public function getCountDevice($dev_id = NULL) {
        $routesTable = TableRegistry::get('Routes');
        $cnt = 0;
        $cnt = $routesTable->find('all')
                ->hydrate(false)
                ->where(['device_id' => $dev_id])
                ->order(['Routes.id' => 'DESC'])
                ->count();
        return $cnt;
    }

    /**
     * Convert direction to rotation
     * @param type $rotation
     * @return string
     */
    public function changeDirectionByRotation($rotation){
        
        $rotation = intval($rotation) % 360;
        //North
        if (($rotation >= 0 && $rotation <= 45) || ($rotation > 315 && $rotation <= 360)){
            return "North";
        }
        //East
        else if ($rotation > 45 && $rotation <= 135){
            return "East";
        }
        //South
        else if ($rotation > 135 && $rotation <= 225){
            return "South";
        }
        //West
        else if ($rotation > 225 && $rotation <= 315){
            return "West";
        }
        else {
            return "Unvaiable";
        }
    }
    
    /**
     * Convert degrees.decimal° to degrees°minute’seconds"
     * @param type $decimal
     * @return string
     */
    public function convertDecimalToSexagesimal($decimal) {
        $return = '';
        
        // check coordinate is negative
        if ($decimal < 0) {
            $decimal *= -1;
            $return .= '-';
        }
        // add degress
        $return .= floor($decimal) . "° , ";
        $decimal -= floor($decimal);
        $decimal *= 60;
        
        // add minute
        $minutes = floor($decimal);
        if ($minutes >= 10) {
            $return .= $minutes . "' , ";
        }
        else {
            $return .= "&nbsp;&nbsp;" . $minutes . "' , ";
        }
        $decimal -= floor($decimal);
        $decimal *= 60;
        
        // add s
        $second = $decimal;
        if ($second >= 10) {
            $return .= number_format($second, 3) . "''";
        }
        else {
            $return .= "&nbsp;&nbsp;" . number_format($second, 3) . "''";
        }        
        
        return $return;
    }

    /**
     * Get route id at specific server
     * @param array $arrayIndex
     * @param int $routeId
     * @return int
     */
    public function getRouteIndex($arrayIndex, $routeId) {
        if (isset($arrayIndex[$routeId])) {
            return $arrayIndex[$routeId];
        }
        else return $routeId;
    }
}
