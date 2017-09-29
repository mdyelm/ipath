<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

/**
 * @author Khoant
 */
class BriteExcel {

    /**
     * Main var for save or read file excel
     * @var type php excel
     */
    private $_objPHPExcel;
    
    /**
     * Store imformation route
     * @var type object
     */
    private $_route_rd;
    
    /**
     * Array of object image infomation for save excel file
     * @var type array
     */
    private $_images;
    
    /**
     * Store infomation author, date of excel file
     * @var type array
     */
    private $_infos;
    
    /**
     * Style of cell excel, can set from out of class
     * @var type array
     */
    private $_styleArray;
    
    /**
     * Width cell (not use)
     * @var type float
     */
    private $_widthCell;
    
    /**
     * Width cell = 4 (by different between size at code and size at excel)
     * @var type float
     */
    private $_widthCell4;
    
    /**
     * Width cell = 9 (by different between size at code and size at excel)
     * @var type float
     */
    private $_widthCell9;
    
    /**
     * Height cell(not use)
     * @var type float
     */    
    private $_heightCell;
    
    /**
     * Height cell = 25 (by different between size at code and size at excel)
     * @var type float
     */    
    private $_heightCell25;
    
    /**
     * Height cell = 3 (by different between size at code and size at excel)
     * @var type float
     */      
    private $_heightCell3;
    
    /**
     * Height cell = 1575 (by different between size at code and size at excel)
     * @var type float
     */      
    private $_heightCell1575;
    
    /**
     * Height cell = 60 (by different between size at code and size at excel)
     * @var type float
     */      
    private $_heightCell60;
    
    /**
     * Height cell default for calculate set position image
     * @var type float
     */      
    private $_default_height;
    
    /**
     * Height block for calculate set position image
     * @var type float
     */    
    private $_block_height;
    
    /**
     * Index of A at ASCII(65)
     * @var type integer
     */
    private $_left_index;
    
    /**
     * Height cell collection (not use)
     * @var type array
     */
    private $_heightCells;
    
    /**
     * Style inner content at excel file
     * @var type array
     */
    private $_styleInnerAll;
    
    /**
     * Style outer content at excel file
     * @var type array
     */
    private $_styleOuterAll;
    
    /**
     * Style custom for set content image
     * @var type array
     */
    private $_styleCustom;
    
    /**
     * Style border color cell at outer image
     * @var type array
     */
    private $_styleBorderColorCell;
    
    /**
     * Style background outer image
     * @var type array
     */
    private $_styleImageBackgroundColorCell;
    
    /**
     *
     * @var type 
     */
    private $_textTitlePage;
    
    /**
     * Scale at file for increase or decrease width cell
     * @var type 
     */
    private $_scaleWidth;
    /**
     * Scale at file for increase or decrease height cell
     * @var type 
     */
    private $_scaleHeight;

    /**
     * Initialize
     * @param type $route
     * @param type $images
     */
    public function __construct($infos = array(), $route_rd = array(), $images = array(), $title = '') {
        $this->_objPHPExcel = new PHPExcel();
        $this->_route_rd = $route_rd;
        $this->_images = $images;
        $this->setInfo($infos);
        $this->setStyleArray();
        $this->_scaleWidth = .56;
        $this->_scaleHeight = .74;
        $this->_heightCell = 33 * $this->_scaleHeight;
        $this->_widthCell = 68.5 * $this->_scaleWidth;
        $this->_default_height = 10 * $this->_scaleHeight;
        $this->_block_height = 15 * $this->_scaleHeight;
        $this->_left_index = 65;

        $this->_widthCell4 = 4.7 * $this->_scaleWidth;
        $this->_widthCell9 = 9.7 * $this->_scaleWidth;
        $this->_heightCell25 = 25 * $this->_scaleHeight;
        $this->_heightCell3 = 3 * $this->_scaleHeight;
        $this->_heightCell1575 = 15.75 * $this->_scaleHeight;
        $this->_heightCell60 = 60 * $this->_scaleHeight;
        $this->_styleBorderColorCell = 'c4bd97';
        $this->_styleImageBackgroundColorCell = 'f2f2f2';
        $this->_textTitlePage = $title;

        $this->setStyleInnerAll();
        
        $this->setStyleOuterAll();
        
        $this->setStyleCustom();        
    }

