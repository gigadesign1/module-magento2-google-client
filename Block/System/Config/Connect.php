<?php
/**
 * Copyright © Gigadesign. All rights reserved.
 */
declare(strict_types=1);

namespace Gigadesign\GoogleClient\Block\System\Config;

use Gigadesign\Exactonline\Helper\Api;

use Gigadesign\GoogleClient\Model\GoogleClientManager;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

/**
 * Class Connect
 *
 * @author Mark van der Werf <info@gigadesign.nl>
 */
class Connect extends Field
{

    /**
     * @var GoogleClientManager
     */
    protected $clientManager;

    /**
     * @var string
     */
    protected $_template = 'Gigadesign_Exactonline::system/config/connect.phtml';

    /**
     * @param GoogleClientManager $clientManager
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        GoogleClientManager $clientManager,
        Context $context,
        array $data = []
    ) {
        $this->clientManager  = $clientManager;

        parent::__construct($context, $data);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate collect button html
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setId('connect_button')->setData(
            [
                'id' => 'connect_button',
                'label' => __('Connect to Google Calendar'),
                'onclick' => 'javascript:doOAuth();return false;'
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getAuthenticationUrl(): string
    {
        return $this->clientManager->getAuthUrl();
    }
}