<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Image;

/*
 * Purpose of this trait is to create new profile picture and/or
 * @todo - to replace existing ones
 * */

/*
 * Trait for creating defined images from one image by resizing (preserving aspect ratio) with defined maximum width.
 *
 * This trait doesn't validate so make sure it's called after validation!
 *
 * This logic is done so if we need new type of image, lets say, image which is slightly bigger than thumbnail, then:
 *      add array inside $imageSizesDir where:
 *          'saveInDir' => is file inside public (file must exist before running this code),
 *          'absPath' => same string as saveInDir,
 *          'maxWidth' => int - max width of resized image, if it's less than max, size won't change.
 *          *** also create file with 'saveInDir' value in /public.
 *
 * Method will create as many images as count($imageSizesDir).
 *
 * return [
 *      'dir' => '/dir/name.ext '
 *      example:
 *      "thumbnails" => "/thumbnails/1635278928.png"
 * ]
*/

trait ProfilePictureTrait {

    public function storeTrait(Request $request)
    {
        $image = $request->file('profilePicture');

        // file name should be unique, it can fail if same request is processed at same second, attempting to create 2 files with same name.
        $imgName = time().'.'.$image->extension();

        $imageSizesDir = [
            ['saveInDir' =>'/thumbnails', 'absPath' => public_path('/thumbnails'), 'maxWidth' => 250],
            ['saveInDir' =>'/images', 'absPath' => public_path('/images'), 'maxWidth' => 1024],
        ];

        $pathsOfImages = [];

        foreach($imageSizesDir as $imgType){
            $img = Image::make($image->path());
            $imgWidth = $img->width();
            $this->resizeAndSave($imgType['absPath'], $img, $imgName, $imgWidth, $imgType['maxWidth']);

            // example:
            // $pathsOfImages [ "thumbnails" ] = "/thumbnails/1635278928.png" ;
            $pathsOfImages[ ltrim( $imgType['saveInDir'], '/') ] = $imgType['saveInDir'] . "/" . $imgName;
        }

        return $pathsOfImages;
    }

    // can props be protected and private inside traits?
    public function resizeAndSave($saveTo, $img, $imgName, $imgWidth, $targetWidth)
    {
        // need try catch here
        $img->resize( $this->getMaxWidth($imgWidth, $targetWidth) , null, function ($const) {
            $const->aspectRatio();
        })->save($saveTo . '/' . $imgName);
    }

    public function getMaxWidth($imgWidth, $targetWidth)
    {
        if($imgWidth >= $targetWidth){
            return $targetWidth;
        }
        return $imgWidth;
    }

    /*
     * @todo Regarding profile update, what to do with old profile picture after user has chosen to update
     * delete pic, move it into their album or what?
     * */
    public function updateImages()
    {

    }

}