    public function setRoute($route = array()) {
        $this->_route_rd = $route;
    }

    public function setInfo($infos = array()) {
        if ($infos) {
            $this->_infos = $infos;
        } else {
            $this->_infos = array(
                'creator' => 'nhoccon',
                'modified' => 'nhoccon',
                'title' => 'ipath route',
                'subject' => 'ipath route content',
                'description' => 'ipath description',
                'keyword' => 'ipath route',
                'category' => 'path route map'
            );
        }
    }

    public function setImages($images = array()) {
        $this->_images = $images;
    }

    public function setStyleArray($styleArray = array()) {
        if ($styleArray) { 
            $this->_styleArray = $styleArray;
        } 
        else {
            $this->_styleArray = array( 
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 12 * $this->_scaleWidth,
                    'name' => 'Times new Roman'
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => 'f9f9f9')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE,
//                        'color' => array('rgb' => 'dddddd')
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            );
        }
    }

    public function setHeightCell($heightCell = 23) {
        $this->_heightCell = $heightCell;
    }

    public function setWidthCell($widthCell = 68) {
        $this->_widthCell = $widthCell;
    }

    public function setDefaultHeight($defaultHeight) {
        $this->_default_height = $defaultHeight;
    }

    public function setBlockHeight($blockHeight) {
        $this->_block_height = $blockHeight;
    }

    public function setLeftIndex($leftIndex) {
        $this->_left_index = $leftIndex;
    }
    
    public function setStyleInnerAll($styleInnerAll = array()){
        if (empty($styleInnerAll)) {
            $this->_styleInnerAll = array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => '000000'),
                    'size' => 12 * $this->_scaleWidth,
                    'name' => 'Times new Roman'
                ),                
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ffffff')
                ),             
                'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_NONE
                  ),
