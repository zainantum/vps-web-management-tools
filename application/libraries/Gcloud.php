<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Cloud\Compute\V1\InstancesClient;
use Google\Cloud\Compute\V1\AttachedDisk;
use Google\Cloud\Compute\V1\AttachedDiskInitializeParams;
use Google\Cloud\Compute\V1\Instance;
use Google\Cloud\Compute\V1\NetworkInterface;
use Google\Cloud\Compute\V1\Address;
use Google\Cloud\Compute\V1\AccessConfig;
use Google\Cloud\Compute\V1\Enums\AttachedDisk\Type;
use Google\Cloud\Compute\V1\NetworksClient;
use Google\Cloud\Compute\V1\Items;
use Google\Cloud\Compute\V1\Metadata;

class Gcloud{
    public $keyPath='';

    function __construct(){
	    $this->ci =& get_instance(); 
        $this->ci->load->database();
        
	}

    function getKey($id){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/exorde/assets/key/$id.json");
    }
    
    function list_all_instances(string $projectId)
    {
        $this->getKey($projectId);
        // List Compute Engine instances using InstancesClient.
        $instancesClient = new InstancesClient();
        $allInstances = $instancesClient->aggregatedList($projectId);
        $str = "";
        foreach ($allInstances as $zone => $zoneInstances) {
            $instances = $zoneInstances->getInstances();
            if (count($instances) > 0) {
                $response = $instancesClient->list($projectId, str_replace("zones/","",$zone));
                foreach ($response as $element) {
                    $dataLeng = json_decode($element->serializeToJsonString(), true);
                        $nama = $dataLeng["name"];
                        if(array_key_exists("natIP",$dataLeng["networkInterfaces"][0]["accessConfigs"][0])){
                            $ip = $dataLeng["networkInterfaces"][0]["accessConfigs"][0]["natIP"];
                        }else{
                            $ip = 'VPS Stop';
                        }
                        $str .= "<option value='$projectId;$zone;".$nama."'>".$nama." ~ $ip ~ $zone</option>";
                }
            }
        }

        return $str;
    }

    function getIp(string $projectId, string $zone, string $in)
    {
        $this->getKey($projectId);
        // List Compute Engine instances using InstancesClient.
        $instancesClient = new InstancesClient();
        $response = $instancesClient->list($projectId, $zone);
        $ip = "";
        foreach ($response as $element) {
            // printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
            $dataLeng = json_decode($element->serializeToJsonString(), true);
            $nama = $dataLeng["name"];
            if($in == $nama){
                $ip = $dataLeng["networkInterfaces"][0]["accessConfigs"][0]["natIP"];
            }
        }
        return $ip;
    }

    function delete_all_instances(string $projectId)
    {
        $this->getKey($projectId);
        // List Compute Engine instances using InstancesClient.
        $rescode = 0;
        $resmsg = "Semua vps berhasil dihapus";
        $instancesClient = new InstancesClient();
        $allInstances = $instancesClient->aggregatedList($projectId);
        $str = "";
        foreach ($allInstances as $zone => $zoneInstances) {
            $instances = $zoneInstances->getInstances();
            if (count($instances) > 0) {
                $response = $instancesClient->list($projectId, str_replace("zones/","",$zone));
                foreach ($response as $element) {
                    $dataLeng = json_decode($element->serializeToJsonString(), true);
                    $nama = $dataLeng["name"];
                    $del = json_decode($this->delete_instance($projectId, str_replace("zones/","",$zone), $nama), true);
                    $lama = $dataLeng["networkInterfaces"][0]["accessConfigs"][0]["natIP"];
                    //read the entire string
                    $str=file_get_contents(FCPATH.'listIp.txt');
                    if($lama != "" && strpos($str, $lama) !== false){
                        $str=str_replace($lama, "",$str);
                        //write the entire string
                        file_put_contents(FCPATH.'listIp.txt', $str);
                    }
                }
            }
        }
        return json_encode(
			array(
			  'result_code'=>$rescode, 
			  'result_msg'=>$resmsg
			)
		  );
    }

