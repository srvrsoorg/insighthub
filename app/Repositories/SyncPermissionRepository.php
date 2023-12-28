<?php

namespace App\Repositories;

use App\Interfaces\SyncPermissionInterface;
use App\Http\Helper;
use App\Models\{Server, Application, UserServer};

class SyncPermissionRepository implements SyncPermissionInterface 
{
    public function syncAll() 
    {
        try {

            $response = Helper::serveravatarClient("monitoring-panel/servers", 'get');

            if (isset($response['error'])) {
                // ❌ Error response: Handle and respond to any external errors
                throw new \Exception($response['message']);
            }

            $serverId = [];
            foreach ($response['servers'] as $saServer) {
                $serverId[] = $saServer['id'];
                // Update or create a server based on external data
                $server = Server::updateOrCreate(['sa_server_id' => $saServer['id']], [
                    'sa_organization_id' => $saServer['organization_id'],
                    'ip' => $saServer['ip'],
                    'name' => $saServer['name'],
                    'operating_system' => $saServer['operating_system'],
                    'version' => $saServer['version'],
                    'cores' => $saServer['cores'],
                    'web_server' => $saServer['web_server'],
                    'agent_status' => $saServer['agent_status'],
                    'timezone' => $saServer['timezone'],
                    'database' => $saServer['database_type']
                ]);

                // Make an external API request to retrieve server applications
                $response = Helper::serveravatarClient("organizations/{$server->sa_organization_id}/servers/{$server->sa_server_id}/applications?pagination=0", 'get');

                if (isset($response['error'])) {
                    // ❌ Error response from the external source
                    throw new \Exception($response['message']);
                }

                if (!empty($response['applications'])) {
                    
                    $applicationId = [];
                    foreach ($response['applications'] as $application) {
                        $applicationId[] = $application['id'];

                        // Update or create application records based on external data
                        Application::updateOrCreate(['sa_application_id' => $application['id']], [
                            'server_id' => $server->id,
                            'name' => $application['name'],
                            'framework' => $application['framework'],
                            'php_version' => $application['php_version'],
                            'primary_domain' => $application['primary_domain'],
                            'ssl' => $application['ssl'],
                            'size' => $application['size'],
                            'active' => $application['active']

                        ]);
                    }
                    // Delete applications that are no longer associated with the server
                    Application::where('server_id', $server->id)->whereNotIn('sa_application_id', $applicationId)->delete();
                }            
            }

            // Delete servers that are no longer present in the external data
            Server::whereNotIn('sa_server_id', $serverId)->delete();

            return [
                "message" => "Servers has been sync successfully."
            ];
        }  catch(\Exception $e) {
            report($e);
            $message = $e->getMessage() ? $e->getMessage() : 'Something went really wrong while sync all servers and applications!';
            throw new \Exception($message);
        }
    }

    public function syncPermission($user) {
        
        try {
            $servers = Server::get();

            foreach($servers as $server) {
                 $userServer = UserServer::updateOrCreate([
                    'user_id' => $user->id,
                    'server_id' => $server->id
                ]);

                $applications = Application::where('server_id', $server->id)->get();
                $userServer->applications()->detach($userServer->applications);
                $userServer->applications()->attach($applications);
            }

            return [
                "message" => "Permission has been attached successfully."
            ];

        } catch(\Exception $e) {
            report($e);
            $message = $e->getMessage() ? $e->getMessage() : 'Something went really wrong while sync permission!';
            throw new \Exception($message);
        }
    }
}