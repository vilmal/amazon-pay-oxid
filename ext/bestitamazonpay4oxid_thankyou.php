<?php
/**
 * This Software is the property of best it GmbH & Co. KG and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license is
 * a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * bestitamazonpay4oxid_thankyou.php
 *
 * The bestitAmazonPay4Oxid_thankyou class file.
 *
 * PHP versions 5
 *
 * @category  bestitAmazonPay4Oxid
 * @package   bestitAmazonPay4Oxid
 * @author    best it GmbH & Co. KG - Alexander Schneider <schneider@bestit-online.de>
 * @copyright 2017 best it GmbH & Co. KG
 * @version   GIT: $Id$
 * @link      http://www.bestit-online.de
 */

/**
 * Class bestitAmazonPay4Oxid_thankyou
 */
class bestitAmazonPay4Oxid_thankyou extends bestitAmazonPay4Oxid_thankyou_parent
{
    /**
     * @var null|bestitAmazonPay4OxidContainer
     */
    protected $_oContainer = null;

    /**
     * Returns the active user object.
     *
     * @return bestitAmazonPay4OxidContainer
     */
    protected function _getContainer()
    {
        if ($this->_oContainer === null) {
            $this->_oContainer = oxNew('bestitAmazonPay4OxidContainer');
        }

        return $this->_oContainer;
    }

    /**
     * Delete Amazon pay details after checkout completed
     *
     * @return mixed
     */
    public function render()
    {
        $this->_getContainer()->getModule()->cleanAmazonPay();
        return parent::render();
    }
}