    function create_instance(
        string $projectId,
        string $zone,
        string $instanceName,
        string $machineType = 'e2-standard-4',
        int $diskSize = 125,
        string $sourceImage = 'projects/ubuntu-os-cloud/global/images/ubuntu-2204-jammy-v20230630',
        string $networkName = 'global/networks/default'
    ) {
        $rescode = 3;
        $resmsg = "";
        $this->getKey($projectId);
        // Set the machine type using the specified zone.
        $machineTypeFullName = sprintf('zones/%s/machineTypes/%s', $zone, $machineType);

        // Describe the source image of the boot disk to attach to the instance.
        $diskInitializeParams = (new AttachedDiskInitializeParams())
            ->setSourceImage($sourceImage);
        $disk = (new AttachedDisk())
            ->setBoot(true)
            ->setAutoDelete(true)
            ->setType(Type::PERSISTENT)
            ->setDiskSizeGb($diskSize)
            ->setInitializeParams($diskInitializeParams);
        
        $networkConfig = (new AccessConfig())
        ->setType("ONE_TO_ONE_NAT")
        ->setName("External NAT");
        // Use the network interface provided in the $networkName argument.
        $network = (new NetworkInterface())
            ->setName($networkName)
            ->setStackType("IPV4_ONLY")
            ->setAccessConfigs(array($networkConfig));
        
        $instance = (new Instance())
            ->setName($instanceName)
            ->setDisks([$disk])
            ->setMachineType($machineTypeFullName)
            ->setNetworkInterfaces([$network]);

        // Insert the new Compute Engine instance using InstancesClient.
        $instancesClient = new InstancesClient();
        $operation = $instancesClient->insert($instance, $projectId, $zone);

        // Wait for the operation to complete.
        $operation->pollUntilComplete();
        if ($operation->operationSucceeded()) {
            $resmsg = "Vps dengan nama $instanceName berhasil dibuat";
            $rescode = 0;
            // log_message("debug","check success create vm ".json_encode($operation));
            // printf('Created instance %s' . PHP_EOL, $instanceName);
        } else {
            $error = $operation->getError();
            $resmsg = $error->getMessage().". Silahkan coba menggunakan zone yg lain";
            // printf('Instance creation failed: %s' . PHP_EOL, $error?->getMessage());
        }

        return json_encode(
			array(
			  'result_code'=>$rescode, 
			  'result_msg'=>$resmsg
			)
		  );	
    }

    function delete_instance(
        string $projectId,
        string $zone,
        string $instanceName
    ) {
        $rescode = 3;
        $resmsg = "";
        $this->getKey($projectId);
        log_message("debug","check delete vps $instanceName, $projectId, $zone");
        // Delete the Compute Engine instance using InstancesClient.
        $instancesClient = new InstancesClient();
        $operation = $instancesClient->delete($instanceName, $projectId, $zone);

        // Wait for the operation to complete.
        $operation->pollUntilComplete();
        if ($operation->operationSucceeded()) {
            $resmsg = "Vps dengan nama $instanceName berhasil dihapus";
            $rescode = 0;
        } else {
            $error = $operation->getError();
            $resmsg = $error->getMessage().". Vps tidak dapat dihapus";
        }
        return json_encode(
			array(
			  'result_code'=>$rescode, 
			  'result_msg'=>$resmsg
			)
		  );
    }

    function start_instance(
        string $projectId,
        string $zone,
        string $instanceName
    ) {
        $rescode = 3;
        $resmsg = "";
        $this->getKey($projectId);
        // Start the Compute Engine instance using InstancesClient.
        $instancesClient = new InstancesClient();
        $operation = $instancesClient->start($instanceName, $projectId, $zone);
    
        // Wait for the operation to complete.
        $operation->pollUntilComplete();
        if ($operation->operationSucceeded()) {
            $resmsg = "Vps dengan nama $instanceName berhasil distart";
            $rescode = 0;
        } else {
            $error = $operation->getError();
            $resmsg = $error->getMessage().". Vps tidak dapat distart";
        }

        return json_encode(
			array(
			  'result_code'=>$rescode, 
			  'result_msg'=>$resmsg
			)
		  );
    }

    function stop_instance(
        string $projectId,
        string $zone,
        string $instanceName
    ) {
        $rescode = 3;
        $resmsg = "";
        $this->getKey($projectId);
        log_message("debug","check stop vps $instanceName, $projectId, $zone");
        // Stop the Compute Engine instance using InstancesClient.
        $instancesClient = new InstancesClient();
        $operation = $instancesClient->stop($instanceName, $projectId, $zone);
    
        // Wait for the operation to complete.
        $operation->pollUntilComplete();
        if ($operation->operationSucceeded()) {
            $resmsg = "Vps dengan nama $instanceName berhasil distop";
            $rescode = 0;
        } else {
            $error = $operation->getError();
            $resmsg = $error->getMessage().". Vps tidak dapat distop";
        }
        return json_encode(
			array(
			  'result_code'=>$rescode, 
			  'result_msg'=>$resmsg
			)
		  );
    }
}



?>
