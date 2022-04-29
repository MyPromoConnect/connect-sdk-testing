<?php

namespace App\Console\Commands;

use App\Services\ClientService;
use Illuminate\Console\Command;
use MyPromo\Connect\SDK\Exceptions\ApiRequestException;
use MyPromo\Connect\SDK\Exceptions\ApiResponseException;
use MyPromo\Connect\SDK\Exceptions\InputValidationException;
use MyPromo\Connect\SDK\Helpers\ProductVariantOptions;
use MyPromo\Connect\SDK\Repositories\Client\ClientConnectorRepository;
use MyPromo\Connect\SDK\Repositories\Client\ClientSettingRepository;
use MyPromo\Connect\SDK\Repositories\Orders\OrderRepository;
use MyPromo\Connect\SDK\Models\Design;
use MyPromo\Connect\SDK\Repositories\Designs\DesignRepository;

class TestSdk extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'test:sdk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This script will test connect SDK all methods one by one.';

    /**
     * This will be the url which we used as connect endpoint to access data.
     * You can set this in ..env file against variable (CONNECT_ENDPOINT_URL)
     *
     * @var $connectEndPointUrl
     */
    protected $connectEndPointUrl;

    /**
     * @var ClientService
     */
    public $clientService;

    /**
     * @var ClientMerchant
     */
    public $clientMerchant;

    /**
     * @var ClientFulfiller
     */
    public $clientFulfiller;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClientService $clientService)
    {
        parent::__construct();
        $this->clientService = $clientService;
        $this->connectEndPointUrl = config('connect.endpoint_url');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        # Introduction to tool
        $this->info('This script will test all methods of "Mypromo Connect SDK" ony by one!');
        $this->info('Testing Start........');
        $this->info('');

        # Start Testing

        # Test Connection
        $this->makeConnectionWithMerchantClient();
        $this->info('');

        $this->makeConnectionWithFulfillerClient();
        $this->info('');


        // TODO - DRAFT
        // TODO - finish all api routes in this category and add tests
        # Test General
        $this->testGeneralRoutes();
        $this->info('');

        // TODO - WIP
        // TODO - finish all api routes in this category and add tests
        // TODO - contains api bugs
        # Test Client Settings
        $this->testClientSettings();
        $this->info('');

        // TODO - WIP
        // TODO - contains api bugs
        # Test Client Connectors
        $this->testClientConnectors();
        $this->info('');

        //dd('bla');


        // TODO - no tests written yet
        # Test Client Jobs
        $this->testClientJobs();
        $this->info('');


        # Test Design Module
        $this->testDesignModule();
        $this->info('');

        # Test Orders Module
        $this->testOrdersModule();
        $this->info('');

        # Test products
        $this->testProducts();
        $this->info('');

        # Test product export
        $this->testProductExport();
        $this->info('');

        # Test product import
        $this->testProductImport();
        $this->info('');


        // TODO - sdk does not support this routes yet
        # Test configuratior
        $this->testProductConfigurator();
        $this->info('');


        # Test production
        $this->testProduction();
        $this->info('');

        # Test Miscellaneous
        $this->testMiscellaneous();
        $this->info('');

        // TODO - DRAFT
        // TODO - finish all api routes in this category and add tests
        # Test admin routes
        $this->testAdminRoutes();
        $this->info('');

        return 0;
    }

    /**
     * This method will test and return connection of client with endpoint
     */
    public function makeConnectionWithMerchantClient()
    {
        $this->startMessage("Build client connection for Merchant. This module will test and create connection with ' . $this->connectEndPointUrl . '");
        $clientId = config('connect.client_merchant_id');
        $clientSecret = config('connect.client_merchant_secret');

        try {

            $this->clientMerchant = $this->clientService->connect($clientId, $clientSecret);
            $status = $this->clientMerchant->status();

            if ($status['message'] !== 'OK') {
                $this->error('Connection failed!');
                return 0;
            }

            $this->info('Connection created successfully!');

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->info('Client connection testing finished!');
    }


    public function makeConnectionWithFulfillerClient()
    {
        $this->startMessage("Build client connection for Fulfiller. This module will test and create connection with ' . $this->connectEndPointUrl . '");
        $clientId = config('connect.client_fulfiller_id');
        $clientSecret = config('connect.client_fulfiller_secret');

        try {

            $this->clientFulfiller = $this->clientService->connect($clientId, $clientSecret);
            $status = $this->clientFulfiller->status();

            if ($status['message'] !== 'OK') {
                $this->error('Connection failed!');
                return 0;
            }

            $this->info('Connection created successfully!');

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->info('Client connection testing finished!');
    }


    /*
     * testGeneralRoutes
     */
    public function testGeneralRoutes()
    {
        $this->startMessage('TODO - testGeneralRoutes');

        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->warn('Routes are in draft mode yet!!!');
        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    }


    /*
     * testClientSettings
     */
    public function testClientSettings()
    {
        $this->startMessage('Client settings test start......');
        $clientSettingRepositoryMerchant = new ClientSettingRepository($this->clientMerchant);
        $clientSettingRepositoryFulfiller = new ClientSettingRepository($this->clientFulfiller);


        $this->testDetail('Get client settings for merchant');

        try {
            $clientSettingResponseMerchant = $clientSettingRepositoryMerchant->getSettings();
            $this->printApiResponse(print_r($clientSettingResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Get client settings for fulfillers');

        try {
            $clientSettingResponseFulfiller = $clientSettingRepositoryFulfiller->getSettings();
            $this->printApiResponse(print_r($clientSettingResponseFulfiller, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Patch client settings for merchant');

        $clientSettingsMerchant = new \MyPromo\Connect\SDK\Models\ClientSettingMerchant();

        $clientSettingsMerchant->setActivateNewFulfiller(true);
        $clientSettingsMerchant->setActivateNewProducts(true);
        $clientSettingsMerchant->setHasToSupplyCarrier(true);
        $clientSettingsMerchant->setHasToSupplyTrackingCode(true);
        $clientSettingsMerchant->setPriceResetLogic(1);
        $clientSettingsMerchant->setAdjustMaxUpPercentage(0);
        $clientSettingsMerchant->setAdjustMaxDownPercentage(0);
        $clientSettingsMerchant->setSentToProductionDelay(1);


        try {
            $clientSettingResponseMerchant = $clientSettingRepositoryMerchant->update($clientSettingsMerchant);
            $this->printApiResponse(print_r($clientSettingResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO just commented out cause of an api issue - revert, when CO-2313 is done
            #return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Patch client settings for fulfiller');

        $clientSettingsFulfiller = new \MyPromo\Connect\SDK\Models\ClientSettingFulfiller();

        $clientSettingsFulfiller->setHasToSupplyCarrier(true);
        $clientSettingsFulfiller->setHasToSupplyTrackingCode(true);


        try {
            $clientSettingResponseMerchant = $clientSettingRepositoryMerchant->update($clientSettingsMerchant);
            $this->printApiResponse(print_r($clientSettingResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO just commented out cause of an api issue - revert, when CO-2313 is done
            #return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

    }


    /*
     * testClientConnectors
     */
    public function testClientConnectors()
    {
        $this->startMessage('Client connectors test start......');
        $clientConnectorRepositoryMerchant = new ClientConnectorRepository($this->clientMerchant);
        $clientConnectorRepositoryFulfiller = new ClientConnectorRepository($this->clientFulfiller);


        $this->testDetail('Get client connectors for merchant');

        $clientConnectorOptions = new \MyPromo\Connect\SDK\Helpers\ClientConnectorOptions();
        $clientConnectorOptions->setPage(1); // get data from this page number
        $clientConnectorOptions->setPerPage(5);
        $clientConnectorOptions->setPagination(false);
        #$clientConnectorOptions->setConnectorId(12);
        #$clientConnectorOptions->setConnectorKey('shopify');
        #$clientConnectorOptions->setTarget('sales_channel');

        try {
            $clientConnectorsResponseMerchant = $clientConnectorRepositoryMerchant->all($clientConnectorOptions);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Patch client connectors for merchant to shopify');

        $clientConnector = new \MyPromo\Connect\SDK\Models\ClientConnector();
        //$clientConnector->setConnectorId(14);
        $clientConnector->setConnectorKey('shopify');
        $clientConnector->setTarget('sales_channel');

        $shopifyConfigurations = new \MyPromo\Connect\SDK\Helpers\ConnectorConfigurationsShopify();
        $shopifyConfigurations->setShopName('Shop Name' . ' - ' . date('Y-m-d H:i:s'));
        $shopifyConfigurations->setToken('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' . ' - ' . date('Y-m-d H:i:s'));
        $shopifyConfigurations->setShopUrl('mypromo-demo.myshopify.com' . ' - ' . date('Y-m-d H:i:s'));

        $shopifyConfigurations->setSalePriceConfig('use_sales_price');
        $shopifyConfigurations->setShopCurrency('EUR');
        $shopifyConfigurations->setProductsLanguage('FR');
        $shopifyConfigurations->setSyncProductSettings('normal');
        $shopifyConfigurations->setCreateCollections(true);
        $shopifyConfigurations->setUpdateImages(true);
        $shopifyConfigurations->setUpdateProducts(true);
        $shopifyConfigurations->setUpdateSeo(true);
        $shopifyConfigurations->setRecreateDeletedCollection(true);
        $shopifyConfigurations->setRecreateDeletedProducts(true);
        $shopifyConfigurations->setAddNewProductsAutomatically(true); // new
        $shopifyConfigurations->setUseMegaMenu(true); // new

        // TODO - settings not saved correctly anymore! see CO-2314
        // TODO - new settings added to sdk - after adding to api: test it!! see CO-2315

        $clientConnector->setConfiguration($shopifyConfigurations);

        $this->info(print_r($clientConnector->toArray(), true));


        try {
            $clientConnectorsResponseMerchant = $clientConnectorRepositoryMerchant->update($clientConnector);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Patch client connectors for merchant to magento');

        $clientConnector = new \MyPromo\Connect\SDK\Models\ClientConnector();
        //$clientConnector->setConnectorId(14);
        $clientConnector->setConnectorKey('magento');
        $clientConnector->setTarget('sales_channel');

        $magentoConfigurations = new \MyPromo\Connect\SDK\Helpers\ConnectorConfigurationsMagento();
        $magentoConfigurations->setInstanceUrl('url');
        $magentoConfigurations->setApiUsername('username');
        $magentoConfigurations->setApiPassword('password');
        $magentoConfigurations->setWebsiteCode('XXXX');
        $magentoConfigurations->setWebsiteCodeId(1);
        $magentoConfigurations->setWebsiteCodeName('Website name');
        $magentoConfigurations->setStoreCode('XXXXX');
        $magentoConfigurations->setStoreCodeId(1);
        $magentoConfigurations->setStoreCodeName('Store code');
        $magentoConfigurations->setSyncProductsSettings('normal');
        $magentoConfigurations->setSalesPriceConfig('use_sales_price');

        $clientConnector->setConfiguration($magentoConfigurations);

        $this->info(print_r($clientConnector->toArray(), true));


        try {
            $clientConnectorsResponseMerchant = $clientConnectorRepositoryMerchant->update($clientConnector);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO - commented out and added error message - after fix the issue CO-2316 revert
            $this->error('setting magento as connector is not working - CO-2316');
            //return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->error('We just have helpers for magento and shopify - add configuration helpers for all other connectors !!!');
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

    }

    /*
     * testClientJobs
     */
    public function testClientJobs()
    {
        $this->startMessage('TODO - testClientJobs');

        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->error('Could not find any repository !!!');
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    }


    /**
     * This method will test design API/Module of SDK
     */
    public function testDesignModule()
    {
        $this->startMessage('Design module testing start......');
        $designRepository = new DesignRepository($this->clientMerchant);

        $design = new Design();
        $design->setEditorUserHash(md5('hashing_string'));
        $design->setReturnUrl(config('connect.shop_url'));
        $design->setCancelUrl(config('connect.shop_url'));
        $design->setSku('MP-F10005-C0000001');
        $design->setIntent('customize');
        $design->setOptions([
            'example-key' => 'example-value'
        ]);

        // Create editor user hash
        try {
            $this->info('Generating Editor user hash....');
            $userHash = $designRepository->createEditorUserHash($design);
            $design->setEditorUserHash($userHash['editor_user_hash']);
            $this->info('Editor user hash generate successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Create Design
        try {
            $this->info('Create design....');
            $designRepository->create($design);

            if ($design->getId()) {
                $this->info('Design with ID ' . $design->getId() . ' created successfully!');
            }
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Submit Design
        try {
            $this->info('Submitting design....');
            $designRepository->submit($design->getId());
            $this->info('Design submitted successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Get Preview
        try {
            $this->info('Trying to get preview.....');
            $designRepository->getPreviewPDF($design->getId());
            $this->info('Preview received successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Save Preview
        try {
            $this->info('Trying preview save .....');
            $designRepository->savePreview($design->getId(), 'preview.pdf');
            $this->info('Preview saved successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->info('Design module testing finished!');
    }

    /**
     * test sdk module for orders
     */
    public function testOrdersModule()
    {
        $this->startMessage('Orders module testing under development...');

        $this->info('Create a new design');

        $designRepository = new DesignRepository($this->clientMerchant);

        $design = new Design();

        try {
            $this->info('Create a new design user');
            $hash = $designRepository->createEditorUserHash($design);
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        if (!isset($hash['editor_user_hash'])) {
            $this->error('Editor hash not found.');
            return 0;
        }

        $design->setEditorUserHash($hash['editor_user_hash']);
        $design->setReturnUrl('https://yourshop.com/basket/TPD123LD02LAXALOP/{DESIGNID}/add/{INTENT}/{USERHASH}/{INTENT}/{DESIGNID}');
        $design->setCancelUrl('https://yourshop.com/product/TPD123LD02LAXALOP/design/{DESIGNID}/user/{USERHASH}');
        $design->setSku('MP-F10005-C0000001');
        $design->setIntent('customize');
        $design->setQuantity(10);

        try {
            $this->info('Create a design with the design user');
            $designResponse = $designRepository->create($design);
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->info("Editor start URL : " . $designResponse['editor_start_url']);

        try {
            $this->info('Submit the design');
            $designResponse = $designRepository->submit($design->getId());
            $this->printApiResponse(print_r($designResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->info('Create an order');

        $orderRepository = new OrderRepository($this->clientMerchant);

        $recipientAddress = new \MyPromo\Connect\SDK\Models\Address();
        $recipientAddress->setAddressId(null);
        $recipientAddress->setAddressKey(null);
        $recipientAddress->setReference('your-reference-code');
        $recipientAddress->setCompany('Sample Company');
        $recipientAddress->setDepartment(null);
        $recipientAddress->setSalutation(null);
        $recipientAddress->setGender(null);
        $recipientAddress->setDateOfBirth(new \DateTime(date('Y-m-d H:i:s')));
        $recipientAddress->setFirstname('Sam');
        $recipientAddress->setMiddlename(null);
        $recipientAddress->setLastname('Sample');
        $recipientAddress->setStreet('Sample Street 1');
        $recipientAddress->setCareOf('Street Add');
        $recipientAddress->setZip(12345);
        $recipientAddress->setCity('Sample Town');
        $recipientAddress->setStateCode('NW');
        $recipientAddress->setDistrict('your-disctrict');
        $recipientAddress->setCountryCode('DE');
        $recipientAddress->setPhone('your-phone');
        $recipientAddress->setFax('your-fax');
        $recipientAddress->setMobile('your-mobile');
        $recipientAddress->setEmail('sam@sample.com');
        $recipientAddress->setVatId('DE1234567890');
        $recipientAddress->setEoriNumber('55555555555');
        $recipientAddress->setAccountHolder('account-holder');
        $recipientAddress->setIban('your-iban');
        $recipientAddress->setBicOrSwift('your-bic-or-swift');
        $recipientAddress->setCommercialRegisterEntry('your-commercial-register-entry');


        $order = new \MyPromo\Connect\SDK\Models\Order();
        $order->setReference('your-order-reference');
        $order->setReference2('your-order-reference2');
        $order->setComment('your comment for order here');
        //$order->setShipper($shipperAddress);
        $order->setRecipient($recipientAddress);
        //$order->setExport($exportAddress);
        //$order->setInvoice($invoiceAddress);

        // CO-1601 not working properly...
        $order->setInvoice($recipientAddress);

        # Optional parameters
        $order->setFakePreflight(true);
        $order->setFakeShipment(true);

        try {
            $this->info('Sending order');
            $orderResponse = $orderRepository->create($order);
            $this->printApiResponse(print_r($orderResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        dd('go on writing tests - add items - submit order - get order etc.');


        $this->info('Add order item with design');

        $orderItem = new \MyPromo\Connect\SDK\Models\OrderItem();
        $orderItem->setOrderId($order->getId());
        $orderItem->setQuantity(35);
        $orderItem->setReference('your-reference');
        $orderItem->setComment('comment for order item here');

        $orderItem->setDesigns($design);


        $design->getId();


        $this->info('Add order item with file');

        $orderItem = new \MyPromo\Connect\SDK\Models\OrderItem();
        $orderItem->setOrderId($order->getId());
        $orderItem->setReference('your-reference');
        $orderItem->setQuantity(35);
        $orderItem->setSku('product-sku');
        $orderItem->setComment('comment for order item here');

        # To add service item mention order_item_id in relation
        $orderItemRelation = new \MyPromo\Connect\SDK\Models\OrderItemRelation();
        $orderItemRelation->setOrderItemId(22);

        # To set relation pass object of orderItemRelation after setting up order_item_id which is added previously in order
        $orderItem->setRelation($orderItemRelation->toArray());


    }


    /*
     * test sdk module for product export
     */
    public function testProductExport()
    {
        $this->startMessage('Product Export Module testing...');
        $requestExportRepository = new \MyPromo\Connect\SDK\Repositories\ProductFeeds\ProductExportRepository($this->clientMerchant);


        $this->testDetail('Requesting new export...');
        $productExport = $this->createExport($requestExportRepository);


        $this->testDetail('Request data of newly created export...');

        try {
            $requestExportByIdResponse = $requestExportRepository->find($productExport->getId());
            $this->printApiResponse(print_r($requestExportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Request data of all exports...');

        try {
            $productExportOptions = new \MyPromo\Connect\SDK\Helpers\ProductExportOptions();
            $productExportOptions->setPage(1); // get data from this page number
            $productExportOptions->setPerPage(5);
            $productExportOptions->setPagination(false);
            $productExportOptions->setCreatedFrom(new \DateTime(date('Y-m-d H:i:s')));
            $productExportOptions->setCreatedTo(new \DateTime(date('Y-m-d H:i:s')));

            $this->info(print_r($productExportOptions->toArray(), 1));

            $requestExportAllResponse = $requestExportRepository->all($productExportOptions);

            $this->printApiResponse(print_r($requestExportAllResponse, 1));

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new export... Cancel test');
        $productExport = $this->createExport($requestExportRepository);
        try {
            $this->info('Trying to cancel...');
            $requestExportByIdResponse = $requestExportRepository->cancelExport($productExport->getId());
            $this->printApiResponse(print_r($requestExportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            //return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new export... Delete test');
        $productExport = $this->createExport($requestExportRepository);
        try {
            $this->info('Trying to delete...');
            $requestExportByIdResponse = $requestExportRepository->deleteExport($productExport->getId());
            $this->printApiResponse(print_r($requestExportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            //return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

    }


    public function createExport(\MyPromo\Connect\SDK\Repositories\ProductFeeds\ProductExportRepository $requestExportRepository)
    {
        /*
         * TODO error in API - CO2291
         *
         */

        $this->startMessage('Requesting new export...');

        $productExport = new \MyPromo\Connect\SDK\Models\ProductExport();

        $productExport->setTempletaId(null);
        $productExport->setTempletaKey('prices');
        $productExport->setFormat('xlsx');

        $productExportFilterOptions = new \MyPromo\Connect\SDK\Helpers\ProductExportFilterOptions();
        $productExportFilterOptions->setCategoryId(null);
        $productExportFilterOptions->setCurrency('EUR');
        $productExportFilterOptions->setLang('DE');
        $productExportFilterOptions->setProductTypes($productExportFilterOptions::ProductExportFilterOptionsProductTypeAll);
        $productExportFilterOptions->setSearch(null);
        $productExportFilterOptions->setSku(null);
        $productExportFilterOptions->setShippingFrom('DE');
        $productExport->setFilters($productExportFilterOptions);

        $callback = new \MyPromo\Connect\SDK\Models\Callback();
        $callback->setUrl(config('connect.callback_url'));
        $productExport->setCallback($callback);

        try {
            $this->info('Sending Export Request');

            $requestExportResponse = $requestExportRepository->requestExport($productExport);
            $this->printApiResponse(print_r($requestExportResponse, 1));

            if ($productExport->getId()) {
                $this->info('Export with ID ' . $productExport->getId() . ' created successfully!');
            }
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        return $productExport;

    }


    /*
     * test sdk module for product import
     */
    public function testProductImport()
    {
        $this->startMessage('Product Import Module testing...');
        $requestImportRepository = new \MyPromo\Connect\SDK\Repositories\ProductFeeds\ProductImportRepository($this->clientMerchant);


        $this->testDetail('Requesting new import...');
        $productImport = $this->createImport($requestImportRepository);


        $this->testDetail('Request data of newly created import...');

        try {
            $requestImportByIdResponse = $requestImportRepository->find($productImport->getId());
            $this->printApiResponse(print_r($requestImportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->testDetail('Request data of all imports...');

        try {
            $productImportOptions = new \MyPromo\Connect\SDK\Helpers\ProductImportOptions();
            $productImportOptions->setPage(1); // get data from this page number
            $productImportOptions->setPerPage(5);
            $productImportOptions->setPagination(false);
            $productImportOptions->setCreatedFrom(new \DateTime(date('Y-m-d H:i:s')));
            $productImportOptions->setCreatedTo(new \DateTime(date('Y-m-d H:i:s')));

            $this->info(print_r($productImportOptions->toArray(), 1));

            $requestImportAllResponse = $requestImportRepository->all($productImportOptions);

            $this->printApiResponse(print_r($requestImportAllResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new import... Cancel test');
        $productImport = $this->createImport($requestImportRepository);
        try {
            $this->info('Trying to cancel...');
            $requestImportByIdResponse = $requestImportRepository->cancelImport($productImport->getId());
            $this->printApiResponse(print_r($requestImportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            //return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new import... Delete test');
        $productImport = $this->createImport($requestImportRepository);
        try {
            $this->info('Trying to delete...');
            $requestImportByIdResponse = $requestImportRepository->deleteImport($productImport->getId());
            $this->printApiResponse(print_r($requestImportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            #return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new import... Validate test');
        $productImport = $this->createImport($requestImportRepository);
        try {
            $this->info('Trying to validate...');
            $requestImportByIdResponse = $requestImportRepository->validate($productImport->getId());
            $this->printApiResponse(print_r($requestImportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            #return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        // TODO
        // confirm import (needs body data...)

    }

    public function createImport(\MyPromo\Connect\SDK\Repositories\ProductFeeds\ProductImportRepository $requestImportRepository)
    {
        $productImport = new \MyPromo\Connect\SDK\Models\ProductImport();
        $productImport->setTempletaId(null);
        $productImport->setTempletaKey('prices');
        $productImport->setDryRun(false);
        $productImport->setDateExecute(null);

        $productImportInput = new \MyPromo\Connect\SDK\Helpers\ProductImportInput();

        //$productImportInput->setUrl('https://downloads.test.mypromo.com/feeds/Merchant-Prices.xlsx');
        $productImportInput->setUrl('https://mypromo-shopify-dev.s3.eu-central-1.amazonaws.com/1651047033.xlsx?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAQYOR5ZFDEHKX74RD%2F20220427%2Feu-central-1%2Fs3%2Faws4_request&X-Amz-Date=20220427T081034Z&X-Amz-SignedHeaders=host&X-Amz-Expires=604800&X-Amz-Signature=9ad5635d5f2b842d96af495d69e93c286cc3b460f45f5ce6ea953b38b93f5e68');
        $productImportInput->setFormat('xlsx');

        $productImport->setInput($productImportInput);

        $callback = new \MyPromo\Connect\SDK\Models\Callback();
        $callback->setUrl(config('connect.callback_url'));
        $productImport->setCallback($callback);

        try {
            $this->info('Sending Import Request');

            $requestImportResponse = $requestImportRepository->requestImport($productImport);
            $this->printApiResponse(print_r($requestImportResponse, 1));

            if ($productImport->getId()) {
                $this->info('Import with ID ' . $productImport->getId() . ' created successfully!');
            }
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        return $productImport;
    }


    /*
     * testProducts
     */
    public function testProducts()
    {
        $this->startMessage('test product routes');

        $productsRepositoryMerchant = new \MyPromo\Connect\SDK\Repositories\Products\ProductRepository($this->clientMerchant);
        $productsRepositoryFulfiller = new \MyPromo\Connect\SDK\Repositories\Products\ProductRepository($this->clientFulfiller);


        $this->testDetail('get all products');
        $productsOptions = new \MyPromo\Connect\SDK\Helpers\ProductOptions();
        $productsOptions->setFrom(1);
        $productsOptions->setPage(1);
        $productsOptions->setPerPage(5);
        $productsOptions->setPagination(false);
        $productsOptions->setShippingFrom('DE');

        $productsOptions->setAvailable(true);
        $productsOptions->setCurrency('EUR');
        $productsOptions->setSearch(null);
        $productsOptions->setLang("DE");
        $productsOptions->setIncludeVariants(true);

        try {
            $productsResponse = $productsRepositoryMerchant->all($productsOptions);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->testDetail('get data of a single product');

        if (!empty($productsResponse['data'][0]['id'])) {
            $this->info('Getting first product of previous result');
            $productId = $productsResponse['data'][0]['id'];

            // TODO - needs to get own helper object...
            $this->warn('Product Options for get by id needs to get own helper object');
            $productsOptions = new \MyPromo\Connect\SDK\Helpers\ProductOptions();
            $productsOptions->setLang("DE");
            $productsOptions->setIncludeVariants(true);

            try {
                $productResponseSingleObj = $productsRepositoryMerchant->find($productId, $productsOptions);
                $this->printApiResponse(print_r($productResponseSingleObj, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error($e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->info('Unable to perform test, cause there are no products.');
        }


        $this->testDetail('get variants of a product');

        if (!empty($productsResponse['data'][0]['variants']['id'])) {
            $this->info('Getting variants of product of previous result');
            $productVariantId = $productsResponse['data'][0]['variants']['id'];

            $productVariantOptions = new \MyPromo\Connect\SDK\Helpers\ProductVariantOptions();
            $productVariantOptions->setFrom(1);
            $productVariantOptions->setPage(1);
            $productVariantOptions->setPerPage(5);
            $productVariantOptions->setPagination(false);
            $productVariantOptions->setLang("DE");
            $productVariantOptions->setId($productVariantId);
            $productVariantOptions->setSku(null);
            $productVariantOptions->setReference(null);

            try {
                $productResponseSingleObj = $productsRepositoryMerchant->getVariants($productVariantOptions);
                $this->printApiResponse(print_r($productResponseSingleObj, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error($e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->info('Unable to perform test, cause there are no variants in product given.');
        }

        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->error('variants - not working cause include_variants filter is not working in previous request');
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');


        // TODO - get a single variant
        $this->testDetail('get variants of a product');
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->error('TODO');
        // TODO - build request
        // TODO - need own helper object for filters
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');


        $this->testDetail('get prices for client type merchant');

        $priceOptionsMerchant = new \MyPromo\Connect\SDK\Helpers\PriceOptionsMerchant();
        $priceOptionsMerchant->setPage(1);
        $priceOptionsMerchant->setPerPage(5);
        $priceOptionsMerchant->setPagination(false);
        $priceOptionsMerchant->setShippingFrom('DE');
        //$priceOptionsMerchant->setSku('MP-F10005-C0000001');

        try {
            $productsResponse = $productsRepositoryMerchant->getPrices($priceOptionsMerchant);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get prices for client type fulfiller');

        $priceOptionsFulfiller = new \MyPromo\Connect\SDK\Helpers\PriceOptionsFulfiller();
        $priceOptionsFulfiller->setPage(1);
        $priceOptionsFulfiller->setPerPage(5);
        $priceOptionsFulfiller->setPagination(false);
        //$priceOptionsFulfiller->setSkuFulfiller('MP-F10005-C0000001');

        try {
            $productsResponse = $productsRepositoryFulfiller->getPrices($priceOptionsFulfiller);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get inventory for client type merchant');

        $inventoryOptionsMerchant = new \MyPromo\Connect\SDK\Helpers\InventoryOptionsMerchant();
        $inventoryOptionsMerchant->setPage(1);
        $inventoryOptionsMerchant->setPerPage(5);
        $inventoryOptionsMerchant->setPagination(false);
        $inventoryOptionsMerchant->setShippingFrom('DE');
        //$inventoryOptionsMerchant->setSku('MP-F10005-C0000001');


        try {
            $productsResponse = $productsRepositoryMerchant->getInventory($inventoryOptionsMerchant);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get inventory for client type fulfiller');

        $inventoryOptionsFulfiller = new \MyPromo\Connect\SDK\Helpers\InventoryOptionsFulfiller();
        $inventoryOptionsFulfiller->setPage(1);
        $inventoryOptionsFulfiller->setPerPage(5);
        $inventoryOptionsFulfiller->setPagination(false);
        //$inventoryOptionsMerchant->setSkuFulfiller('MP-F10005-C0000001');


        try {
            $productsResponse = $productsRepositoryFulfiller->getInventory($inventoryOptionsFulfiller);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get seo overwrites for client type merchant');

        $seoOptions = new \MyPromo\Connect\SDK\Helpers\SeoOptions();
        $seoOptions->setPage(1);
        $seoOptions->setPerPage(5);
        $seoOptions->setPagination(false);
        //$seoOptions->setSku('MP-F10005-C0000001');

        try {
            $productsResponse = $productsRepositoryMerchant->getSeo($seoOptions);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->error('Add PATCH methods here as well !!!');

    }


    /*
     * testProductConfigurator
     */
    public function testProductConfigurator()
    {
        $this->startMessage('TODO - testProductConfigurator');

        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->error('Could not find any repository for the configurator routes !!!');
        $this->error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    }


    /*
     * testProduction
     */
    public function testProduction()
    {
        $this->startMessage('testProduction');

        $this->testDetail('Get all production orders');
        $productionRepository = new \MyPromo\Connect\SDK\Repositories\ProductionOrders\ProductionOrderRepository($this->clientFulfiller);

        $productionOrderOptions = new \MyPromo\Connect\SDK\Helpers\ProductionOrderOptions();
        $productionOrderOptions->setFrom(1);
        $productionOrderOptions->setPage(1); // get data from this page number
        $productionOrderOptions->setPerPage(5);

        #$productionOrderOptions->setCreatedFrom(new \DateTime(date('Y-m-d H:i:s')));
        #$productionOrderOptions->setCreatedTo(new \DateTime(date('Y-m-d H:i:s')));
        #$productionOrderOptions->setUpdatedFrom(new \DateTime(date('Y-m-d H:i:s')));
        #$productionOrderOptions->setUpdatedTo(new \DateTime(date('Y-m-d H:i:s')));

        try {
            $productionOrderResponse = $productionRepository->all($productionOrderOptions);
            $this->printApiResponse(print_r($productionOrderResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Get a single production order');
        if (!empty($productionOrderResponse['data'])) {
            $this->info('Getting first production order of previous result');
            $productionOrderId = $productionOrderResponse['data'][0]['id'];

            try {
                $productionOrderResponseSingleObj = $productionRepository->find($productionOrderId);
                $this->printApiResponse(print_r($productionOrderResponseSingleObj, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error($e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->info('Unable to perform test, cause there are no production orders.');
        }

        $this->testDetail('Get a generic label');
        if (!empty($productionOrderResponse['data'])) {
            $this->info('Get a generic label for the first production order of previous result');
            $productionOrderId = $productionOrderResponse['data'][0]['id'];

            try {
                $productionOrderResponseGenericLabel = $productionRepository->genericLabel($productionOrderId);
                $this->printApiResponse(print_r($productionOrderResponseGenericLabel, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                // return 0; // TODO: just available if configured for a client of type merchant the order is coming from...
            } catch (ApiRequestException $e) {
                $this->error($e->getMessage());
                $this->stopMessage();
                return 0;
            }
        }


        $this->testDetail('Add a shipment');
        if (!empty($productionOrderResponse['data'])) {
            $this->info('Add a shipment to the first production order of previous result');
            $productionOrderId = $productionOrderResponse['data'][0]['id'];

            $shipment = new \MyPromo\Connect\SDK\Models\Shipment();

            $shipment->setCarrier('UPS');
            $shipment->setTrackingId('132415XYZ');

            $shipment->setHeight('30');
            $shipment->setWidth('45');
            $shipment->setDepth('20');
            $shipment->setWeight('10000');
            /*
            $shipment->setProductionOrderItems([
                ['id' => 1, 'quantity' => 5],
                ['id' => 2, 'quantity' => 10]
                // ........
            ]);
            */

            //$shipment->setForce(true);

            try {
                $productionOrderResponseAddShipment = $productionRepository->addShipment($productionOrderId, $shipment);
                $this->printApiResponse(print_r($productionOrderResponseAddShipment, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                //return 0; - TODO if failed we see that as ok for now...
            } catch (ApiRequestException $e) {
                $this->error($e->getMessage());
                $this->stopMessage();
                return 0;
            }


        } else {
            $this->info('Unable to perform test, cause there are no production orders.');
        }

    }


    /*
     * testMiscellaneous
     */
    public function testMiscellaneous()
    {
        $this->startMessage('testMiscellaneous');

        $this->testDetail('get api status');
        $generalRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\GeneralRepository($this->clientMerchant);

        try {
            $apiStatusResponse = $generalRepository->apiStatus();
            $this->printApiResponse(print_r($apiStatusResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        // TODO - just identifier or complete urls required ???
        $url = "A8ru29";

        try {
            // TODO - does not work!
            //$fileContent = $generalRepository->downloadFile($url);

            // TODO - this is results in error - sdk implementation is wrong!
            // TypeError
            //  MyPromo\Connect\SDK\Repositories\Miscellaneous\GeneralRepository::downloadFile(): Return value must be of type array, null returned

            // TODO: create save method similar to $designRepository->savePreview($design->getId(), 'preview.pdf');
            // alternativly offer savetodisk option and filename in the method
            // eg. downloadFile($url, true, '/path/to/file.ext')

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get carriers');
        $carrierRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\CarrierRepository($this->clientMerchant);

        $carrierOptions = new \MyPromo\Connect\SDK\Helpers\CarrierOptions();
        $carrierOptions->setPage(1);
        $carrierOptions->setPerPage(5);
        $carrierOptions->setPagination(false);

        try {
            $carrierResponse = $carrierRepository->all($carrierOptions);
            $this->printApiResponse(print_r($carrierResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get countries');
        $countryRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\CountryRepository($this->clientMerchant);

        $countryOptions = new \MyPromo\Connect\SDK\Helpers\CountryOptions();
        $countryOptions->setPage(1);
        $countryOptions->setPerPage(5);
        $countryOptions->setPagination(false);

        try {
            $countryResponse = $countryRepository->all($countryOptions);
            $this->printApiResponse(print_r($countryResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get locales');
        $localeRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\LocaleRepository($this->clientMerchant);

        $localeOptions = new \MyPromo\Connect\SDK\Helpers\LocaleOptions();
        $localeOptions->setPage(1);
        $localeOptions->setPerPage(5);
        $localeOptions->setPagination(false);

        try {
            $localeResponse = $localeRepository->all($localeOptions);
            $this->printApiResponse(print_r($localeResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get states');
        $stateRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\StateRepository($this->clientMerchant);

        $stateOptions = new \MyPromo\Connect\SDK\Helpers\StateOptions();
        $stateOptions->setPage(1);
        $stateOptions->setPerPage(5);
        $stateOptions->setPagination(false);

        try {
            $stateResponse = $stateRepository->all($stateOptions);
            $this->printApiResponse(print_r($stateResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get timezones');
        $timeZonesRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\TimezoneRepository($this->clientMerchant);

        $timeZonesOptions = new \MyPromo\Connect\SDK\Helpers\TimezoneOptions();
        $timeZonesOptions->setPage(1);
        $timeZonesOptions->setPerPage(5);
        $timeZonesOptions->setPagination(false);

        try {
            $timeZonesResponse = $timeZonesRepository->all($timeZonesOptions);
            $this->printApiResponse(print_r($timeZonesResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('API request failed: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error($e->getMessage());
            $this->stopMessage();
            return 0;
        }


    }


    /*
     * testAdminRoutes
     */
    public function testAdminRoutes()
    {
        $this->startMessage('TODO - testAdminRoutes');

        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->warn('Routes are in draft mode yet!!!');
        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    }

    /**
     * Start testing of new modules (Show hiding)
     *
     * @param $title
     */
    public function startMessage($title)
    {
        $this->info('************************************************************************************************************************************************');
        $this->info($title);
        $this->info('************************************************************************************************************************************************');
    }

    public function testDetail($title)
    {
        $this->info('------------------------------------------------------------------------------------------------------------------------------------------------');
        $this->info($title);
        $this->info('------------------------------------------------------------------------------------------------------------------------------------------------');
    }


    public function printApiResponse($response, int $crop_length = 100)
    {
        // TODO - move toggle to somewhere else
        $crop = false;

        if ($crop == true) {
            $cropped_response = substr($response, 0, $crop_length) . "\n >>> cropped response\n\n";

        } else {
            $cropped_response = $response;
        }

        $this->info($cropped_response);
    }


    /**
     * This method can be used to stop testing
     */
    public
    function stopMessage(): int
    {
        $this->error('Test failed...');
        return 0;
    }
}
