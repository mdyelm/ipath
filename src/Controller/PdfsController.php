<?php

namespace App\Controller;

use App\Controller\UserBaseController;

class PdfsController extends UserBaseController {

    public $components = array('RequestHandler');

    public function download($id) {
        try {
            //run view Pdfs/pdf/download.ctp and layout pdf/default
            $id_array = $this->request->query['id_array'];
            if ($id) {
                $this->loadModel('Images');
                $this->loadModel('Routes');
                $route = $this->Routes->find('all')
                        ->hydrate(false)
                        ->join([
                            [
                                'table' => 'm_devices',
                                'type' => 'INNER',
                                'alias' => 'Devices',
                                'conditions' =>
                                [
                                    'Routes.device_id = Devices.id',
                                ],
                            ],
                            'Locations' => [
                                'table' => 'm_locations',
                                'type' => 'LEFT OUTER',
                                'alias' => 'Locations',
                                'conditions' =>
                                [
                                    'Locations.route_id = Routes.id',
                                    'Locations.id = (SELECT MAX(id) FROM m_locations WHERE route_id = Routes.id)'
                                ],
                            ],
                            'LocationsMin' => [
                                'table' => 'm_locations',
                                'type' => 'LEFT OUTER',
                                'alias' => 'LocationsMin',
                                'conditions' =>
                                [
                                    'LocationsMin.route_id = Routes.id',
                                ],
                            ],
                        ])
                        ->where(['Routes.id' => $id])
                        ->select(['Routes.created', 'LocationsMin.id', 'LocationsMin.longitude', 'LocationsMin.latitude', 'Locations.id', 'Locations.longitude', 'Locations.latitude', 'Routes.id', 'Routes.time_start', 'Routes.time_end', 'Routes.country', 'Devices.name'])
                        ->distinct('Routes.id')
                        ->first();
                if (!empty($route)) {
                    $conditions = ['Images.route_id' => $id, 'Locations.type' => 2, 'Locations.latitude IS NOT' => null];
                    if (!empty($id_array)) {
                        $conditions['Images.id in'] = $id_array;
                    }
                    $images = $this->Images->find('all')
                            ->hydrate(false)
                            ->join([
                                'locations' => [
                                    'table' => 'm_locations',
                                    'type' => 'LEFT',
                                    'alias' => 'Locations',
                                    'conditions' =>
                                    [
                                        'Locations.image_id = Images.id',
                                    ],
                                ],
                            ])
                            ->where($conditions)
                            ->select(['Locations.id', 'Locations.longitude', 'Locations.latitude', 'Images.rotation', 'Images.name', 'Images.width', 'Images.height', 'Images.size', 'Images.created', 'Locations.catch_time', 'Images.comment'])
                            ->order(['Locations.id' => 'ASC']);
                    $cnt = 0;
                    foreach ($images as $key => $value) {
                        if ($value) {
                            $cnt++;
                        }
                    }
                    $route['cnt'] = $cnt;
                    // download
                    $dateTimeTitle = new \DateTime($route['time_start']);
                    $DateTitle = $dateTimeTitle->format('Y/m/d');
                    //$SURVEY = $route['id'];
                    $SURVEY = "SURVEY";
                    $titlePdf = $DateTitle . "_" . $route['country'] . "_".$SURVEY;
                    $dateNow = new \DateTime();
                    $dateFormat = $dateNow->format('Ymd');
                    $this->set(['route' => $route, 'images' => $images, 'titlePdf' => $titlePdf]);

                    $this->viewBuilder()->options([
                        'pdfConfig' => [
                            'orientation' => 'portrait',
                            'filename' => $dateFormat . 'Survey.pdf', // file name download
                            'download' => true // auto download 
                        ]
                    ]);
                }
            } else {
                $this->Flash->error(__('Error'));
                $this->redirect($this->referer());
            }
        } catch (Exception $ex) {
            $this->Flash->error(__('Error'));
            $this->redirect($this->referer());
        }
    }

    public function createFile() {
        //run view pdf/create_file and layout pdf/default
        $CakePdf = new \CakePdf\Pdf\CakePdf();
        $CakePdf->template('create_file', 'default');
        $CakePdf->viewVars(['dataPdf' => 1]);
        // Get the PDF string returned
        $pdf = $CakePdf->output();
        // Or write it to file directly
        $pdf = $CakePdf->write(WWW_ROOT . 'files' . DS . 'pdf' . DS . 'messi.pdf');
        echo "create success file: " . WWW_ROOT . 'files' . DS . 'pdf' . DS . 'messi.pdf';
        die;
    }

}
