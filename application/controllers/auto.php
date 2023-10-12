<?php
require 'vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=tidal-triumph-391023-497ff6577e29.json');

use Google\Cloud\Compute\V1\InstancesClient;
use Google\Cloud\Compute\V1\AttachedDisk;
use Google\Cloud\Compute\V1\AttachedDiskInitializeParams;
use Google\Cloud\Compute\V1\Instance;
use Google\Cloud\Compute\V1\NetworkInterface;
use Google\Cloud\Compute\V1\Address;
use Google\Cloud\Compute\V1\AccessConfig;
use Google\Cloud\Compute\V1\Enums\AttachedDisk\Type;
use Google\ApiCore\ApiException;
use Google\ApiCore\PagedListResponse;
use Google\Cloud\Billing\V1\BillingAccount;
use Google\Cloud\Billing\V1\CloudBillingClient;

function list_all_instances(string $projectId)
{
    // List Compute Engine instances using InstancesClient.
    $instancesClient = new InstancesClient();
    $allInstances = $instancesClient->aggregatedList($projectId);

    printf('All instances for %s' . PHP_EOL, $projectId);
    foreach ($allInstances as $zone => $zoneInstances) {
        $instances = $zoneInstances->getInstances();
        if (count($instances) > 0) {
            printf('Zone - %s' . PHP_EOL, $zone);
            foreach ($instances as $instance) {
                printf(' - %s' . PHP_EOL, $instance->getName());
            }
        }
    }
}

function create_instance(
    string $projectId,
    string $zone,
    string $instanceName,
    string $machineType = 'e2-standard-4',
    string $sourceImage = 'projects/ubuntu-os-cloud/global/images/ubuntu-2204-jammy-v20230630',
    string $networkName = 'global/networks/default'
) {
    // Set the machine type using the specified zone.
    $machineTypeFullName = sprintf('zones/%s/machineTypes/%s', $zone, $machineType);

    // Describe the source image of the boot disk to attach to the instance.
    $diskInitializeParams = (new AttachedDiskInitializeParams())
        ->setSourceImage($sourceImage);
    $disk = (new AttachedDisk())
        ->setBoot(true)
        ->setAutoDelete(true)
        ->setType(Type::PERSISTENT)
        ->setDiskSizeGb(125)
        ->setInitializeParams($diskInitializeParams);

    
    $networkConfig = (new AccessConfig())
     ->setType("ONE_TO_ONE_NAT")
     ->setName("External NAT");
    // Use the network interface provided in the $networkName argument.
    $network = (new NetworkInterface())
        ->setName($networkName)
        ->setStackType("IPV4_ONLY")
        ->setAccessConfigs(array($networkConfig));
    
    // // get address
    // $ipName = "foo";
    // $addr = (new Address())
    // ->setName($ipName);
    // $response = $service->addresses->insert($projectId, $zone, $addr);
    // sleep(5);
    // $response = $service->addresses->get($project, $ipZone, $ipName);
    // $ip = $response->address;
    // echo($ip);

    // $networkConfig = (new AccessConfig())
    // ->setNatIP($ip)
    // ->setType("ONE_TO_ONE_NAT")
    // ->setName("External NAT");
    // Create the Instance object.
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
        printf('Created instance %s' . PHP_EOL, $instanceName);
    } else {
        $error = $operation->getError();
        printf('Instance creation failed: %s' . PHP_EOL, $error?->getMessage());
    }
}

function getBilling(){
    $client = new CloudBillingClient();
    $accounts = $client->listBillingAccounts();
    var_dump($accounts);
    foreach ($accounts as $account) {
        printf('Billing account: ' . $account->getName() . PHP_EOL);
    }
}

function list_billing_accounts_sample(): void
{
    // Create a client.
    $cloudBillingClient = new CloudBillingClient();

    // Call the API and handle any network failures.
    try {
        $response = $cloudBillingClient->listBillingAccounts();
        var_dump($response);

        foreach ($response as $element) {
            printf('Element data: %s' . PHP_EOL, $element->serializeToJsonString());
        }
    } catch (ApiException $ex) {
        printf('Call failed with message: %s' . PHP_EOL, $ex->getMessage());
    }
}

// create_instance("tidal-triumph-391023", "us-east1-b","trial");
list_all_instances("tidal-triumph-391023");
list_billing_accounts_sample();