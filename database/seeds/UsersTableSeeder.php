<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

use MicrosoftAzure\Storage\Table\TableRestProxy;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\EdmType;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entity = new Entity();
        $entity->setPartitionKey("azure");
        $entity->setRowKey('admin@mail.com');
        $entity->addProperty("Name", EdmType::STRING, 'Admin');
        $entity->addProperty("Email", EdmType::STRING, 'admin@mail.com');
        $entity->addProperty("Password", EdmType::STRING, bcrypt('admin'));

        $tableClient = TableRestProxy::createTableService(env('AZURE'));
        $tableName = "users";
        try    {
            $tableClient->createTable($tableName);
            $tableClient->insertEntity($tableName, $entity);
        }
        catch(ServiceException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
        }

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('admin'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'remember_token' => str_random(10),
        ]);
    }
}
