<?php
namespace Commercepundit\Customerdiscount\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Customer\Model\Customer;

class InstallAndPatch implements DataPatchInterface, PatchRevertableInterface
{
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(EavSetupFactory $eavSetupFactory, EavConfig $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function apply()
    {
        $this->installCustomDiscountAttribute();
    }

    private function installCustomDiscountAttribute()
    {
        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'cp_discount',
            [
                'type' => 'decimal',
                'label' => 'Custom Discount',
                'input' => 'text',
                'source' => '',
                'visible' => true,
                'required' => false,
                'frontend' => '',
                'unique' => false,
                'system' => false,
                'position' => 500,
                'adminhtml_only' => '',
                'note' => 'Enter the custom discount percentage here. This percentage will be applied to eligible customers. (Example: 5 for a 5.5% discount)'
            ]
        );

        $sampleAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'cp_discount');
        $sampleAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $sampleAttribute->save();
    }

    public static function getDependencies()
    {
        // Add dependencies if any
        return [];
    }

    public function revert()
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(Customer::ENTITY, 'cp_discount');
    }

    public function getAliases()
    {
        // Add aliases if any
        return [];
    }
}
