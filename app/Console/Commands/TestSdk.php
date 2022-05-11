<?php

namespace App\Console\Commands;

use App\Services\ClientService;
use Illuminate\Console\Command;
use MyPromo\Connect\SDK\Exceptions\ApiRequestException;
use MyPromo\Connect\SDK\Exceptions\ApiResponseException;
use MyPromo\Connect\SDK\Exceptions\InputValidationException;
use MyPromo\Connect\SDK\Helpers\Products\ProductVariantOptions;
use MyPromo\Connect\SDK\Repositories\Client\ClientConnectorRepository;
use MyPromo\Connect\SDK\Repositories\Client\ClientSettingRepository;
use MyPromo\Connect\SDK\Repositories\Jobs\JobRepository;
use MyPromo\Connect\SDK\Repositories\Orders\OrderRepository;
use MyPromo\Connect\SDK\Models\Designs\Design;
use MyPromo\Connect\SDK\Repositories\Designs\DesignRepository;
use MyPromo\Connect\SDK\Repositories\Miscellaneous\GeneralRepository;
use Prophecy\Exception\Prediction\AggregateException;

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
     * @var object
     */
    public $clientMerchant;

    /**
     * @var object
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
        $this->info('This script will test all methods of "Mypromo Connect SDK" ony by one!');
        $this->info('Testing Start........');
        $this->info('');

        $this->makeConnectionWithMerchantClient();
        $this->info('');

        $this->makeConnectionWithFulfillerClient();
        $this->info('');

        // test api status
        $this->apiStatus($this->clientMerchant);


        // TODO - DRAFT
        // TODO - finish all api routes in this category and add tests
        #$this->testGeneralRoutes();
        #$this->info('');


        // TODO - WIP
        // TODO - finish all api routes in this category and add tests
        // TODO - contains api bugs
        $this->testClientSettings();
        $this->info('');


        $this->warn('We just have connector helpers for magento and shopify - add configuration helpers for all other connectors !!!');

        // TODO - WIP
        // TODO - contains api bugs
        $this->testClientConnectorsShopify();
        $this->info('');


        // TODO add more jobs
        $this->testClientJobsSalesChannel('products');
        $this->info('');

        // TODO - WIP
        // TODO - contains api bugs
        // TODO - CO-2327
        $this->testClientConnectorsMagento();
        $this->info('');


        $this->testClientJobsSalesChannel('prices');
        $this->info('');

        $this->testClientJobsSalesChannel('inventory');
        $this->info('');


        $this->testDesignModule();
        $this->info('');

        // TODO check short url provided and add download tests (see implementation of downloadFile($url, $targetFile))
        // TODO finish test (add item, relation, submit...)
        $this->testOrdersModule();
        $this->info('');


        // TODO - add PATCH tests
        // TODO - fix patch Models and Helpers - CO-2343
        $this->testProducts();
        $this->info('');


        $this->testProductExport();
        $this->info('');


        $this->testProductImport();
        $this->info('');


        // TODO - sdk does not support this routes yet
        $this->testProductConfigurator();
        $this->info('');


        // TODO check short url provided and add download tests (see implementation of downloadFile($url, $targetFile))
        $this->testProduction();
        $this->info('');


        $this->testMiscellaneous();
        $this->info('');

        // TODO - DRAFT
        // TODO - finish all api routes in this category and add tests
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
            $this->info('Connection created successfully!');

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
            $this->info('Connection created successfully!');

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
        $this->getClientSettings($clientSettingRepositoryMerchant);

        $this->testDetail('Patch client settings for merchant - V1');
        $clientSettingsMerchant = new \MyPromo\Connect\SDK\Models\Client\SettingsMerchant();

        $clientSettingsMerchantActivateNewFulfiller = true;
        $clientSettingsMerchantActivateNewProducts = true;
        $clientSettingsMerchantHasToSupplyCarrier = true;
        $clientSettingsMerchantHasToSupplyTrackingCode = true;
        $clientSettingsMerchantPriceResetLogic = 1;
        $clientSettingsMerchantAdjustMaxUpPercentage = 0;
        $clientSettingsMerchantAdjustMaxDownPercentage = 0;
        $clientSettingsMerchantSentToProductionDelay = 1;

        $clientSettingsMerchant->setActivateNewFulfiller($clientSettingsMerchantActivateNewFulfiller);
        $clientSettingsMerchant->setActivateNewProducts($clientSettingsMerchantActivateNewProducts);
        $clientSettingsMerchant->setHasToSupplyCarrier($clientSettingsMerchantHasToSupplyCarrier);
        $clientSettingsMerchant->setHasToSupplyTrackingCode($clientSettingsMerchantHasToSupplyTrackingCode);
        $clientSettingsMerchant->setPriceResetLogic($clientSettingsMerchantPriceResetLogic);
        $clientSettingsMerchant->setAdjustMaxUpPercentage($clientSettingsMerchantAdjustMaxUpPercentage);
        $clientSettingsMerchant->setAdjustMaxDownPercentage($clientSettingsMerchantAdjustMaxDownPercentage);
        $clientSettingsMerchant->setSentToProductionDelay($clientSettingsMerchantSentToProductionDelay);

        $this->setClientSettings($clientSettingRepositoryMerchant, $clientSettingsMerchant);

        $this->testDetail('Get client settings for merchant again and compare results');
        $clientSettingResponseMerchant = $this->getClientSettings($clientSettingRepositoryMerchant);

        try {
            $this->compareValues('SentToProductionDelay', $clientSettingResponseMerchant['production']['sent_to_production_delay'], $clientSettingsMerchantSentToProductionDelay);
            $this->compareValues('HasToSupplyCarrier', $clientSettingResponseMerchant['shipping']['has_to_supply_carrier'], $clientSettingsMerchantHasToSupplyCarrier);
            $this->compareValues('HasToSupplyTrackingCode', $clientSettingResponseMerchant['shipping']['has_to_supply_tracking_code'], $clientSettingsMerchantHasToSupplyTrackingCode);
            $this->compareValues('PriceResetLogic', $clientSettingResponseMerchant['price_rules']['price_reset_logic'], $clientSettingsMerchantPriceResetLogic);
            $this->compareValues('AdjustMaxUpPercentage', $clientSettingResponseMerchant['price_rules']['adjust_max_up_percentage'], $clientSettingsMerchantAdjustMaxUpPercentage);
            $this->compareValues('AdjustMaxDownPercentage', $clientSettingResponseMerchant['price_rules']['adjust_max_down_percentage'], $clientSettingsMerchantAdjustMaxDownPercentage);
            $this->compareValues('ActivateNewFulfiller', $clientSettingResponseMerchant['automatisms']['activate_new_fulfillers'], $clientSettingsMerchantActivateNewFulfiller);
            $this->compareValues('ActivateNewProducts', $clientSettingResponseMerchant['automatisms']['activate_new_products'], $clientSettingsMerchantActivateNewProducts);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->testDetail('Patch client settings for merchant - V2');
        $clientSettingsMerchant = new \MyPromo\Connect\SDK\Models\Client\SettingsMerchant();

        $clientSettingsMerchantActivateNewFulfiller = false;
        $clientSettingsMerchantActivateNewProducts = false;
        $clientSettingsMerchantHasToSupplyCarrier = false;
        $clientSettingsMerchantHasToSupplyTrackingCode = false;
        $clientSettingsMerchantPriceResetLogic = 0;
        $clientSettingsMerchantAdjustMaxUpPercentage = 1;
        $clientSettingsMerchantAdjustMaxDownPercentage = 1;
        $clientSettingsMerchantSentToProductionDelay = 0;

        $clientSettingsMerchant->setActivateNewFulfiller($clientSettingsMerchantActivateNewFulfiller);
        $clientSettingsMerchant->setActivateNewProducts($clientSettingsMerchantActivateNewProducts);
        $clientSettingsMerchant->setHasToSupplyCarrier($clientSettingsMerchantHasToSupplyCarrier);
        $clientSettingsMerchant->setHasToSupplyTrackingCode($clientSettingsMerchantHasToSupplyTrackingCode);
        $clientSettingsMerchant->setPriceResetLogic($clientSettingsMerchantPriceResetLogic);
        $clientSettingsMerchant->setAdjustMaxUpPercentage($clientSettingsMerchantAdjustMaxUpPercentage);
        $clientSettingsMerchant->setAdjustMaxDownPercentage($clientSettingsMerchantAdjustMaxDownPercentage);
        $clientSettingsMerchant->setSentToProductionDelay($clientSettingsMerchantSentToProductionDelay);

        $this->setClientSettings($clientSettingRepositoryMerchant, $clientSettingsMerchant);


        $this->testDetail('Get client settings for merchant again and compare results');
        $clientSettingResponseMerchant = $this->getClientSettings($clientSettingRepositoryMerchant);

        try {
            $this->compareValues('SentToProductionDelay', $clientSettingResponseMerchant['production']['sent_to_production_delay'], $clientSettingsMerchantSentToProductionDelay);
            $this->compareValues('HasToSupplyCarrier', $clientSettingResponseMerchant['shipping']['has_to_supply_carrier'], $clientSettingsMerchantHasToSupplyCarrier);
            $this->compareValues('HasToSupplyTrackingCode', $clientSettingResponseMerchant['shipping']['has_to_supply_tracking_code'], $clientSettingsMerchantHasToSupplyTrackingCode);
            $this->compareValues('PriceResetLogic', $clientSettingResponseMerchant['price_rules']['price_reset_logic'], $clientSettingsMerchantPriceResetLogic);
            $this->compareValues('AdjustMaxUpPercentage', $clientSettingResponseMerchant['price_rules']['adjust_max_up_percentage'], $clientSettingsMerchantAdjustMaxUpPercentage);
            $this->compareValues('AdjustMaxDownPercentage', $clientSettingResponseMerchant['price_rules']['adjust_max_down_percentage'], $clientSettingsMerchantAdjustMaxDownPercentage);
            $this->compareValues('ActivateNewFulfiller', $clientSettingResponseMerchant['automatisms']['activate_new_fulfillers'], $clientSettingsMerchantActivateNewFulfiller);
            $this->compareValues('ActivateNewProducts', $clientSettingResponseMerchant['automatisms']['activate_new_products'], $clientSettingsMerchantActivateNewProducts);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->testDetail('Get client settings for fulfillers');
        $this->getClientSettings($clientSettingRepositoryFulfiller);


        $this->testDetail('Patch client settings for fulfiller - V1');

        $clientSettingsFulfiller = new \MyPromo\Connect\SDK\Models\Client\SettingsFulfiller();
        $clientSettingsFulfillerHasToSupplyCarrier = false;
        $clientSettingsFulfillerHasToSupplyTrackingCode = false;

        $clientSettingsFulfiller->setHasToSupplyCarrier($clientSettingsFulfillerHasToSupplyCarrier);
        $clientSettingsFulfiller->setHasToSupplyTrackingCode($clientSettingsFulfillerHasToSupplyTrackingCode);

        $this->setClientSettings($clientSettingRepositoryFulfiller, $clientSettingsFulfiller);


        $this->testDetail('Get client settings for fulfiller again and compare results');
        $clientSettingResponseFulfiller = $this->getClientSettings($clientSettingRepositoryMerchant);

        try {
            $this->compareValues('HasToSupplyCarrier', $clientSettingResponseFulfiller['shipping']['has_to_supply_carrier'], $clientSettingsFulfillerHasToSupplyCarrier);
            $this->compareValues('HasToSupplyTrackingCode', $clientSettingResponseFulfiller['shipping']['has_to_supply_tracking_code'], $clientSettingsFulfillerHasToSupplyTrackingCode);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->testDetail('Patch client settings for fulfiller - V1');

        $clientSettingsFulfiller = new \MyPromo\Connect\SDK\Models\Client\SettingsFulfiller();
        $clientSettingsFulfillerHasToSupplyCarrier = true;
        $clientSettingsFulfillerHasToSupplyTrackingCode = true;

        $clientSettingsFulfiller->setHasToSupplyCarrier($clientSettingsFulfillerHasToSupplyCarrier);
        $clientSettingsFulfiller->setHasToSupplyTrackingCode($clientSettingsFulfillerHasToSupplyTrackingCode);

        $this->setClientSettings($clientSettingRepositoryFulfiller, $clientSettingsFulfiller);


        $this->testDetail('Get client settings for fulfiller again and compare results');
        $clientSettingResponseFulfiller = $this->getClientSettings($clientSettingRepositoryMerchant);

        try {
            $this->compareValues('HasToSupplyCarrier', $clientSettingResponseFulfiller['shipping']['has_to_supply_carrier'], $clientSettingsFulfillerHasToSupplyCarrier);
            $this->compareValues('HasToSupplyTrackingCode', $clientSettingResponseFulfiller['shipping']['has_to_supply_tracking_code'], $clientSettingsFulfillerHasToSupplyTrackingCode);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


    }


    private function getClientSettings(ClientSettingRepository $clientSettingRepository)
    {
        $this->testDetail('Get client settings');

        try {
            $clientSettingResponse = $clientSettingRepository->getSettings();
            $this->printApiResponse(print_r($clientSettingResponse, true));

            return $clientSettingResponse;

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }
    }

    private function setClientSettings(ClientSettingRepository $clientSettingRepository, $clientSettings)
    {
        try {
            $clientSettingResponse = $clientSettingRepository->getSettings($clientSettings);
            $this->printApiResponse(print_r($clientSettingResponse, true));

            return $clientSettingResponse;

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO just commented out cause of an api issue - revert, when CO-2313 is done
            #return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }
    }


    /*
     * testClientConnectorsShopify
     */
    public function testClientConnectorsShopify()
    {
        $this->startMessage('Client connectors test start......');
        $clientConnectorRepositoryMerchant = new ClientConnectorRepository($this->clientMerchant);
        $clientConnectorRepositoryFulfiller = new ClientConnectorRepository($this->clientFulfiller);


        $this->testDetail('Get client connectors for merchant');
        $this->getClientConnectorSettings($clientConnectorRepositoryMerchant);


        $this->testDetail('Patch client connectors for merchant to shopify - V1');

        $clientConnector = new \MyPromo\Connect\SDK\Models\Client\Connector();

        //$clientConnectorConnectorId->setConnectorKey = 14;
        $clientConnectorConnectorKey = "magento_sales_channel";
        $clientConnectorTarget = "sales_channel";
        $shopifyConfigurationsShopName = "Shop Name";
        $shopifyConfigurationsToken = "shpat_1234567890";
        $shopifyConfigurationsShopUrl = "mypromo-demo.myshopify.com";
        $shopifyConfigurationsSalePriceConfig = "use_sales_price";
        $shopifyConfigurationsShopCurrency = "EUR";
        $shopifyConfigurationsProductsLanguage = "DE";
        $shopifyConfigurationsSyncProductSettings = "normal";
        $shopifyConfigurationsCreateCollections = true;
        $shopifyConfigurationsUpdateImages = true;
        $shopifyConfigurationsUpdateProducts = true;
        $shopifyConfigurationsUpdateSeo = true;
        $shopifyConfigurationsRecreateDeletedCollection = true;
        $shopifyConfigurationsRecreateDeletedProducts = true;
        $shopifyConfigurationsAddNewProductsAutomatically = true;
        $shopifyConfigurationsUseMegaMenu = true;

        //$clientConnector->setConnectorId($clientConnectorConnectorId);
        $clientConnector->setConnectorKey($clientConnectorConnectorKey);
        $clientConnector->setTarget($clientConnectorTarget);

        $shopifyConfigurations = new \MyPromo\Connect\SDK\Models\Client\ConnectorConfigurationShopify();
        $shopifyConfigurations->setShopName($shopifyConfigurationsShopName);
        $shopifyConfigurations->setToken($shopifyConfigurationsToken);
        $shopifyConfigurations->setShopUrl($shopifyConfigurationsShopUrl);
        $shopifyConfigurations->setSalePriceConfig($shopifyConfigurationsSalePriceConfig);
        $shopifyConfigurations->setShopCurrency($shopifyConfigurationsShopCurrency);
        $shopifyConfigurations->setProductsLanguage($shopifyConfigurationsProductsLanguage);
        $shopifyConfigurations->setSyncProductSettings($shopifyConfigurationsSyncProductSettings);
        $shopifyConfigurations->setCreateCollections($shopifyConfigurationsCreateCollections);
        $shopifyConfigurations->setUpdateImages($shopifyConfigurationsUpdateImages);
        $shopifyConfigurations->setUpdateProducts($shopifyConfigurationsUpdateProducts);
        $shopifyConfigurations->setUpdateSeo($shopifyConfigurationsUpdateSeo);
        $shopifyConfigurations->setRecreateDeletedCollection($shopifyConfigurationsRecreateDeletedCollection);
        $shopifyConfigurations->setRecreateDeletedProducts($shopifyConfigurationsRecreateDeletedProducts);
        $shopifyConfigurations->setAddNewProductsAutomatically($shopifyConfigurationsAddNewProductsAutomatically);
        $shopifyConfigurations->setUseMegaMenu($shopifyConfigurationsUseMegaMenu);

        // TODO - settings not saved correctly anymore! see CO-2314
        // TODO - new settings added to sdk - after adding to api: test it!! see CO-2315

        $clientConnector->setConfiguration($shopifyConfigurations);

        $this->setClientConnectorSettings($clientConnectorRepositoryMerchant, $clientConnector);


        $this->testDetail('Get client connector settings for merchant again and compare results');
        $clientConnectorsResponse = $this->getClientConnectorSettings($clientConnectorRepositoryMerchant, null, null, $clientConnectorTarget);

        try {
            $this->compareValues('ConnectorKey', $clientConnectorsResponse['data'][0]['connector_key'], $clientConnectorConnectorKey);
            $this->compareValues('Target', $clientConnectorsResponse['data'][0]['target'], $clientConnectorTarget);

            $this->compareValues('ShopName', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_name'], $shopifyConfigurationsShopName);
            $this->compareValues('Token', $clientConnectorsResponse['data'][0]['configuration']['spy_token'], $shopifyConfigurationsToken);
            $this->compareValues('ShopUrl', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_url'], $shopifyConfigurationsShopUrl);
            $this->compareValues('SalePriceConfig', $clientConnectorsResponse['data'][0]['configuration']['spy_sales_price_config'], $shopifyConfigurationsSalePriceConfig);
            $this->compareValues('ShopCurrency', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_currency'], $shopifyConfigurationsShopCurrency);
            $this->compareValues('ProductsLanguage', $clientConnectorsResponse['data'][0]['configuration']['spy_products_language'], $shopifyConfigurationsProductsLanguage);
            $this->compareValues('SyncProductSettings', $clientConnectorsResponse['data'][0]['configuration']['spy_sync_products_settings'], $shopifyConfigurationsSyncProductSettings);
            $this->compareValues('CreateCollections', $clientConnectorsResponse['data'][0]['configuration']['spy_create_collections'], $shopifyConfigurationsCreateCollections);
            $this->compareValues('UpdateImages', $clientConnectorsResponse['data'][0]['configuration']['spy_update_images'], $shopifyConfigurationsUpdateImages);
            $this->compareValues('UpdateProducts', $clientConnectorsResponse['data'][0]['configuration']['spy_update_products'], $shopifyConfigurationsUpdateProducts);
            $this->compareValues('UpdateSeo', $clientConnectorsResponse['data'][0]['configuration']['spy_update_seo'], $shopifyConfigurationsUpdateSeo);
            $this->compareValues('RecreateDeletedCollection', $clientConnectorsResponse['data'][0]['configuration']['spy_recreate_deleted_collections'], $shopifyConfigurationsRecreateDeletedCollection);
            $this->compareValues('RecreateDeletedProducts', $clientConnectorsResponse['data'][0]['configuration']['spy_recreate_deleted_products'], $shopifyConfigurationsRecreateDeletedProducts);
            $this->compareValues('AddNewProductsAutomatically', $clientConnectorsResponse['data'][0]['configuration']['spy_add_new_products_automatically'], $shopifyConfigurationsAddNewProductsAutomatically);
            $this->compareValues('UseMegaMenu', $clientConnectorsResponse['data'][0]['configuration']['spy_use_mega_menu'], $shopifyConfigurationsUseMegaMenu);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->testDetail('Patch client connectors for merchant to shopify - V2');

        $clientConnector = new \MyPromo\Connect\SDK\Models\Client\Connector();

        //$clientConnectorConnectorId->setConnectorKey = 14;
        $clientConnectorConnectorKey = "magento_sales_channel";
        $clientConnectorTarget = "sales_channel";
        $shopifyConfigurationsShopName = "Shop Name B";
        $shopifyConfigurationsToken = "shpat_1234567890_B";
        $shopifyConfigurationsShopUrl = "mypromo-demoB.myshopify.com";
        $shopifyConfigurationsSalePriceConfig = "use_recommended_sales_price";
        $shopifyConfigurationsShopCurrency = "USD";
        $shopifyConfigurationsProductsLanguage = "EN";
        $shopifyConfigurationsSyncProductSettings = "all";
        $shopifyConfigurationsCreateCollections = false;
        $shopifyConfigurationsUpdateImages = false;
        $shopifyConfigurationsUpdateProducts = false;
        $shopifyConfigurationsUpdateSeo = false;
        $shopifyConfigurationsRecreateDeletedCollection = false;
        $shopifyConfigurationsRecreateDeletedProducts = false;
        $shopifyConfigurationsAddNewProductsAutomatically = false;
        $shopifyConfigurationsUseMegaMenu = false;

        //$clientConnector->setConnectorId($clientConnectorConnectorId);
        $clientConnector->setConnectorKey($clientConnectorConnectorKey);
        $clientConnector->setTarget($clientConnectorTarget);

        $shopifyConfigurations = new \MyPromo\Connect\SDK\Models\Client\ConnectorConfigurationShopify();
        $shopifyConfigurations->setShopName($shopifyConfigurationsShopName);
        $shopifyConfigurations->setToken($shopifyConfigurationsToken);
        $shopifyConfigurations->setShopUrl($shopifyConfigurationsShopUrl);
        $shopifyConfigurations->setSalePriceConfig($shopifyConfigurationsSalePriceConfig);
        $shopifyConfigurations->setShopCurrency($shopifyConfigurationsShopCurrency);
        $shopifyConfigurations->setProductsLanguage($shopifyConfigurationsProductsLanguage);
        $shopifyConfigurations->setSyncProductSettings($shopifyConfigurationsSyncProductSettings);
        $shopifyConfigurations->setCreateCollections($shopifyConfigurationsCreateCollections);
        $shopifyConfigurations->setUpdateImages($shopifyConfigurationsUpdateImages);
        $shopifyConfigurations->setUpdateProducts($shopifyConfigurationsUpdateProducts);
        $shopifyConfigurations->setUpdateSeo($shopifyConfigurationsUpdateSeo);
        $shopifyConfigurations->setRecreateDeletedCollection($shopifyConfigurationsRecreateDeletedCollection);
        $shopifyConfigurations->setRecreateDeletedProducts($shopifyConfigurationsRecreateDeletedProducts);
        $shopifyConfigurations->setAddNewProductsAutomatically($shopifyConfigurationsAddNewProductsAutomatically);
        $shopifyConfigurations->setUseMegaMenu($shopifyConfigurationsUseMegaMenu);

        // TODO - settings not saved correctly anymore! see CO-2314
        // TODO - new settings added to sdk - after adding to api: test it!! see CO-2315

        $clientConnector->setConfiguration($shopifyConfigurations);

        $this->setClientConnectorSettings($clientConnectorRepositoryMerchant, $clientConnector);


        $this->testDetail('Get client connector settings for merchant again and compare results');
        $clientConnectorsResponse = $this->getClientConnectorSettings($clientConnectorRepositoryMerchant, null, null, $clientConnectorTarget);

        try {
            $this->compareValues('ConnectorKey', $clientConnectorsResponse['data'][0]['connector_key'], $clientConnectorConnectorKey);
            $this->compareValues('Target', $clientConnectorsResponse['data'][0]['target'], $clientConnectorTarget);

            $this->compareValues('ShopName', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_name'], $shopifyConfigurationsShopName);
            $this->compareValues('Token', $clientConnectorsResponse['data'][0]['configuration']['spy_token'], $shopifyConfigurationsToken);
            $this->compareValues('ShopUrl', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_url'], $shopifyConfigurationsShopUrl);
            $this->compareValues('SalePriceConfig', $clientConnectorsResponse['data'][0]['configuration']['spy_sales_price_config'], $shopifyConfigurationsSalePriceConfig);
            $this->compareValues('ShopCurrency', $clientConnectorsResponse['data'][0]['configuration']['spy_shop_currency'], $shopifyConfigurationsShopCurrency);
            $this->compareValues('ProductsLanguage', $clientConnectorsResponse['data'][0]['configuration']['spy_products_language'], $shopifyConfigurationsProductsLanguage);
            $this->compareValues('SyncProductSettings', $clientConnectorsResponse['data'][0]['configuration']['spy_sync_products_settings'], $shopifyConfigurationsSyncProductSettings);
            $this->compareValues('CreateCollections', $clientConnectorsResponse['data'][0]['configuration']['spy_create_collections'], $shopifyConfigurationsCreateCollections);
            $this->compareValues('UpdateImages', $clientConnectorsResponse['data'][0]['configuration']['spy_update_images'], $shopifyConfigurationsUpdateImages);
            $this->compareValues('UpdateProducts', $clientConnectorsResponse['data'][0]['configuration']['spy_update_products'], $shopifyConfigurationsUpdateProducts);
            $this->compareValues('UpdateSeo', $clientConnectorsResponse['data'][0]['configuration']['spy_update_seo'], $shopifyConfigurationsUpdateSeo);
            $this->compareValues('RecreateDeletedCollection', $clientConnectorsResponse['data'][0]['configuration']['spy_recreate_deleted_collections'], $shopifyConfigurationsRecreateDeletedCollection);
            $this->compareValues('RecreateDeletedProducts', $clientConnectorsResponse['data'][0]['configuration']['spy_recreate_deleted_products'], $shopifyConfigurationsRecreateDeletedProducts);
            $this->compareValues('AddNewProductsAutomatically', $clientConnectorsResponse['data'][0]['configuration']['spy_add_new_products_automatically'], $shopifyConfigurationsAddNewProductsAutomatically);
            $this->compareValues('UseMegaMenu', $clientConnectorsResponse['data'][0]['configuration']['spy_use_mega_menu'], $shopifyConfigurationsUseMegaMenu);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


    }

    /*
     * testClientConnectorsMagento
     */
    public function testClientConnectorsMagento()
    {
        $this->startMessage('Client connectors test start......');
        $clientConnectorRepositoryMerchant = new ClientConnectorRepository($this->clientMerchant);
        $clientConnectorRepositoryFulfiller = new ClientConnectorRepository($this->clientFulfiller);


        $this->testDetail('Get client connectors for merchant');
        $this->getClientConnectorSettings($clientConnectorRepositoryMerchant);


        $this->testDetail('Patch client connectors for merchant to magento - V1');

        $clientConnector = new \MyPromo\Connect\SDK\Models\Client\Connector();

        //$clientConnectorConnectorId->setConnectorKey = 12;
        $clientConnectorConnectorKey = 'magento_sales_channel';
        $clientConnectorTarget = 'sales_channel';

        $magentoConfigurationsInstanceUrl = 'url';
        $magentoConfigurationsApiUsername = 'username';
        $magentoConfigurationsApiPassword = 'password';
        $magentoConfigurationsWebsiteCode = 'XXXX';
        $magentoConfigurationsWebsiteCodeId = 1;
        $magentoConfigurationsWebsiteCodeName = 'Website name';
        $magentoConfigurationsStoreCode = 'XXXXX';
        $magentoConfigurationsStoreCodeId = 1;
        $magentoConfigurationsStoreCodeName = 'Store code';
        $magentoConfigurationsSyncProductsSettings = 'normal';
        $magentoConfigurationsSalesPriceConfig = 'use_sales_price';

        //$clientConnector->setConnectorId($clientConnectorConnectorId);
        $clientConnector->setConnectorKey($clientConnectorConnectorKey);
        $clientConnector->setTarget($clientConnectorTarget);

        $magentoConfigurations = new \MyPromo\Connect\SDK\Models\Client\ConnectorConfigurationMagento();
        $magentoConfigurations->setInstanceUrl($magentoConfigurationsInstanceUrl);
        $magentoConfigurations->setApiUsername($magentoConfigurationsApiUsername);
        $magentoConfigurations->setApiPassword($magentoConfigurationsApiPassword);
        $magentoConfigurations->setWebsiteCode($magentoConfigurationsWebsiteCode);
        $magentoConfigurations->setWebsiteCodeId($magentoConfigurationsWebsiteCodeId);
        $magentoConfigurations->setWebsiteCodeName($magentoConfigurationsWebsiteCodeName);
        $magentoConfigurations->setStoreCode($magentoConfigurationsStoreCode);
        $magentoConfigurations->setStoreCodeId($magentoConfigurationsStoreCodeId);
        $magentoConfigurations->setStoreCodeName($magentoConfigurationsStoreCodeName);
        $magentoConfigurations->setSyncProductsSettings($magentoConfigurationsSyncProductsSettings);
        $magentoConfigurations->setSalesPriceConfig($magentoConfigurationsSalesPriceConfig);

        // TODO - settings not saved correctly anymore! see CO-2314
        // TODO - new settings added to sdk - after adding to api: test it!! see CO-2315

        $clientConnector->setConfiguration($magentoConfigurations);

        $this->setClientConnectorSettings($clientConnectorRepositoryMerchant, $clientConnector);


        $this->testDetail('Get client connector settings for merchant again and compare results');
        $clientConnectorsResponse = $this->getClientConnectorSettings($clientConnectorRepositoryMerchant, null, null, $clientConnectorTarget);

        try {
            $this->compareValues('ConnectorKey', $clientConnectorsResponse['data'][0]['connector_key'], $clientConnectorConnectorKey);
            $this->compareValues('Target', $clientConnectorsResponse['data'][0]['target'], $clientConnectorTarget);

            $this->compareValues('InstanceUrl', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_instance_url'], $magentoConfigurationsInstanceUrl);
            $this->compareValues('ApiUsername', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_api_username'], $magentoConfigurationsApiUsername);
            $this->compareValues('ApiPassword', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_api_password'], $magentoConfigurationsApiPassword);
            $this->compareValues('WebsiteCode', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code'], $magentoConfigurationsWebsiteCode);
            $this->compareValues('WebsiteCodeId', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code_id'], $magentoConfigurationsWebsiteCodeId);
            $this->compareValues('WebsiteCodeName', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code_name'], $magentoConfigurationsWebsiteCodeName);
            $this->compareValues('StoreCode', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code'], $magentoConfigurationsStoreCode);
            $this->compareValues('StoreCodeId', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code_id'], $magentoConfigurationsStoreCodeId);
            $this->compareValues('StoreCodeName', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code_name'], $magentoConfigurationsStoreCodeName);
            $this->compareValues('SyncProductsSettings', $clientConnectorsResponse['data'][0]['configuration']['sync_products_settings'], $magentoConfigurationsSyncProductsSettings);
            $this->compareValues('SalesPriceConfig', $clientConnectorsResponse['data'][0]['configuration']['sales_price_config'], $magentoConfigurationsSalesPriceConfig);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->testDetail('Patch client connectors for merchant to magento - V2');

        $clientConnector = new \MyPromo\Connect\SDK\Models\Client\Connector();

        //$clientConnectorConnectorId->setConnectorKey = 12;
        $clientConnectorConnectorKey = 'magento_sales_channel';
        $clientConnectorTarget = 'sales_channel';

        $magentoConfigurationsInstanceUrl = 'urlB';
        $magentoConfigurationsApiUsername = 'usernameB';
        $magentoConfigurationsApiPassword = 'passwordB';
        $magentoConfigurationsWebsiteCode = 'XXXXB';
        $magentoConfigurationsWebsiteCodeId = 2;
        $magentoConfigurationsWebsiteCodeName = 'Website nameB';
        $magentoConfigurationsStoreCode = 'XXXXXB';
        $magentoConfigurationsStoreCodeId = 2;
        $magentoConfigurationsStoreCodeName = 'Store codeB';
        $magentoConfigurationsSyncProductsSettings = 'all';
        $magentoConfigurationsSalesPriceConfig = 'use_buying_price';

        //$clientConnector->setConnectorId($clientConnectorConnectorId);
        $clientConnector->setConnectorKey($clientConnectorConnectorKey);
        $clientConnector->setTarget($clientConnectorTarget);

        $magentoConfigurations = new \MyPromo\Connect\SDK\Models\Client\ConnectorConfigurationMagento();
        $magentoConfigurations->setInstanceUrl($magentoConfigurationsInstanceUrl);
        $magentoConfigurations->setApiUsername($magentoConfigurationsApiUsername);
        $magentoConfigurations->setApiPassword($magentoConfigurationsApiPassword);
        $magentoConfigurations->setWebsiteCode($magentoConfigurationsWebsiteCode);
        $magentoConfigurations->setWebsiteCodeId($magentoConfigurationsWebsiteCodeId);
        $magentoConfigurations->setWebsiteCodeName($magentoConfigurationsWebsiteCodeName);
        $magentoConfigurations->setStoreCode($magentoConfigurationsStoreCode);
        $magentoConfigurations->setStoreCodeId($magentoConfigurationsStoreCodeId);
        $magentoConfigurations->setStoreCodeName($magentoConfigurationsStoreCodeName);
        $magentoConfigurations->setSyncProductsSettings($magentoConfigurationsSyncProductsSettings);
        $magentoConfigurations->setSalesPriceConfig($magentoConfigurationsSalesPriceConfig);

        // TODO - settings not saved correctly anymore! see CO-2314
        // TODO - new settings added to sdk - after adding to api: test it!! see CO-2315

        $clientConnector->setConfiguration($magentoConfigurations);

        $this->setClientConnectorSettings($clientConnectorRepositoryMerchant, $clientConnector);


        $this->testDetail('Get client connector settings for merchant again and compare results');
        $clientConnectorsResponse = $this->getClientConnectorSettings($clientConnectorRepositoryMerchant, null, null, $clientConnectorTarget);

        try {
            $this->compareValues('ConnectorKey', $clientConnectorsResponse['data'][0]['connector_key'], $clientConnectorConnectorKey);
            $this->compareValues('Target', $clientConnectorsResponse['data'][0]['target'], $clientConnectorTarget);

            $this->compareValues('InstanceUrl', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_instance_url'], $magentoConfigurationsInstanceUrl);
            $this->compareValues('ApiUsername', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_api_username'], $magentoConfigurationsApiUsername);
            $this->compareValues('ApiPassword', $clientConnectorsResponse['data'][0]['configuration']['magento_connector_api_password'], $magentoConfigurationsApiPassword);
            $this->compareValues('WebsiteCode', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code'], $magentoConfigurationsWebsiteCode);
            $this->compareValues('WebsiteCodeId', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code_id'], $magentoConfigurationsWebsiteCodeId);
            $this->compareValues('WebsiteCodeName', $clientConnectorsResponse['data'][0]['configuration']['magento_website_code_name'], $magentoConfigurationsWebsiteCodeName);
            $this->compareValues('StoreCode', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code'], $magentoConfigurationsStoreCode);
            $this->compareValues('StoreCodeId', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code_id'], $magentoConfigurationsStoreCodeId);
            $this->compareValues('StoreCodeName', $clientConnectorsResponse['data'][0]['configuration']['magento_store_code_name'], $magentoConfigurationsStoreCodeName);
            $this->compareValues('SyncProductsSettings', $clientConnectorsResponse['data'][0]['configuration']['sync_products_settings'], $magentoConfigurationsSyncProductsSettings);
            $this->compareValues('SalesPriceConfig', $clientConnectorsResponse['data'][0]['configuration']['sales_price_config'], $magentoConfigurationsSalesPriceConfig);
        } catch (\Exception $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
        }


        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        $this->warn('We just have helpers for magento and shopify - add configuration helpers for all other connectors !!!');
        $this->warn('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

    }

    private function getClientConnectorSettings(ClientConnectorRepository $clientConnectorRepository, $ConnectorId = null, $ConnectorKey = null, $Target = null)
    {
        $clientConnectorOptions = new \MyPromo\Connect\SDK\Helpers\Client\ConnectorOptions();
        $clientConnectorOptions->setPage(1); // get data from this page number
        $clientConnectorOptions->setPerPage(15);
        $clientConnectorOptions->setPagination(false);

        ($ConnectorId != null) ? $clientConnectorOptions->setConnectorId($ConnectorId) : $clientConnectorOptions->setConnectorId(null);;
        ($ConnectorKey != null) ? $clientConnectorOptions->setConnectorKey($ConnectorKey) : $clientConnectorOptions->setConnectorKey(null);;
        ($Target != null) ? $clientConnectorOptions->setTarget($Target) : $clientConnectorOptions->setTarget(null);;


        try {
            $clientConnectorsResponseMerchant = $clientConnectorRepository->all($clientConnectorOptions);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));

            return $clientConnectorsResponseMerchant;

        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }
    }

    private function setClientConnectorSettings(ClientConnectorRepository $clientConnectorRepository, $clientConnector)
    {
        $this->printApiResponse(print_r($clientConnector->toArray(), true));

        try {
            $clientConnectorsResponseMerchant = $clientConnectorRepository->update($clientConnector);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));

            return $clientConnectorsResponseMerchant;

        } catch (ApiResponseException | InputValidationException $e) {

            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }
    }


    /*
     * testClientJobsSalesChannel
     */
    public function testClientJobsSalesChannel($job)
    {
        $this->startMessage('testClientJobsSalesChannel');
        $connectorJobRepository = new JobRepository($this->clientMerchant);


        $this->testDetail('Create a sales channel sync job');

        $connectorJob = new \MyPromo\Connect\SDK\Models\Jobs\Job();
        $connectorJob->setTarget('sales_channel');

        $filters = new \MyPromo\Connect\SDK\Models\Jobs\JobFilters();
        $filters->setJob($job);
        $filters->setFulfiller(null);
        $filters->setProducts(null);
        $filters->setTestProduct(false);
        $connectorJob->setFilters($filters);

        $callback = new \MyPromo\Connect\SDK\Models\Callback();
        $callback->setUrl(config('connect.callback_url'));
        $connectorJob->setCallback($callback);


        try {
            $clientConnectorsResponseMerchant = $connectorJobRepository->create($connectorJob);
            $this->printApiResponse(print_r($clientConnectorsResponseMerchant, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());

            // TODO CO-2326
            //print_r($connectorJob);

            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

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
        $design->setSku(config('connect.test_sku_child'));
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Submit Design
        try {
            $this->info('Submitting design....');
            $designRepository->submit($design->getId());
            $this->info('Design submitted successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Get Preview
        try {
            $this->info('Trying to get preview.....');
            $designRepository->getPreviewPDF($design->getId());
            $this->info('Preview received successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        // Save Preview
        try {
            $this->info('Trying preview save .....');
            $designRepository->savePreview($design->getId(), 'preview.pdf');
            $this->info('Preview saved successfully!');
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
        $design->setSku(config('connect.test_sku_child'));
        $design->setIntent(config('connect.test_sku_child_intent'));
        $design->setQuantity(10);

        try {
            $this->info('Create a design with the design user');
            $designResponse = $designRepository->create($design);
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->info("Editor start URL : " . $designResponse['editor_start_url']);

        try {
            $this->info('Submit the design');
            $designResponse = $designRepository->submit($design->getId());
            $this->printApiResponse(print_r($designResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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


        $order = new \MyPromo\Connect\SDK\Models\Orders\Order();
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        dd('go on writing tests - add items - submit order - get order etc.');


        $this->info('Add order item with design');

        $orderItem = new \MyPromo\Connect\SDK\Models\Orders\OrderItem();
        $orderItem->setOrderId($order->getId());
        $orderItem->setQuantity(35);
        $orderItem->setReference('your-reference');
        $orderItem->setComment('comment for order item here');

        $orderItem->setDesigns($design);


        $design->getId();


        $this->info('Add order item with file');

        $orderItem = new \MyPromo\Connect\SDK\Models\Orders\OrderItem();
        $orderItem->setOrderId($order->getId());
        $orderItem->setReference('your-reference');
        $orderItem->setQuantity(config('connect.test_sku_qty'));
        $orderItem->setSku(config('connect.test_sku_child'));
        $orderItem->setComment('comment for order item here');

        # To add service item mention order_item_id in relation
        $orderItemRelation = new \MyPromo\Connect\SDK\Models\Orders\OrderItemRelation();
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Request data of all exports...');

        $productExportOptions = new \MyPromo\Connect\SDK\Helpers\ProductFeeds\ExportOptions();
        $productExportOptions->setPage(1); // get data from this page number
        $productExportOptions->setPerPage(5);
        $productExportOptions->setPagination(false);
        $productExportOptions->setCreatedFrom(new \DateTime(date('Y-m-d H:i:s')));
        $productExportOptions->setCreatedTo(new \DateTime(date('Y-m-d H:i:s')));

        $this->printApiResponse(print_r($productExportOptions->toArray(), true));

        try {
            $requestExportAllResponse = $requestExportRepository->all($productExportOptions);
            $this->printApiResponse(print_r($requestExportAllResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        // TODO - CO-2321
        // TODO - fetch all exports with status SUCCESS to perform a download test (if result, else show warn message)
        if (isset($requestExportAllResponse['data'][0]['files']['export']['url'])) {
            $downloadFileUrl = $requestExportAllResponse['data'][0]['files']['export']['url'];
            $filename = 'download_generic-label-' . date('Ymd_His') . '.pdf';
            $this->downloadFile($downloadFileUrl, $filename);
        } else {
            $this->error('Unable to test download of generic label. Could not generate label due to missing production order.');
        }


        $this->testDetail('Requesting new export... Cancel test');
        $productExport = $this->createExport($requestExportRepository);
        try {
            $this->info('Trying to cancel...');
            $requestExportByIdResponse = $requestExportRepository->cancel($productExport->getId());
            $this->printApiResponse(print_r($requestExportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            //return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Requesting new export... Delete test');
        $productExport = $this->createExport($requestExportRepository);
        try {
            $this->info('Trying to delete...');
            $requestExportByIdResponse = $requestExportRepository->delete($productExport->getId());
            $this->printApiResponse(print_r($requestExportByIdResponse, 1));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            //return 0; // TODO - is always failing cause its depending on status of job
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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

        $productExport = new \MyPromo\Connect\SDK\Models\ProductFeeds\Export();

        $productExport->setTempletaId(null);
        $productExport->setTempletaKey('prices');
        $productExport->setFormat('xlsx');

        $productExportFilterOptions = new \MyPromo\Connect\SDK\Models\ProductFeeds\ExportFilters();
        $productExportFilterOptions->setCategoryId(null);
        $productExportFilterOptions->setCurrency('EUR');
        $productExportFilterOptions->setLang('DE');
        $productExportFilterOptions->setProductTypes($productExportFilterOptions::ProductExportFilterOptionsProductTypeAll);
        $productExportFilterOptions->setSearch(null);
        //$productExportFilterOptions->setSku(config('connect.test_sku_child'));
        $productExportFilterOptions->setShippingFrom('DE');
        $productExport->setFilters($productExportFilterOptions);

        $callback = new \MyPromo\Connect\SDK\Models\Callback();
        $callback->setUrl(config('connect.callback_url'));
        $productExport->setCallback($callback);

        try {
            $this->info('Sending Export Request');

            $requestExportResponse = $requestExportRepository->create($productExport);
            $this->printApiResponse(print_r($requestExportResponse, 1));

            if ($productExport->getId()) {
                $this->info('Export with ID ' . $productExport->getId() . ' created successfully!');
            }
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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

        if ($productImport != false) {

            try {
                $requestImportByIdResponse = $requestImportRepository->find($productImport->getId());
                $this->printApiResponse(print_r($requestImportByIdResponse, 1));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }


            // TODO - Bug see CO-2319
            $this->testDetail('Trying to download the original file...');

            $downloadFileUrl = $requestImportByIdResponse['files']['original']['url'];
            $filename = 'download_import_original-' . date('Ymd_His') . '.xlsx';
            $this->downloadFile($downloadFileUrl, $filename);


            $this->testDetail('Request data of all imports...');

            try {
                $productImportOptions = new \MyPromo\Connect\SDK\Helpers\ProductFeeds\ImportOptions();
                $productImportOptions->setPage(1); // get data from this page number
                $productImportOptions->setPerPage(5);
                $productImportOptions->setPagination(false);
                $productImportOptions->setCreatedFrom(new \DateTime(date('Y-m-d H:i:s')));
                $productImportOptions->setCreatedTo(new \DateTime(date('Y-m-d H:i:s')));

                $this->printApiResponse(print_r($productImportOptions->toArray(), true));

                $requestImportAllResponse = $requestImportRepository->all($productImportOptions);

                $this->printApiResponse(print_r($requestImportAllResponse, 1));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }


            $this->testDetail('Requesting new import... Cancel test');
            $productImport = $this->createImport($requestImportRepository);
            try {
                $this->info('Trying to cancel...');
                $requestImportByIdResponse = $requestImportRepository->cancel($productImport->getId());
                $this->printApiResponse(print_r($requestImportByIdResponse, 1));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                //return 0; // TODO - is always failing cause its depending on status of job
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }


            $this->testDetail('Requesting new import... Delete test');
            $productImport = $this->createImport($requestImportRepository);
            try {
                $this->info('Trying to delete...');
                $requestImportByIdResponse = $requestImportRepository->delete($productImport->getId());
                $this->printApiResponse(print_r($requestImportByIdResponse, 1));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                #return 0; // TODO - is always failing cause its depending on status of job
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
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
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                #return 0; // TODO - is always failing cause its depending on status of job
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }


            // TODO
            // confirm import (needs body data...)
        } else {
            $this->error('Unable to perform all import tests!');
        }
    }

    public function createImport(\MyPromo\Connect\SDK\Repositories\ProductFeeds\ProductImportRepository $requestImportRepository)
    {
        $productImport = new \MyPromo\Connect\SDK\Models\ProductFeeds\Import();
        $productImport->setTempletaId(null);
        $productImport->setTempletaKey('prices');
        $productImport->setDryRun(false);
        $productImport->setDateExecute(null);

        $productImportInput = new \MyPromo\Connect\SDK\Models\ProductFeeds\ImportInput();

        $productImportInput->setUrl('https://downloads.test.mypromo.com/feeds/Merchant-Prices.xlsx');
        #$productImportInput->setUrl('https://mypromo-shopify-dev.s3.eu-central-1.amazonaws.com/1651047033.xlsx?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAQYOR5ZFDEHKX74RD%2F20220427%2Feu-central-1%2Fs3%2Faws4_request&X-Amz-Date=20220427T081034Z&X-Amz-SignedHeaders=host&X-Amz-Expires=604800&X-Amz-Signature=9ad5635d5f2b842d96af495d69e93c286cc3b460f45f5ce6ea953b38b93f5e68');
        $productImportInput->setFormat('xlsx');

        $productImport->setInput($productImportInput);

        $callback = new \MyPromo\Connect\SDK\Models\Callback();
        $callback->setUrl(config('connect.callback_url'));
        $productImport->setCallback($callback);

        try {
            $this->info('Sending Import Request');

            $requestImportResponse = $requestImportRepository->create($productImport);
            $this->printApiResponse(print_r($requestImportResponse, 1));

            if ($productImport->getId()) {
                $this->info('Import with ID ' . $productImport->getId() . ' created successfully!');
            }
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
        $productsOptions = new \MyPromo\Connect\SDK\Helpers\Products\ProductOptions();
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }

        $this->testDetail('get data of a single product');

        if (!empty($productsResponse['data'][0]['id'])) {
            $this->info('Getting first product of previous result');
            $productId = $productsResponse['data'][0]['id'];

            // TODO - needs to get own helper object...
            $this->warn('Product Options for get by id needs to get own helper object');
            $productsOptions = new \MyPromo\Connect\SDK\Helpers\Products\ProductOptions();
            $productsOptions->setLang("DE");
            $productsOptions->setIncludeVariants(true);

            try {
                $productResponseSingleObj = $productsRepositoryMerchant->find($productId, $productsOptions);
                $this->printApiResponse(print_r($productResponseSingleObj, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->error('Unable to perform test, cause there are no products.');
        }


        $this->testDetail('get variants of a product');

        if (!empty($productsResponse['data'][0]['variants']['id'])) {
            $this->info('Getting variants of product of previous result');
            $productVariantId = $productsResponse['data'][0]['variants']['id'];

            $productVariantOptions = new \MyPromo\Connect\SDK\Helpers\Products\ProductVariantOptions();
            $productVariantOptions->setPage(1);
            $productVariantOptions->setPerPage(5);
            $productVariantOptions->setPagination(false);
            $productVariantOptions->setLang("DE");
            $productVariantOptions->setId($productVariantId);
            //$productVariantOptions->setSku(config('connect.test_sku_child'));
            $productVariantOptions->setReference(null);

            try {
                $productResponseSingleObj = $productsRepositoryMerchant->getVariants($productVariantOptions);
                $this->printApiResponse(print_r($productResponseSingleObj, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->error('Unable to perform test, cause there are no variants in product given.');
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

        $priceOptionsMerchant = new \MyPromo\Connect\SDK\Helpers\Products\PriceOptionsMerchant();
        $priceOptionsMerchant->setPage(1);
        $priceOptionsMerchant->setPerPage(5);
        $priceOptionsMerchant->setPagination(false);
        $priceOptionsMerchant->setShippingFrom('DE');
        //$priceOptionsMerchant->setSku(config('connect.test_sku_child'));

        try {
            $productsResponse = $productsRepositoryMerchant->getPrices($priceOptionsMerchant);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get prices for client type fulfiller');

        $priceOptionsFulfiller = new \MyPromo\Connect\SDK\Helpers\Products\PriceOptionsFulfiller();
        $priceOptionsFulfiller->setPage(1);
        $priceOptionsFulfiller->setPerPage(5);
        $priceOptionsFulfiller->setPagination(false);
        //$priceOptionsFulfiller->setSkuFulfiller(config('connect.test_sku_child'));

        try {
            $productsResponse = $productsRepositoryFulfiller->getPrices($priceOptionsFulfiller);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get inventory for client type merchant');

        $inventoryOptionsMerchant = new \MyPromo\Connect\SDK\Helpers\Products\InventoryOptionsMerchant();
        $inventoryOptionsMerchant->setPage(1);
        $inventoryOptionsMerchant->setPerPage(5);
        $inventoryOptionsMerchant->setPagination(false);
        $inventoryOptionsMerchant->setShippingFrom('DE');
        //$inventoryOptionsMerchant->setSku(config('connect.test_sku_child'));


        try {
            $productsResponse = $productsRepositoryMerchant->getInventory($inventoryOptionsMerchant);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get inventory for client type fulfiller');

        $inventoryOptionsFulfiller = new \MyPromo\Connect\SDK\Helpers\Products\InventoryOptionsFulfiller();
        $inventoryOptionsFulfiller->setPage(1);
        $inventoryOptionsFulfiller->setPerPage(5);
        $inventoryOptionsFulfiller->setPagination(false);
        //$inventoryOptionsMerchant->setSkuFulfiller(config('connect.test_sku_child'));


        try {
            $productsResponse = $productsRepositoryFulfiller->getInventory($inventoryOptionsFulfiller);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get seo overwrites for client type merchant');

        $seoOptions = new \MyPromo\Connect\SDK\Helpers\Products\SeoOptions();
        $seoOptions->setPage(1);
        $seoOptions->setPerPage(5);
        $seoOptions->setPagination(false);
        //$seoOptions->setSku(config('connect.test_sku_child'));

        try {
            $productsResponse = $productsRepositoryMerchant->getSeo($seoOptions);
            $this->printApiResponse(print_r($productsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
        $this->startMessage('testProductConfigurator');

        $clientId = config('connect.client_merchant_id');


        $this->testDetail('Get all categories for a client');
        $categoriesRepository = new \MyPromo\Connect\SDK\Repositories\Configurator\CategoriesRepository($this->clientMerchant);

        $configuratorCategoriesOptions = new \MyPromo\Connect\SDK\Helpers\Configurator\CategoriesOptions();
        $configuratorCategoriesOptions->setLang('DE'); // get data from this page number
        $configuratorCategoriesOptions->setClientId($clientId);
        $configuratorCategoriesOptions->setEmpty(false);
        $configuratorCategoriesOptions->setHidden(true);

        try {
            $categoriesResponse = $categoriesRepository->all($configuratorCategoriesOptions);
            $this->printApiResponse(print_r($categoriesResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO - comment in after CO-2324 is fixed
            //return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Get options for a mother');
        $optionsRepository = new \MyPromo\Connect\SDK\Repositories\Configurator\OptionsRepository($this->clientMerchant);

        $configuratorOptionsOptions = new \MyPromo\Connect\SDK\Helpers\Configurator\OptionsOptions();
        $configuratorOptionsOptions->setLang('DE'); // get data from this page number
        $configuratorOptionsOptions->setClientId($clientId);
        $configuratorOptionsOptions->setId(null);
        $configuratorOptionsOptions->setSku(config('connect.test_sku_parent'));
        $configuratorOptionsOptions->setReference(null);

        try {
            $optionsResponse = $optionsRepository->all($configuratorOptionsOptions);
            $this->printApiResponse(print_r($optionsResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            // TODO - comment in after CO-2324 is fixed
            //return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('Get variants for a child');

        if (!empty($optionsResponse['available_variants']['default']['id'])) {

            $default_child = $optionsResponse['available_variants']['default']['id'];

            $variantRepository = new \MyPromo\Connect\SDK\Repositories\Configurator\VariantRepository($this->clientMerchant);

            $configuratorVariantOptions = new \MyPromo\Connect\SDK\Helpers\Configurator\VariantOptions();
            $configuratorVariantOptions->setLang('DE'); // get data from this page number
            $configuratorVariantOptions->setClientId($clientId);
            $configuratorVariantOptions->setCurrency('EUR');
            $configuratorVariantOptions->setId(null);
            $configuratorVariantOptions->setSku($default_child);

            try {
                $variantResponse = $variantRepository->all($configuratorVariantOptions);
                $this->printApiResponse(print_r($variantResponse, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                // TODO - comment in after CO-2324 is fixed
                //return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }
        } else {
            $this->error('Unable to perform test, cause there are no results for the mother.');
        }

    }


    /*
     * testProduction
     */
    public function testProduction()
    {
        $this->startMessage('testProduction');

        $this->testDetail('Get all production orders');
        $productionRepository = new \MyPromo\Connect\SDK\Repositories\ProductionOrders\ProductionOrderRepository($this->clientFulfiller);

        $productionOrderOptions = new \MyPromo\Connect\SDK\Helpers\ProductionOrders\ProductionOrderOptions();
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                return 0;
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }

        } else {
            $this->error('Unable to perform test, cause there are no production orders.');
        }

        $this->testDetail('Get a generic label');
        if (!empty($productionOrderResponse['data'])) {
            $this->info('Get a generic label for the first production order of previous result');
            $productionOrderId = $productionOrderResponse['data'][0]['id'];

            try {
                $productionOrderResponseGenericLabel = $productionRepository->genericLabel($productionOrderId);
                $this->printApiResponse(print_r($productionOrderResponseGenericLabel, true));
            } catch (ApiResponseException | InputValidationException $e) {
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                // return 0; // TODO: just available if configured for a client of type merchant the order is coming from...
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }
        } else {
            $this->error('Unable to test generic label route, cause there is no prodution order.');
        }


        $this->testDetail('Trying to download the label file...');

        // TODO - will fail due to wrong short link url - see CO-2320
        if (!empty($productionOrderResponseGenericLabel)) {
            $downloadFileUrl = $productionOrderResponseGenericLabel['url'];
            $filename = 'download_generic-label-' . date('Ymd_His') . '.pdf';
            $this->downloadFile($downloadFileUrl, $filename);
        } else {
            $this->error('Unable to test download of generic label. Could not generate label due to missing production order.');
        }


        $this->testDetail('Add a shipment');
        if (!empty($productionOrderResponse['data'])) {
            $this->info('Add a shipment to the first production order of previous result');
            $productionOrderId = $productionOrderResponse['data'][0]['id'];

            $shipment = new \MyPromo\Connect\SDK\Models\ProductionOrders\Shipment();

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
                $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
                $this->stopMessage();
                //return 0; - TODO if failed we see that as ok for now...
            } catch (ApiRequestException $e) {
                $this->error('ApiRequestException: ' . $e->getMessage());
                $this->stopMessage();
                return 0;
            }


        } else {
            $this->error('Unable to perform test, cause there are no production orders.');
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
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
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get carriers');
        $carrierRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\CarrierRepository($this->clientMerchant);

        $carrierOptions = new \MyPromo\Connect\SDK\Helpers\Miscellaneous\CarrierOptions();
        $carrierOptions->setPage(1);
        $carrierOptions->setPerPage(5);
        $carrierOptions->setPagination(false);

        try {
            $carrierResponse = $carrierRepository->all($carrierOptions);
            $this->printApiResponse(print_r($carrierResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get countries');
        $countryRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\CountryRepository($this->clientMerchant);

        $countryOptions = new \MyPromo\Connect\SDK\Helpers\Miscellaneous\CountryOptions();
        $countryOptions->setPage(1);
        $countryOptions->setPerPage(5);
        $countryOptions->setPagination(false);

        try {
            $countryResponse = $countryRepository->all($countryOptions);
            $this->printApiResponse(print_r($countryResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get locales');
        $localeRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\LocaleRepository($this->clientMerchant);

        $localeOptions = new \MyPromo\Connect\SDK\Helpers\Miscellaneous\LocaleOptions();
        $localeOptions->setPage(1);
        $localeOptions->setPerPage(5);
        $localeOptions->setPagination(false);

        try {
            $localeResponse = $localeRepository->all($localeOptions);
            $this->printApiResponse(print_r($localeResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get states');
        $stateRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\StateRepository($this->clientMerchant);

        $stateOptions = new \MyPromo\Connect\SDK\Helpers\Miscellaneous\StateOptions();
        $stateOptions->setPage(1);
        $stateOptions->setPerPage(5);
        $stateOptions->setPagination(false);

        try {
            $stateResponse = $stateRepository->all($stateOptions);
            $this->printApiResponse(print_r($stateResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


        $this->testDetail('get timezones');
        $timeZonesRepository = new \MyPromo\Connect\SDK\Repositories\Miscellaneous\TimezoneRepository($this->clientMerchant);

        $timeZonesOptions = new \MyPromo\Connect\SDK\Helpers\Miscellaneous\TimezoneOptions();
        $timeZonesOptions->setPage(1);
        $timeZonesOptions->setPerPage(5);
        $timeZonesOptions->setPagination(false);

        try {
            $timeZonesResponse = $timeZonesRepository->all($timeZonesOptions);
            $this->printApiResponse(print_r($timeZonesResponse, true));
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return 0;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return 0;
        }


    }

    /**
     * download a file from connect
     * own method to validate given files in some responses as well
     *
     * @param $file
     * @return int|void
     */
    public function downloadFile($url, $targetFile)
    {
        $this->startMessage('download file ' . $url . ' and saving to ' . $targetFile);
        $miscellaneousRepository = new GeneralRepository($this->clientMerchant);

        try {
            $downloadFileResponse = $miscellaneousRepository->downloadFile($url, $targetFile);
            $this->printApiResponse(print_r($downloadFileResponse, true));

            return true;
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return false;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return false;
        }

    }

    /**
     * @param $client
     * @return bool
     */
    public function apiStatus($client)
    {
        $this->startMessage('Test api status');
        $miscellaneousRepository = new GeneralRepository($client);

        try {
            $apiStatusResponse = $miscellaneousRepository->apiStatus();
            $this->printApiResponse(print_r($apiStatusResponse, true));

            return true;
        } catch (ApiResponseException | InputValidationException $e) {
            $this->error('ApiResponseException: ' . $e->getMessage() . ' - Errors: ' . print_r($e->getErrors(), true) . ' - Code: ' . $e->getCode());
            $this->stopMessage();
            return false;
        } catch (ApiRequestException $e) {
            $this->error('ApiRequestException: ' . $e->getMessage());
            $this->stopMessage();
            return false;
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

    /*
     *
     */
    public function testDetail($title)
    {
        $this->info($title);
        $this->info('------------------------------------------------------------------------------------------------------------------------------------------------');
    }


    /*
     *
     */
    public function printApiResponse($response, int $crop_length = 100)
    {
        // TODO - move toggles to somewhere else
        $show_api_response = false;
        $crop = false;

        if ($show_api_response == true) {
            if ($crop == true) {
                $cropped_response = substr($response, 0, $crop_length) . "\n >>> cropped response\n\n";

            } else {
                $cropped_response = $response;
            }

            $this->info($cropped_response);
        }
    }

    /*
     *
     */
    private function compareValues($key, $responseValue, $setValue)
    {
        if ($responseValue != $setValue) {
            $this->error($key . ' was not set from ' . $responseValue . ' to ' . $setValue);
        } else {
            $this->info($key . ' was successfully updated to ' . $setValue);

        }

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