//                  'outline' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THICK,
//                    'color' => array('rgb' => '0000FF'),
//                  ),
                )            
            );            
        }
        else {
            $this->_styleInnerAll = $styleInnerAll;
        }
    }
    
    public function setStyleOuterAll($styleOuterAll = array()) {
        if (empty($styleOuterAll)) {
            $this->_styleOuterAll = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'a0a0a0')
                ),            
            );            
        }
        else {
            $this->_styleOuterAll = $styleOuterAll;
        }
    }
    
    /**
     * Get style custom title of page
     * @return type
     */
    protected function _getStyleCustomTitle(){
        return array(
                    'font' => array(
                        'bold' => true,
                        'size' => 20 * $this->_scaleWidth,
                    ),
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                            'color' => array('rgb' => $this->_styleBorderColorCell)
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                    )                    
                );
    }

    /**
     * Get style custom number of page
     * @return type
     */
    protected function _getStyleCustomNumberPage(){
        return array(
                    'font' => array(
                        'bold' => true,
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                    )                    
                );
    }
    
    /**
     * Get border image in page
     */
    protected function _getImageBorder() {
        return array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
            ),            
        );
    }
    
    /**
     * Get border right image block in page
     */
    protected function _getImageBorderRight() {
        return array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => $this->_styleBorderColorCell)
                ),
            ),            
        );
    }
    
    protected function _getImageBackground() {
        return array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => $this->_styleImageBackgroundColorCell)
                ),            
        );
    }
    /**
     * Get left content style image block
     */
    protected function _getLeftContentImageBlock() {
        return array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array('rgb' => $this->_styleBorderColorCell)
                      )
                    )            
        );
    }
    
    /**
     * Get right content style image block
     */
    protected function _getRightContentImageBlock() {
        return array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array('rgb' => $this->_styleBorderColorCell)
                      )
                    )            
        );
    }

    public function setStyleCustom($styleCustom = array()) {
        if (empty($styleCustom)) {
            $this->_styleCustom = array(
                'title' => $this->_getStyleCustomTitle(),
                'numberPage' => $this->_getStyleCustomNumberPage(),
                'imageBorder' => $this->_getImageBorder(),
                'imageBorderRight' => $this->_getImageBorderRight(),
                'imageBackground' => $this->_getImageBackground(),
                'leftContentImageBlock' => $this->_getLeftContentImageBlock(),
                'rightContentImageBlock' => $this->_getRightContentImageBlock(),
            );
        }
        else {
            $this->_styleCustom = $styleCustom;
        }
    }
    /**
     * Set default style file
     */
    protected function _initializeDefaultStyle() {
        $this->_objPHPExcel->getDefaultStyle()->applyFromArray($this->_styleOuterAll);
    }


    /**
     * set column size width
     */
    protected function _initializeColumnSizeWidth(){
        // fix width A, H, I , J with width = 4
        $this->_objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth($this->_widthCell4);
        $this->_objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth($this->_widthCell4);
        $this->_objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth($this->_widthCell4);
        $this->_objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth($this->_widthCell4);
        $this->_objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth($this->_widthCell4);
        
        // fix width = 9
        for($i = 1; $i <= 6; $i++) {
            $this->_objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $i))->setWidth($this->_widthCell9);
            $this->_objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $i + 9))->setWidth($this->_widthCell9);
        }
    }

    /**
     * Set column size single page
     */
    protected function _initializeSinglePageSizeHeight($index) {
        
        $this->_objPHPExcel->getActiveSheet()->getRowDimension($index * 50+1)->setRowHeight($this->_heightCell25);
        $this->_objPHPExcel->getActiveSheet()->getRowDimension($index * 50+2)->setRowHeight($this->_heightCell25);
        $this->_objPHPExcel->getActiveSheet()->getRowDimension($index * 50+3)->setRowHeight($this->_heightCell25);
        
        // set height for block image
        for ($i = 0; $i < 3; $i++) {
            $headLineImageBlock = $index * 50 + $i * 16;
            // line narrow 4, 12
            $this->_objPHPExcel->getActiveSheet()->getRowDimension($headLineImageBlock + 4)->setRowHeight($this->_heightCell3);
            $this->_objPHPExcel->getActiveSheet()->getRowDimension($headLineImageBlock + 12)->setRowHeight($this->_heightCell3);
            
            // line image
            for ($j = 5; $j <= 11; $j++) {
                $this->_objPHPExcel->getActiveSheet()->getRowDimension($headLineImageBlock + $j)->setRowHeight($this->_heightCell25);
            }
            
            // line info image
            for ($j = 13; $j <= 19; $j++) {
                if ($j != 17) {
                    $this->_objPHPExcel->getActiveSheet()->getRowDimension($headLineImageBlock + $j)->setRowHeight($this->_heightCell1575);
                }
            }
            
            $this->_objPHPExcel->getActiveSheet()->getRowDimension($headLineImageBlock + 17)->setRowHeight($this->_heightCell60);
        }
    }
    
    /**
     * Set content of block image
     * @param type $positionRow
     * @param type $positionColumn
     */
    protected function _setContentBlockImage($positionRow, $positionColumn) {
        
        // set style cell
        // left block
        $cellLeft = chr($positionColumn+1) . ($positionRow + 14) . ':' . chr($positionColumn + 2) . ($positionRow + 17);
        $this->_objPHPExcel->getActiveSheet()->getStyle($cellLeft)->applyFromArray($this->_styleCustom['leftContentImageBlock']);
        // right block
        $cellRight = chr($positionColumn+3) . ($positionRow + 14) . ':' . chr($positionColumn + 6) . ($positionRow + 17);
        $this->_objPHPExcel->getActiveSheet()->getStyle($cellRight)->applyFromArray($this->_styleCustom['rightContentImageBlock']);
        
        //merge cell
        for ($i = 0; $i < 4; $i++) {
            // left
            $cellLeft = chr($positionColumn+1) . ($positionRow + 14 + $i) . ':' . chr($positionColumn + 2) . ($positionRow + 14 + $i);
            $this->_objPHPExcel->getActiveSheet()->mergeCells($cellLeft);
            
            // check if column not coordinate
            if ($i != 1) {
                $cellRight = chr($positionColumn+3) . ($positionRow + 14 + $i) . ':' . chr($positionColumn + 6) . ($positionRow + 14 + $i);
                $this->_objPHPExcel->getActiveSheet()->mergeCells($cellRight);
            }
            // column coordinate
            else {
                $cellRight = chr($positionColumn+3) . ($positionRow + 14 + $i) . ':' . chr($positionColumn + 4) . ($positionRow + 14 + $i);
                $this->_objPHPExcel->getActiveSheet()->mergeCells($cellRight);
                $cellRight = chr($positionColumn+5) . ($positionRow + 14 + $i) . ':' . chr($positionColumn + 6) . ($positionRow + 14 + $i);
                $this->_objPHPExcel->getActiveSheet()->mergeCells($cellRight);
            }
        }
        
    }


    /**
     * Set style image block
     * @param type $positionRow
     * @param type $positionColumn
     */
    protected function _setStyleImageBlock($positionRow, $positionColumn, $position='left') {
        
        // set border image 
        $cellImageBorder = chr($positionColumn) . ($positionRow + 4) . ':' . chr($positionColumn + 7) . ($positionRow + 12);
        if ($position == 'left') {
            $imageBorder = 'imageBorder';
        }
        else {
            $imageBorder = 'imageBorderRight';
        }        
        $this->_objPHPExcel->getActiveSheet()->getStyle($cellImageBorder)->applyFromArray($this->_styleCustom[$imageBorder]);
        
        // set background image
//        $cellImageBackground = chr($positionColumn+1) . ($positionRow + 5) . ':' . chr($positionColumn + 6) . ($positionRow + 11);
//        $this->_objPHPExcel->getActiveSheet()->getStyle($cellImageBackground)->applyFromArray($this->_styleCustom['imageBackground']);
        
        // set content of block image
        $this->_setContentBlockImage($positionRow, $positionColumn);
    }


    /**
     * Merge cell, set style cell, set border cell, fill data default
     * @param type $i
     */
    protected function _mergeCellAndSetStyle($index, $page) {
        // title
        $cellTitle = 'D' . ($index * 50 + 1) . ':N' .($index * 50 + 1);
        $this->_objPHPExcel->getActiveSheet()->mergeCells($cellTitle);
        $this->_objPHPExcel->getActiveSheet()->getStyle($cellTitle)->applyFromArray($this->_styleCustom['title']);
        $this->_objPHPExcel->getActiveSheet()->setCellValue('D' . ($index * 50 + 1), $this->_textTitlePage);
        
        // page
        $cellPage = 'P' . ($index * 50 + 1) . ':Q' .($index * 50 + 1);
        $this->_objPHPExcel->getActiveSheet()->mergeCells($cellPage);
        $this->_objPHPExcel->getActiveSheet()->getStyle($cellPage)->applyFromArray($this->_styleCustom['numberPage']);
        $textPage = sprintf('NO.%02d/%02d', $index +1, $page);
        $this->_objPHPExcel->getActiveSheet()->setCellValue('P' . ($index * 50 + 1), $textPage);
        
        for ($i = 0; $i < 3; $i++) {
            $headLineImageBlock = $index * 50 + $i * 16;
            $this->_setStyleImageBlock($headLineImageBlock, $this->_left_index);
            $this->_setStyleImageBlock($headLineImageBlock, $this->_left_index + 9, 'right');
        }
    }


    /**
     * Determine position image at cell bottom
     * @param type $position
     * @param type $index
     */
    protected function _setImageCell($position, $index, $img) {

        $signature = __DIR__ . "/../../webroot/files/image/" . $this->_route_rd['id'] . "/" . $img['name'];    //Path to signature .jpg file
        // check exist file, if have then to do
        if (is_file($signature)) {
            $widthImage = $img['width'];
            $heightImage = $img['height'];
            // $widthImage = 140;
            // $heightImage = 151;    
            $widthBound = $this->_widthCell * 6 + 13.7 * $this->_scaleWidth * 2;

            $heightBound = $this->_heightCell * 7 + .6;

            $currentSize = $this->_calculateSize($widthImage, $heightImage, $widthBound, $heightBound);
            $objDrawing = new PHPExcel_Worksheet_Drawing();    //create object for Worksheet drawing
            $objDrawing->setName('Khoa nt draw');        //set name to image
            $objDrawing->setDescription('Nhoc con draw'); //set description to image
            $objDrawing->setPath($signature);
            $objDrawing->setWidth($currentSize[0]);                 //set width, height
            $objDrawing->setHeight($currentSize[1]);

            // calculate cell position by width height
            $cellPosition = $this->_calculateCellPosition($currentSize, $widthBound, $heightBound, $position, $index-1);

            $objDrawing->setCoordinates($cellPosition[0]);        //set image to cell
            
            $objDrawing->setOffsetX($cellPosition[1]);                       //setOffsetX works properly
            $objDrawing->setOffsetY($cellPosition[2]);                       //setOffsetY works properly    
            $objDrawing->setResizeProportional(FALSE);
            $objDrawing->setWorksheet($this->_objPHPExcel->getActiveSheet());
        }
    }
    
    /**
     * Convert degrees.decimal° to degrees°minute’seconds"
     * @param type $decimal
     * @return string
     */
    protected function _convertDecimalToSexagesimal($decimal) {
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
        $return .= floor($decimal) . "' , ";
        $decimal -= floor($decimal);
        $decimal *= 60;
        $return .= number_format($decimal, 3) . "''";
        
        return $return;
    }    
    
    /**
     * Change date if 
     * @param type $img
     * @return type
     */
    protected function _changeDate($img){
        
        if ($img['Locations']['catch_time'] != '') { 
            $time = str_replace('-', '/', $img['Locations']['catch_time']);
        }
        else {
            $time = $img['created']->i18nFormat('yyyy/MM/dd HH:mm:ss');
        }        
        return $time;
    }
    
    /**
     * Fill content data image block
     * @param type $positionRow
     * @param type $positionColumn
     * @param type $img
     */
    protected function _fillContentDataImageBlock($positionRow, $positionColumn, $img) {
        
        // static value
        $this->_objPHPExcel->getActiveSheet()
                ->setCellValue(chr($positionColumn+1) . ($positionRow + 14), __('Date / Time'))
                ->setCellValue(chr($positionColumn+1) . ($positionRow + 15), __('LAT / LON'))
                ->setCellValue(chr($positionColumn+1) . ($positionRow + 16), __('DIRECTION'))
                ->setCellValue(chr($positionColumn+1) . ($positionRow + 17), __('COMMENT'));

        // dynamic value
        $this->_objPHPExcel->getActiveSheet()
                ->setCellValue(chr($positionColumn+3) . ($positionRow + 14), $this->_changeDate($img))
                ->setCellValue(chr($positionColumn+3) . ($positionRow + 15), $this->_convertDecimalToSexagesimal($img['Locations']['latitude']))
                ->setCellValue(chr($positionColumn+5) . ($positionRow + 15), $this->_convertDecimalToSexagesimal($img['Locations']['longitude']))
                ->setCellValue(chr($positionColumn+3) . ($positionRow + 16), $this->_changeDirectionByRotation($img['rotation']))
                ->setCellValue(chr($positionColumn+3) . ($positionRow + 17), $img['comment']);                
    }


    /**
     * Fill data image block by index of _images
     * @param type $positionRow
     * @param type $positionColumn
     * @param type $index
     */
    protected function _fillDataImageBlock($positionRow, $positionColumn, $index) {
        // if exist image for fill
        if (isset($this->_images[$index])) {
                $img = $this->_images[$index];
                $this->_setImageCell($positionRow + 5, $positionColumn + 1, $img);
                $this->_fillContentDataImageBlock($positionRow, $positionColumn, $img);
        }
    }


    /**
     * Fill content to block
     * @param type $index
     */
    protected function _fillContentImage($index) {
        
        for ($i = 0; $i < 3; $i++) {
            $headLineImageBlock = $index * 50 + $i * 16;
            $this->_fillDataImageBlock($headLineImageBlock, $this->_left_index, $index * 6 + $i * 2);
            $this->_fillDataImageBlock($headLineImageBlock, $this->_left_index + 9, $index * 6 + $i * 2 + 1);
        }        
    }
    
    protected function _setPrintPageSetup($page) {
        // set print area
        $this->_objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:Q' . $page * 50); 
        
        // set break page row
        for ($i = 0; $i < $page; $i++) {
            $this->_objPHPExcel->getActiveSheet()->setBreak( 'R' . (($i + 1)*50) , PHPExcel_Worksheet::BREAK_ROW );
        }
        
        // set break page column
        $this->_objPHPExcel->getActiveSheet()->setBreak( 'R10' , PHPExcel_Worksheet::BREAK_COLUMN );
        
        // set page A4
        $this->_objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $this->_objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.5);
        $this->_objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.5);
        $this->_objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(1.15);
        $this->_objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.4);
        
