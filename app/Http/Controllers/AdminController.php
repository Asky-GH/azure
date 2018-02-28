<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AdminController extends Controller
{
    public function index()
    {
        $users = $this->azure('list');
        return view('users', ['users' => $users]);
    }

    public function delete()
    {
        if (request()->email == 'admin@mail.com') {
            return view('security');
        }
        $this->azure('delete');
        return redirect('/users');
    }

    public function filter()
    {
        return view('forbidden');
    }

    public function azure($action)
    {
        $tableClient = TableRestProxy::createTableService(env('AZURE'));
        $tableName = "users";
        try    {
            switch ($action) {
                case 'list':
                    $filter = "PartitionKey eq 'azure'";
                    return $tableClient->queryEntities($tableName, $filter)->getEntities();
                
                case 'delete':
                    $blobClient = BlobRestProxy::createBlobService(env('AZURE'));
                    $user = DB::table('users')->where('email', request()->email)->first();
                    $containerName = strval($user->id) . strtolower($user->name) . "container";
                    $blobClient->deleteContainer($containerName);
                    $tableClient->deleteEntity($tableName, 'azure', $user->email);
                    DB::table('users')->where('email', request()->email)->delete();
            }
        }
        catch(ServiceException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
}
