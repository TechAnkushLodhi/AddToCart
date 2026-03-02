<?php
namespace Ankush\AddToCartPopup\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;

class Popup extends Template
{
    protected $registry;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get current added product
     */
    public function getProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get product image URL
     */
    public function getProductImageUrl(Product $product, string $imageType = 'product_page_image_small'): string
    {
        return $this->imageHelper
            ->init($product, $imageType)
            ->getUrl();
    }
}