//        $this->_objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
    }

    /**
     * set column size width
     */
    protected function _initializeRowSizeHeight(){
        // count page
        $totalImage = count($this->_images);
        $page = ceil($totalImage/6);
        
        // draw border 
        $this->_objPHPExcel->getActiveSheet()->getStyle('A1:Q' . $page*50)->applyFromArray($this->_styleInnerAll);        
        
        $this->_setPrintPageSetup($page);
        
        // set height single page
        for ($i = 0; $i < $page; $i++) {
            $this->_initializeSinglePageSizeHeight($i);
            $this->_mergeCellAndSetStyle($i, $page);
            $this->_fillContentImage($i);
        }
        
    }
    
    
    /**
     * initialize excel file
     */
    protected function _initialize() {

        $this->_objPHPExcel->getProperties()->setCreator($this->_infos['creator'])
                ->setLastModifiedBy($this->_infos['modified'])
                ->setTitle($this->_infos['title'])
                ->setSubject($this->_infos['subject'])
                ->setDescription($this->_infos['description'])
                ->setKeywords($this->_infos['keyword'])
                ->setCategory($this->_infos['category']);
        
        $this->_initializeDefaultStyle();        

        $this->_initializeColumnSizeWidth();
        
        $this->_initializeRowSizeHeight();        
        
    }

    protected function _changeDirectionByRotation($rotation1) {
        $rotation = intval($rotation1) % 360;
        //North
        if (($rotation >= 0 && $rotation <= 45) || ($rotation > 315 && $rotation <= 360)) {
            return "North";
        }
        //East
        else if ($rotation > 45 && $rotation <= 135) {
            return "East";
        }
        //South
        else if ($rotation > 135 && $rotation <= 225) {
            return "South";
        }
        //West
        else if ($rotation > 225 && $rotation <= 315) {
            return "West";
        } else {
            return "Unavailable";
        }
    }

    /**
     * Calculate size image
     * @param type $widthImage
     * @param type $heightImage
     * @param type $widthBound
     * @param type $heightBound
     * @return type
     */
    protected function _calculateSize($widthImage, $heightImage, $widthBound, $heightBound) {

        if ($widthImage <= 0 || $heightImage <= 0 || $widthBound <= 0 || $heightBound <= 0) {
            return array($widthBound, $heightBound);
        }
        $width = $widthBound;
        $height = $heightBound;

        if ($widthImage / $heightImage >= $widthBound / $heightBound) {
            $height = intval($widthBound * $heightImage / $widthImage);
        } else {
            $width = intval($heightBound * $widthImage / $heightImage);
            // fix for print
            $height -= 5;
        }
        return array($width, $height);
    }

    /**
     * Calculate image top left at cell position
     * @param type $currentSize
     * @param type $widthBound
     * @param type $heightBound
     * @param type $position
     * @param type $index
     * @return string
     */
    protected function _calculateCellPosition($currentSize, $widthBound, $heightBound, $position, $index) {
        $return = array(chr($index) . $position, 0, 0);
        // if full size width
        $heightCell = $this->_heightCell;
        $widthCell = $this->_widthCell;
        $widthDiff = $heightDiff = 0;
        if ($currentSize[0] == $widthBound) {
            // index not change, position change
            $heightDiff = ($heightBound - $currentSize[1]) / 2;
            $step = floor($heightDiff / $heightCell);
            $return[2] = round($heightDiff % $heightCell);
            $return[0] = chr($index) . ($position + $step);
            // fix for excel
            $return[1] = 18;
        } else {
            // index not change, position change
            $widthDiff = ($widthBound - $currentSize[0]) / 2;
            $step = floor($widthDiff / $widthCell);
            $return[0] = chr($index + $step + 1) . $position;
            // fix for excel
            $return[1] = round($widthDiff % $widthCell) + 3;
        }

        return $return;
    }

    protected function _save() {

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="' . date('Ymd') . rand(0, 10000) . '_SURVEYDOC.xlsx"');
        header('Content-Disposition: attachment;filename="' . date('Ymd') . '_SURVEYDOC.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0       
                     
        $objWriter = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
        die();
    }

    /**
     * Create file excel
     */
    public function createExcelFile() {

        $this->_initialize();

        $this->_save();
    }

}
