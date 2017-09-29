<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
    
//    public function importCoordinate(){
//        $connection = ConnectionManager::get('default');
//        if ($this->request->is('post')){
//            $data = $this->request->data;
//            if (isset($data['file']) && $data['file']['size'] > 0) {
//                
//                $route_id = 1;
//                if (isset($data['route'])){
//                    $route_id = intval($data['route']);
//                }
//                
//                $query = "INSERT INTO m_locations (latitude, longitude, route_id, type, created, modified) VALUES ";
//                
//                //$content = file_get_contents($data['file']['tmp_name']);
//                $myfile = fopen($data['file']['tmp_name'], "r") or die("Unable to open file!");
//                $date = date('Y-m-d H:i:s');
//                $query_data = '';
//                $first = true;
//                // Output one line until end-of-file
//                while(!feof($myfile)) {                    
//                    $line = fgets($myfile);
//                    $line = str_replace("\n", "", $line);
//                    $coordinate = explode(',', $line);
//                    if (!empty($coordinate) && isset($coordinate[1])) {
//                        if ($first) {
//                            $query_data = "('" . $coordinate[0] . "', '" . $coordinate[1] . "', '" . $route_id . "', 0, '" . $date . "', '" . $date . "')";
//                            $first = FALSE;
//                        }
//                        else {
//                            $query_data .= ",('" . $coordinate[0] . "', '" . $coordinate[1] . "', '" . $route_id . "', 0, '" . $date . "', '" . $date . "')";
//                        }
//                    }
//                }
//                $connection->execute($query.$query_data);
//                fclose($myfile);
//            }
//        }
//    }
    public function policy(){
        $this->viewBuilder()->layout('policy');
    }
    /**
    * set language 
    */
    public function language() {
       $language = $this->request->query['language'];
       //$this->request->session()->write('Config.language', $language);
       $this->Cookie->write('Config.language', $language);
       $this->redirect($this->referer());
    }
    /**
     * slideView
     */
    public function slideView($language = null,$type=null) {
        if(!empty($language) && !empty($type)){
            $this->viewBuilder()->layout('slide_guide');
            $this->loadModel('Guide');
            $config = array(
                'join' => [
                    'users' => [
                        'table' => 'm_app_language',
                        'type' => 'LEFT',
                        'alias' => 'AppLanguages',
                        'conditions' =>
                        [
                            'AppLanguages.id = Guide.language_id',
                        ],
                    ],
                ],
                'conditions' => array(
                    'AppLanguages.language'=>$language,
                    'AppLanguages.del_flg'=>0,
                    'Guide.del_flg'=>0,
                    'Guide.type' =>$type
                ),
                'fields' => array('Guide.id', 'Guide.guide_text', 'Guide.image'),
                'order' => array('Guide.created'),
            );
            $results = $this->Guide->find('all', $config)->all()->toArray();
            $this->set('slide', $results);
        }else{
            $this->redirect($this->referer());
        }
    }
}
