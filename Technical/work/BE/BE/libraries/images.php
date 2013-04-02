<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* @author: Sam Bassett, July 5, 2011
* 
* inspired in part by 
*  http://www.snipe.net/2004/06/cropped-thumbnails-using-php-and-the-gd-library/
* 
* GD library documentation:
* http://php.net/manual/en/book.image.php
* 
* 
*/
class Images
{
	//images library

	/**
	* get the file extension of this image
	* 
	* @param string $ipath
	* @return string
	*/
	public function path_image_type($ipath)
	{
	  $type = strtolower(substr(strrchr($ipath,"."),1));
	  if($type == 'jpeg') $type = 'jpg';
	  return $type;
	}
	/**
	* return true or false whether this file is an image or not
	* 
	* @param mixed $ipath
	* @return bool
	*/
	public function type_is_valid_image($_files_index)
	{
		if(!isset( $_FILES[$_files_index])) return false;//if nothing there
		
		//return true if image, false otherwise
		return strstr( $_FILES[$_files_index]['type'],'image/');
	}
	
	
	
	/**
	* returns an array for the given image sizs
	* is simply a wrapper for 
	* getimagesize
	* @param mixed $_files_index
	*/
	public function get_image_size($_files_index)
	{
		return getimagesize($_FILES[$_files_index]["tmp_name"]);   
		
	}
	
	/**
	* return the name of the file from clientside
	* 
	* @param mixed $_files_index
	* @return mixed
	*/
	public function get_name($_files_index)
	{
		return $_FILES[$_files_index]['name'];
	}
	
	/**
	* gets the file extension of the file as uploaded
	* 
	* @param mixed $_files_index
	* @return mixed
	*/
	public function get_extension($_files_index)
	{
		$file_name_array = explode(".",$this->get_name($_files_index)); 
        return array_pop($file_name_array);
	}
	/**
	* return the tmp_name of the file
	* 
	* @param mixed $_files_index
	* @return mixed
	*/
	public function tmp_name($_files_index)
	{
		return $_FILES[$_files_index]['tmp_name'];
	}
	/**
	* converts image to a GD image object
	* 
	* @param mixed $ipath
	* @return resource
	*/
	public function path_to_image($ipath)  
	{
		$type = $this->path_image_type($ipath);

		switch($type)
		{
			case 'bmp': $img = imagecreatefromwbmp($ipath); break;
			case 'gif': $img = imagecreatefromgif( $ipath); break;
			case 'jpg': $img = imagecreatefromjpeg($ipath); break;//these two are
			case 'jpeg':$img = imagecreatefromjpeg($ipath); break;//the same
			case 'png': $img = imagecreatefrompng( $ipath); break;
			default : return false;
		}
		return $img;
	}
	/**
	* saves your image object to the destination path, with the given file type
	* 
	* @param string $ipath
	* @param string $dest
	* @param string $type
	* @return bool
	*/
	public function image_to_path($new,$dst,$type='png')
	{
		switch($type)
		{
			case 'bmp': imagewbmp($new, $dst); break;
			case 'gif': imagegif( $new, $dst); break;
			case 'jpg': imagejpeg($new, $dst); break;//these two are
			case 'jpeg':imagejpeg($new, $dst); break;// the same
			case 'png': imagepng( $new, $dst); break;
			default : return false;
		}
		return true;
	}
  
	/**
	* Resize the image so that the SMALLEST  of the existing x/y dimensions of the image
	* are within input limit
	* this DOES keep the aspect ratio the same
	* EXAMPLE: a 500 by 750 image, since the height is larger, would be reduced to 75 by ? , where ? is larger than 75 
	* so an easy way to create a thumbnail is to simply stretch and then crop
	* 
	* 
	* 
	* @param resource $image
	* @param int $limit
	* @return resource
	*/
	public function stretch($image,$limit=75)
	{
		//$size = getimagesize($image);
		//$orig_x=$size[0];
		//$orig_y=$size[1];

		//get current size
		$src_w = imagesx($image);
		$src_h = imagesy($image);


		$width_ratio  = ($limit / $src_w );
		$height_ratio = ($limit / $src_h);

		if($src_w > $src_h)
		{
			//is landscape == wide short rectangle

			$dst_h = $limit;
			$dst_w  = round($src_w * $height_ratio);


		}
		else if($src_w < $src_h )
		{
			//is portrait == tall skinny rectangle

			$dst_h = round($src_h * $width_ratio);
			$dst_w  = $limit;

		}
		else
		{
			// is square
			$dst_h = $limit;
			$dst_w = $limit;
		}

		//create new blank image of the correct size
		$new = imagecreatetruecolor($dst_w,$dst_h);
		//the zeroes mean default crops with centering
		imagecopyresampled($new, $image, 0 , 0 , 0, 0, $dst_w, $dst_h, $src_w, $src_h);

		//return the image resource object
		return $new;
	}

