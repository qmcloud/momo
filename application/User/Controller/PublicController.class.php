<?php

/**
 * 
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
class PublicController extends HomebaseController {
    
	function avatar(){
		
		$users_model=M("Users");
		$id=I("get.id",0,"intval");
		
		$find_user=$users_model->field('avatar')->where(array("id"=>$id))->find();
		
		$avatar=$find_user['avatar'];
		$should_show_default=false;
		
		if(empty($avatar)){
			$should_show_default=true;
		}else{
			if(strpos($avatar,"http")===0){
				header("Location: $avatar");exit();
			}else{
				$avatar_dir=C("UPLOADPATH")."avatar/";
				$avatar=$avatar_dir.$avatar;
				if(file_exists($avatar)){
					$imageInfo = getimagesize($avatar);
					if ($imageInfo !== false) {
						$mime=$imageInfo['mime'];
						header("Content-type: $mime");
						echo file_get_contents($avatar);
					}else{
						$should_show_default=true;
					}
				}else{
					$should_show_default=true;
				}
			}
			
			
		}
		
		if($should_show_default){
			$imageInfo = getimagesize("public/images/headicon.png");
			if ($imageInfo !== false) {
				$mime=$imageInfo['mime'];
				header("Content-type: $mime");
				echo file_get_contents("public/images/headicon.png");
			}
			
		}
		exit();
		
	}
    

    
}
