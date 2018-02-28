<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use MicrosoftAzure\Storage\Table\TableRestProxy;

class AdminController extends Controller
{
    public function get()
    {
        $tableClient = TableRestProxy::createTableService(env('AZURE'));

        $filter = "PartitionKey eq 'azure'";

        try    {
            $result = $tableClient->queryEntities("users", $filter);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179438.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }

        $users = $result->getEntities();

        // foreach($entities as $entity){
        //     echo $entity->getPartitionKey().":".$entity->getRowKey()."<br />";
        // }

        return view('users', ['users' => $users]);
    }
}
