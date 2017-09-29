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

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Event\Event;
//use Cake\Core\Configure;
use Cake\I18n\I18n;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Cookie');
        $lang = $this->Cookie->read('Config.language');
        if (!empty($lang)) {
            I18n::locale($lang);
        }
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
    /**
     * upload file and crop
     * @param type $data_upload
     * @param type $folder_save
     * @param type $width
     * @param type $height
     * @return string
     */
    public function upload_file_and_crop($data_upload, $folder_save, $width, $height) {
        $result = array('code'=>0,'message'=>'Error upload');
        if ($data_upload['name']) {
            $filename = $data_upload['name'];
            $file_tmp_name = $data_upload['tmp_name'];
            $image_info = getimagesize($file_tmp_name);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            $path_img = WWW_ROOT . "files" . DS . $folder_save;
            if (!is_dir($path_img)) {
                mkdir($path_img, 0777);
            }
            $extension = exif_imagetype($file_tmp_name);
            switch ($extension) {
                case IMAGETYPE_JPEG:
                    $type = 'jpg';
                    $image = imagecreatefromjpeg($file_tmp_name);
                    break;
                case IMAGETYPE_PNG:
                    $type = 'png';
                    $image = imagecreatefrompng($file_tmp_name);
                    break;
                case IMAGETYPE_GIF:
                    $type = 'gif';
                    $image = imagecreatefromgif($file_tmp_name);
                    break;
                default:
                    $type = null;
            }
            if ($type) {
                if($image_width<$width || $image_height<$height){
                    return $result = array('code'=>0,'message'=>'推奨画像サイズ（横'.$width.'px × 縦'.$height.'px)');
                }
                $newavatarName = sha1(time() . $filename) . "." . $type;
                if (is_uploaded_file($file_tmp_name)) {
                    $checkAvatarUpload = Util::cropImage($width, $height, $image, $path_img . DS . $newavatarName);
                    $result = array('code'=>1,'name_image'=>$newavatarName);
                }
            }else{
                    $result = array('code'=>0,'message'=>'Please supply a valid image.');
            }
        }
        return $result;
    }
    
    public function upload_file_api($data_upload, $folder_save,$folder_route) {
        $result = "";
        if ($data_upload['name']) {
            $filename = $data_upload['name'];
            $file_tmp_name = $data_upload['tmp_name'];
            $path_img = WWW_ROOT . "files" . DS . $folder_save;
            if (!is_dir($path_img)) {
                mkdir($path_img, 0777);
            }
            $path_route = $path_img . DS . $folder_route;
            if (!is_dir($path_route)) {
                mkdir($path_route, 0777);
            }
            $image_info = getimagesize($file_tmp_name);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            $image_size = $data_upload['size'];
            
            $extension = exif_imagetype($file_tmp_name);
            switch ($extension) {
                case IMAGETYPE_JPEG:
                    $type = 'jpg';
                    break;
                case IMAGETYPE_PNG:
                    $type = 'png';
                    break;
                case IMAGETYPE_GIF:
                    $type = 'gif';
                    break;
                default:
                    $type = null;
            }
            if ($type) {
                $filename = sha1(time() . $filename) . "." . $type;
                if (move_uploaded_file($file_tmp_name, $path_route . '/' . $filename)) {
                    $result = array('file_name'=>$filename,'width'=>$image_width,'height'=>$image_height,'size'=>$image_size);
                }
            }
        }
        return $result;
    }

    /**
     * unlink image
     * @param type $folder_name
     * @param type $name_file
     * @return boolean
     */
    public function unlink_file($folder_name, $name_file) {
        $path_file = WWW_ROOT . "files" . DS . $folder_name . DS . $name_file;
        if (file_exists($path_file)) {
            @unlink($path_file);
        }
        return true;
    }
}
