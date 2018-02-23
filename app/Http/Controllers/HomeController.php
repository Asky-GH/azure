<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

// require_once 'vendor/autoload.php';

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
        // Create blob REST proxy.
        $blobClient = BlobRestProxy::createBlobService(env('AZURE'));

        try    {
            $listContainersResult = $blobClient->listContainers();
            $containers = $listContainersResult->getContainers();
            $containerExists = false;
            $containerName = strval(Auth::id()) . strtolower(Auth::user()->name) . "container";
            foreach ($containers as $container) {
                if ($container->getName() == $containerName) {
                    $containerExists = true;
                }
            }
            if (!$containerExists) {
                $blobClient->createContainer($containerName);
            }

            // List blobs.
            $blob_list = $blobClient->listBlobs($containerName);
            $blobs = $blob_list->getBlobs();

            return view('home', ['blobs' => $blobs]);

            // foreach($blobs as $blob)
            // {
            //     echo $blob->getName().": ".$blob->getUrl()."<br />";
            // }
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

    public function download(Request $request)
    {
        // Create blob REST proxy.
        $blobClient = BlobRestProxy::createBlobService(env('AZURE'));

        try    {
            // Get blob.
            $blob = $blobClient->getBlob($containerName, $request->name);
            //fpassthru($blob->getContentStream());

            $containerName = strval(Auth::id()) . strtolower(Auth::user()->name) . "container";
            $file = fopen("c:\\Users\\" . get_current_user() . "\\Downloads\\" . $request->name, "x");
            fwrite($file, stream_get_contents($blob->getContentStream()));
            return redirect('/home');
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

    public function upload()
    {
        // Create blob REST proxy.
        $blobClient = BlobRestProxy::createBlobService(env('AZURE'));

        $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
        $blob_name = basename($_FILES["fileToUpload"]["name"]);

        try    {
            $containerName = strval(Auth::id()) . strtolower(Auth::user()->name) . "container";
            //Upload blob
            $blobClient->createBlockBlob($containerName, $blob_name, $content);
            return redirect('/home');
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

    public function delete(Request $request)
    {
        // Create blob REST proxy.
        $blobClient = BlobRestProxy::createBlobService(env('AZURE'));

        try    {
            $containerName = strval(Auth::id()) . strtolower(Auth::user()->name) . "container";
            // Delete blob.
            $blobClient->deleteBlob($containerName, $request->name);            
            return redirect('/home');
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
