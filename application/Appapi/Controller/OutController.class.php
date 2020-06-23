<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class OutController extends HomebaseController{

    public function out(){    
        //读取库里所有的表       
        $prefix=C('DB_PREFIX');
        $database=C('DB_NAME');
        $sql="show tables";   
        $result=M()->query($sql);  

        foreach ($result as $k=>$v) {             
            $k++;                     

            $_sql="SHOW FULL COLUMNS FROM ".$v['tables_in_'.$database]; 
            $data[][0]=array("表 .".$v['tables_in_'.$database]."表",'','','','','','');
            $data[][1]=array("字段","类型","校对","NULL","键","默认","额外","权限","注释");
            $data[]=M()->query($_sql);  
            $data[][]=array();                      
        }          
           
        $filename="test_excel";              
        $this->getExcel($filename,$data);    
    }  

    private function getExcel($fileName,$data){             
    //对数据进行检验            
         if(empty($data)||!is_array($data)){                 
             die("data must be a array");             
         }             
         
         //导入PHPExcel类库         
 
        require_once (SITE_PATH."PHPExcel/PHPExcel.php");
        
        $date=date("Y_m_d",time()); 
        $fileName.="_{$date}.xls";              
        //创建PHPExcel对象，注意，不能少了\             
        $objPHPExcel=new \PHPExcel();             
        $objProps=$objPHPExcel->getProperties();  

        $column=2;             
        $objActSheet=$objPHPExcel->getActiveSheet();   
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);//设置默认高度

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('5');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('40');//设置列宽

        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
        
        foreach ($data as $ke=>$row){      

            foreach($row as $key=>$rows){

                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:J{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:J{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFont()->setBold(true);//粗体

                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');

                }else{
                    if(!empty($rows)){
                        array_unshift($rows,$key+1);
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:J{$column}");//设置边框
                    } 
                }

                if($rows['1']=='字段'){
                    $rows[0]='ID';
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }

                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getAlignment()->setWrapText(true);//换行
                 //行写入                     
                $span = ord("A");                       
                foreach($rows as $keyName=>$value){                    
                    // 列写入                       
                    $j=chr($span);                         
                    $objActSheet->setCellValue($j.$column, $value);                        
                    $span++;                     
                }                     
                $column++;                 
            }             
        } 
        $fileName = iconv("utf-8", "gb2312", $fileName);             
        //设置活动单指数到第一个表,所以Excel打开这是第一个表             
        $objPHPExcel->setActiveSheetIndex(0);             
        header('Content-Type: application/vnd.ms-excel');             
        header("Content-Disposition: attachment;filename=\"$fileName\"");             
        header('Cache-Control: max-age=0');                
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');             
        $objWriter->save('php://output'); //文件通过浏览器下载             
        exit;     
    }
}