	/**
	* keeping aspect ratio the same, it shrinks the image down 
	* so that the maximum of x and y dimesnions are as given in $max
	* 
	* stretch and proportionate are opposites
	* 
	* @param resource $image
	* @param int $max
	*/
	public function proportionate($image,$limit=75)
	{
		$src_w = imagesx($image);
		$src_h = imagesy($image);

		//keep ratios the same
		$width_ratio  = ($limit / $src_w );
		$height_ratio = ($limit / $src_h);
		
		//these do nto get changed in this case
		$src_x=0;
		$src_y=0;
		$dst_x=0;
		$dst_y=0;
		
		if($src_w > $src_h)
		{
			//is landscape == wide short rectangle
			$dst_h = round($src_h * $width_ratio);
			$dst_w = $limit;

		}
		else if($src_w < $src_h )
		{
			//is portrait == tall skinny rectangle
			$dst_h = $limit;
			$dst_w = round($src_w * $height_ratio);
		}
		else
		{
			// is square
			$dst_h = $limit;
			$dst_w = $limit;
		}
		

		//create new blank image of the correct size
		$new = imagecreatetruecolor($dst_w,$dst_h);
		//the zeroes mean default crops with centering
		imagecopyresampled($new, $image, $dst_x , $dst_y , $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		//return the image resource object
		return $new;
	}
	
	/**
	* places the image on an area larger than itself
	* centered, and background filled in
	* 
	* @param mixed $image
	* @param mixed $w
	* @param mixed $h
	*/
	public function fill($image,$w=75,$h=75)
	{
		$src_w=imagesx($image);
		$src_h=imagesy($image);
		
		$dst_w=$w;
		$dst_h=$h;
		
		$dst_x=0;
		$dst_y=0;
		if($src_w>$w||$src_h>$h){return $image;}
		
		if($src_w<$w)
		{
			//example: if image given was 25 wide, its going to be centered in a 75 area, so we are adding 25 on each side
			//so result location is the interval 25,50
			$shift=round(($w-$src_w)/2);
			$dst_x+=$shift;
			
			
		}//if equal nothing neeeds to be done
		
		if($src_h<$h)
		{
			$shift=round(($h-$src_h)/2);
			$dst_y+=$shift;
			
		}//if equal nothing neeeds to be done
		
		
		$new = imagecreatetruecolor($dst_w,$dst_h);
		// sets background to white, default was black//transparent
		$white = imagecolorallocate($new, 255, 255, 255);
		imagefill($new, 0, 0, $white);                           //$dst_w, $dst_h,
		imagecopy($new, $image, $dst_x , $dst_y , 0, 0,  $src_w, $src_h);//DO NOT resample, straigtcopy we want

		//return the image resource object
		return $new;
	}
	/**
	* places the image on a new $x by $y blank image , with given background colour
	* automatically centered . image is restricted to be within the given size
	* 
	* @param resource $image
	* @param mixed $x
	* @param mixed $y
	* @return resource
	*/
	public function crop($image,$x=75,$y=75)
	{
		$orig_x=imagesx($image);
		$orig_y=imagesy($image);
		
		$src_w=$x;//height and width of crop
		$src_h=$y;
		$src_x=0;//start coordinates of crop, default is top left
		$src_y=0;
		//make sure crop is centered
		if($orig_y > $y)
		{
			//ex: so if image is 125 wide, instead of cropping to 0,75
			//we remove 25 on either end, and crpo from 25 to 100
			$removed=round(($orig_y-$y)/2);
			$src_y=$removed;
			$src_h=$orig_y-$removed;
		}
		if($orig_x > $x)
		{
			//ex: so if image is 125 tall, instead of cropping to 0,75
			//we remove 25 on top and bottom, and crpo from 25 to 100
			$removed=round(($orig_x-$x)/2);
			$src_x=$removed;
			$src_w=$orig_x-$removed;
		}
		$new = imagecreatetruecolor($x,$y);

		// sets background to white, default was black//transparent
		$white = imagecolorallocate($new, 255, 255, 255);
		imagefill($new, 0, 0, $white);
		//crop
		imagecopy($new, $image, 0 , 0 ,  $src_x,$src_y,$src_w,$src_h);

		return $new;
	}




	/**
	* NOT YET IMPLEMENTED
	* 
	* places a border on the image with given colour and width, that covers up 
	* that much of the image, but the image size stays the same
	* 
	* @param resource $image
	* @param mixed $hexBackground
	* @param mixed $width
	* @return resource
	*/
	public function matte($image,$hexBackground="#FFFFFF",$width=20)
	{
		
		return false;
	}

	/**
	* NOT YET IMPLEMENTED
	* 
	* makes the image larger by adding a frame of given colour and width 
	* this will make the image LARGER
	* and will NOT remove any parts of the image
	* 
	* @param resource $image
	* @param mixed $hexBackground
	* @param mixed $width
	* @return resource
	*/
	public function frame($image,$hexBackground="#FFFFFF",$width=20)
	{


		return false;
	}








}
?>
