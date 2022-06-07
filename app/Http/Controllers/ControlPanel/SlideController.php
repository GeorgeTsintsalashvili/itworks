<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\NaturalNumber;

class SlideController extends Controller
{
    public function index() // display a listing of the resource
    {
       return \View::make('contents.controlPanel.slides.index') -> with([
           'slides' => \DB::table('slides') -> orderBy('orderNum') -> get()
       ]);
    }

    public function store(Request $request) // Store a newly created resource in storage
    {
      $response = [ 'uploaded' => false ];

      $fileKey = 'files';

      if($request -> hasFile($fileKey)) // if file is moved into temporary location by web server
      {
        $response['uploaded'] = true;
        $response['testPassed'] = false;

        $files = $request -> file($fileKey);
        $maxAllowedUploads = 4;
        $numOfUploadsMoved = count($files);

        try{

          if($numOfUploadsMoved <= $maxAllowedUploads)
          {
            $rules = [ 'files.*' => ['required', 'mimes:jpg,jpeg,png,bmp', 'max:3072' ] ];

            $request -> validate($rules); // throws exception

            $response['testPassed'] = true;

            $originalImagesPath = realpath('./images/slides/original');
            $resizedImagesPath = realpath('./images/slides/preview');

            foreach($files as $file)
            {
              $extension = $file -> getClientOriginalExtension();
              $fileName = md5(microtime()) . mt_rand(1, 10000) . '.' . $extension;

              $file -> move($originalImagesPath, $fileName);

              $originalImageFullName = $originalImagesPath . '/' . $fileName;
              $toResizeImageFullName = $resizedImagesPath . '/' . $fileName;

              \File::copy($originalImageFullName, $toResizeImageFullName);

              $resizedImage = \Image::make($toResizeImageFullName);

              $resizedImage -> resize(64, 64) -> save();

              \DB::table('slides') -> insert([ 'image' => $fileName ]);
            }
          }

          else throw new \Exception();
        }

        catch(\Exception $e){

          $response['testPassed'] = false;
        }
      }

      return $response;
    }

    public function updateOrder(Request $request)
    {
      $response['updated'] = false;

      $parameters = $request -> only(['orderNum', 'record-id']);

      $rules = [ 'orderNum' => ['required', new NaturalNumber ],
                 'record-id' => ['required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
         $recordQuery = \DB::table('slides') -> where('id', $parameters['record-id']);

         if($recordQuery -> count() != 0)
         {
           $recordQuery -> update([ 'orderNum' => $parameters['orderNum'] ]);

           $response['updated'] = true;
         }
      }

      return $response;
    }

    public function destroy($id) // Remove the specified resource from storage
    {
      $response['deleted'] = false;

      $recordQuery = \DB::table('slides') -> where('id', $id);

      if($recordQuery -> count() != 0)
      {
         $record = $recordQuery -> first();
         $fileName = $record -> image;

         $recordQuery -> delete();

         $slidesPath = realpath('./images/slides');

         $originalImagesPath = $slidesPath . '/original/';
         $resizedImagesPath = $slidesPath . '/preview/';

         $originalImageFullName = $originalImagesPath . $fileName;
         $resizeImageFullName = $resizedImagesPath . $fileName;

         if(file_exists($originalImageFullName) && file_exists($resizeImageFullName))
         {
           \File::delete($originalImageFullName);
           \File::delete($resizeImageFullName);

           $response['deleted'] = true;
         }
      }

      return $response;
    }
}
