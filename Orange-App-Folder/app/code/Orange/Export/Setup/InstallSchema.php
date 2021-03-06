<?php
namespace Orange\Export\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('orange_order_export')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('orange_order_export')
            )
            ->addColumn(
                'export_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Export Id'
            )
            ->addColumn(
                'created_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                '',
                [],
                'Created Date'
            )
            ->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'Created Time'
            )
            ->addColumn(
                'uc_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'UC Order Id'
            )
            ->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Order Status'
            )
            ->addColumn(
                'order_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Order Total'
            )
            ->addColumn(
                'product_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Product Count'
            )
            ->addColumn(
                'primary_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Email'
            )
            ->addColumn(
                'delivery_first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'First Name'
            )
            ->addColumn(
                'delivery_last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Last Name'
            )
            ->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Payment Method'
            )
            ->addColumn(
                'host',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Host'
            )
			->addColumn(
                'sim_activated',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'Sim Activated'
            )
            ->addColumn(
                'product',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'product'
            )
            ->addColumn(
                'model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'model'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'qty'
            )
            ->addColumn(
                'cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'cost'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'price'
            )
            ->addColumn(
                'nid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                3,
                [],
                'nid'
            )
            ->addColumn(
                'catalogue',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'catalogue'
            )
            ->addColumn(
                'subsidy_device_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'subsidy_device_name'
            )
            ->addColumn(
                'language',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'language'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'title'
            )
			->addColumn(
                'zipcity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'zipcity'
            )
            ->addColumn(
                'street',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'street'
            )
            ->addColumn(
                'house_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'house_number'
            )
            ->addColumn(
                'bus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'bus'
            )
            ->addColumn(
                'optin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'optin'
            )
            ->addColumn(
                'gsm_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'gsm_number'
            )
            ->addColumn(
                'current_operator',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_operator'
            )
            ->addColumn(
                'current_gsm_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_gsm_number'
            )
            ->addColumn(
                'current_sim_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_sim_number'
            )
            ->addColumn(
                'current_tarrif_plan',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_tarrif_plan'
            )
            ->addColumn(
                'current_customer_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_customer_number'
            )
			->addColumn(
                'are_you_the_owner_of_the_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'are_you_the_owner_of_the_account'
            )
            ->addColumn(
                'first_name_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'first_name_owner'
            )
            ->addColumn(
                'last_name_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'last_name_owner'
            )
            ->addColumn(
                'telephone_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'telephone_number'
            )
            ->addColumn(
                'nationality',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'nationality'
            )
            ->addColumn(
                'place_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'place_of_birth'
            )
            ->addColumn(
                'date_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'date_of_birth'
            )
            ->addColumn(
                'are_you_registered_with_belgian_government',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'are_you_registered_with_belgian_government'
            )
            ->addColumn(
                'identity_card_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'identity_card_number'
            )
            ->addColumn(
                'rijksregister_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'rijksregister_number'
            )
            ->addColumn(
                'passport_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'passport_number'
            )
			->addColumn(
                'add_to_existing_plan',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'add_to_existing_plan'
            )
            ->addColumn(
                'customer_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'customer_number'
            )
            ->addColumn(
                'method_of_payment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'method_of_payment'
            )
            ->addColumn(
                'bank_account_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'bank_account_number'
            )
            ->addColumn(
                'enter_vat_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'enter_vat_number'
            )
            ->addColumn(
                'delivery_attention_off',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'delivery_attention_off'
            )
            ->addColumn(
                'delivery_zipcity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_zipcity'
            )
            ->addColumn(
                'delivery_street',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_street'
            )
            ->addColumn(
                'delivery_house_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_house_number'
            )
            ->addColumn(
                'delivery_bus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'delivery_bus'
            )
            ->addColumn(
                'shipping_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'shipping_total'
            )
			->addColumn(
                'bpost_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'bpost_data'
            )
            ->addColumn(
                'delivery_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'delivery_method'
            )
            ->addColumn(
                'invoice_owner_first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner_first_name'
            )
            ->addColumn(
                'invoice_owner_last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner_last_name'
            )
            ->addColumn(
                'invoice_owner_date_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'invoice_owner_date_of_birth'
            )
            ->addColumn(
                'invoice_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner'
            )
            ->addColumn(
                'business_profile',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'business_profile'
            )
            ->addColumn(
                'business_company_name',
               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'business_company_name'
            )
            ->addColumn(
                'business_company_vat',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'business_company_vat'
            )
            ->addColumn(
                'business_company_legal',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'business_company_legal'
            )
            ->addColumn(
                'customer_scoring',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'customer_scoring'
            )
			->addColumn(
                'eid_or_rpid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'eid_or_rpid'
            )
            ->addColumn(
                'scoring_decline_reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'scoring_decline_reason'
            )
            ->addColumn(
                'mobile_internet_client_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'mobile_internet_client_type'
            )
            ->addColumn(
                'subsidy_advantage',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'subsidy_advantage'
            )
            ->addColumn(
                'client_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'client_type'
            )
            ->addColumn(
                'iew_advantage',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'iew_advantage'
            )
            ->addColumn(
                'reduction_on_iew',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'reduction_on_iew'
            )
            ->addColumn(
                'promo_postpaid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'promo_postpaid'
            )
            ->addColumn(
                'pro_pack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'pro_pack'
            )
            ->addColumn(
                'smartphone_propack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'smartphone_propack'
            )
            ->addColumn(
                'reduction_propack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'reduction_propack'
            )
			->addColumn(
                'surf_proPack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'surf_propack'
            )
            ->addColumn(
                'soho_segment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'soho_segment'
            )
            ->addColumn(
                'ogone_transaction_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'ogone_transaction_id'
            )
            ->addColumn(
                'general_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'general_conditions'
            )
            ->addColumn(
                'coupon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'coupon'
            )            
            ->setComment('Orange Order Export Table');
            $installer->getConnection()->createTable($table);

        }
		if (!$installer->tableExists('orange_abandonorder_export')) {
            $abandontable = $installer->getConnection()->newTable(
                $installer->getTable('orange_abandonorder_export')
            )
            ->addColumn(
                'export_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Export Id'
            )
			->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Quote Id'
            )
            ->addColumn(
                'created_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                150,
                [],
                'Created Date'
            )
            ->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Created Time'
            )
			->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Order Status'
            )
            ->addColumn(
                'order_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Order Total'
            )
            ->addColumn(
                'product_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Product Count'
            )
            ->addColumn(
                'primary_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Email'
            )
            ->addColumn(
                'delivery_first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'First Name'
            )
            ->addColumn(
                'delivery_last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Last Name'
            )
            ->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Payment Method'
            )
            ->addColumn(
                'host',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Host'
            )
			->addColumn(
                'sim_activated',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'Sim Activated'
            )
            ->addColumn(
                'product',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'product'
            )
            ->addColumn(
                'model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'model'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'qty'
            )
            ->addColumn(
                'cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'cost'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'price'
            )
            ->addColumn(
                'nid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'nid'
            )
            ->addColumn(
                'catalogue',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'catalogue'
            )
            ->addColumn(
                'subsidy_device_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'subsidy_device_name'
            )
            ->addColumn(
                'language',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'language'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'title'
            )
			->addColumn(
                'zipcity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'zipcity'
            )
            ->addColumn(
                'street',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'street'
            )
            ->addColumn(
                'house_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'house_number'
            )
            ->addColumn(
                'bus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'bus'
            )
            ->addColumn(
                'optin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'optin'
            )
            ->addColumn(
                'gsm_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'gsm_number'
            )
            ->addColumn(
                'current_operator',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_operator'
            )
            ->addColumn(
                'current_gsm_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_gsm_number'
            )
            ->addColumn(
                'current_sim_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_sim_number'
            )
            ->addColumn(
                'current_tarrif_plan',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_tarrif_plan'
            )
            ->addColumn(
                'current_customer_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'current_customer_number'
            )
			->addColumn(
                'are_you_the_owner_of_the_account',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'are_you_the_owner_of_the_account'
            )
            ->addColumn(
                'first_name_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'first_name_owner'
            )
            ->addColumn(
                'last_name_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'last_name_owner'
            )
            ->addColumn(
                'telephone_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'telephone_number'
            )
            ->addColumn(
                'nationality',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'nationality'
            )
            ->addColumn(
                'place_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'place_of_birth'
            )
            ->addColumn(
                'date_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'date_of_birth'
            )
            ->addColumn(
                'are_you_registered_with_belgian_government',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'are_you_registered_with_belgian_government'
            )
            ->addColumn(
                'identity_card_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'identity_card_number'
            )
            ->addColumn(
                'rijksregister_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'rijksregister_number'
            )
            ->addColumn(
                'passport_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'passport_number'
            )
			->addColumn(
                'add_to_existing_plan',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'add_to_existing_plan'
            )
            ->addColumn(
                'customer_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'customer_number'
            )
            ->addColumn(
                'method_of_payment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'method_of_payment'
            )
            ->addColumn(
                'bank_account_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'bank_account_number'
            )
            ->addColumn(
                'enter_vat_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'enter_vat_number'
            )
            ->addColumn(
                'delivery_attention_off',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'delivery_attention_off'
            )
            ->addColumn(
                'delivery_zipcity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_zipcity'
            )
            ->addColumn(
                'delivery_street',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_street'
            )
            ->addColumn(
                'delivery_house_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'delivery_house_number'
            )
            ->addColumn(
                'delivery_bus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'delivery_bus'
            )
            ->addColumn(
                'shipping_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'shipping_total'
            )
			->addColumn(
                'bpost_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'bpost_data'
            )
            ->addColumn(
                'delivery_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'delivery_method'
            )
            ->addColumn(
                'invoice_owner_first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner_first_name'
            )
            ->addColumn(
                'invoice_owner_last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner_last_name'
            )
            ->addColumn(
                'invoice_owner_date_of_birth',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'invoice_owner_date_of_birth'
            )
            ->addColumn(
                'invoice_owner',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'invoice_owner'
            )
            ->addColumn(
                'business_profile',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'business_profile'
            )
            ->addColumn(
                'business_company_name',
               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'business_company_name'
            )
            ->addColumn(
                'business_company_vat',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'business_company_vat'
            )
            ->addColumn(
                'business_company_legal',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'business_company_legal'
            )
            ->addColumn(
                'customer_scoring',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'customer_scoring'
            )
			->addColumn(
                'eid_or_rpid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'eid_or_rpid'
            )
            ->addColumn(
                'scoring_decline_reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'scoring_decline_reason'
            )
            ->addColumn(
                'mobile_internet_client_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'mobile_internet_client_type'
            )
            ->addColumn(
                'subsidy_advantage',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'subsidy_advantage'
            )
            ->addColumn(
                'client_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'client_type'
            )
            ->addColumn(
                'iew_advantage',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'iew_advantage'
            )
            ->addColumn(
                'reduction_on_iew',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'reduction_on_iew'
            )
            ->addColumn(
                'promo_postpaid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'promo_postpaid'
            )
            ->addColumn(
                'pro_pack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'pro_pack'
            )
            ->addColumn(
                'smartphone_propack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'smartphone_propack'
            )
            ->addColumn(
                'reduction_propack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'reduction_propack'
            )
			->addColumn(
                'surf_propack',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable => false'],
                'surf_propack'
            )
            ->addColumn(
                'soho_segment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'soho_segment'
            )
            ->addColumn(
                'ogone_transaction_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'ogone_transaction_id'
            )
            ->addColumn(
                'general_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'general_conditions'
            )
            ->addColumn(
                'coupon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'coupon'
            )            
            ->setComment('Orange Abandon Order Export Table');
            $installer->getConnection()->createTable($abandontable);

        }
        $installer->endSetup();
    }
}
