<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blobs = $this->azure('list');
        return view('home', ['blobs' => $blobs]);
    }

    public function download()
    {
        $this->azure('download');
        return redirect('/home');
    }

    public function upload()
    {
        $uploadError = $this->azure('upload');        
        if ($uploadError == "Выберите файл для загрузки!") {
            $blobs = $this->azure('list');
            return view('home', ['uploadError' => $uploadError, 'blobs' => $blobs]);
        }
        return redirect('/home');
    }

    public function delete()
    {
        $this->azure('delete');
        return redirect('/home');
    }

    public function azure($action)
    {
        $blobClient = BlobRestProxy::createBlobService(env('AZURE'));
        $containerName = strval(auth()->user()->id) . strtolower(auth()->user()->name) . "container";
        try    {
            switch ($action) {
                case 'list':
                    $containers = $blobClient->listContainers()->getContainers();
                    $containerExists = false;
                    foreach ($containers as $container) {
                        if ($container->getName() == $containerName) {
                            $containerExists = true;
                        }
                    }
                    if (!$containerExists) {
                        $blobClient->createContainer($containerName);
                    }        
                    return $blobClient->listBlobs($containerName)->getBlobs();

                case 'download':
                    $blob = $blobClient->getBlob($containerName, request()->name);
                    $file = fopen("c:\\Users\\" . get_current_user() . "\\Downloads\\" . request()->name, "w");
                    fwrite($file, stream_get_contents($blob->getContentStream()));
                    break;

                case 'upload':
                    if ($_FILES['fileToUpload']['size'] == 0 && $_FILES['fileToUpload']['error'] == 4)
                    {
                        return "Выберите файл для загрузки!";
                    }
                    $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
                    $blob_name = basename($_FILES["fileToUpload"]["name"]);
                    $blobClient->createBlockBlob($containerName, $blob_name, $content);
                    break;
                
                case 'delete':
                    $blobClient->deleteBlob($containerName, request()->name);
                    break;
            }
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
}
