<?php

namespace Omnipay\Payrexx\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payrexx\Message\Response\FetchPaymentMethodsResponse;
use Payrexx\Models\Request\PaymentMethod;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;

/**
 * @see https://developers.payrexx.com/reference#retrieve-a-transaction
 */
class FetchPaymentMethodsRequest extends AbstractRequest
{
    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInstance($value)
    {
        return $this->setParameter('instance', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFilterCurrency($value)
    {
        return $this->setParameter('filterCurrency', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFilterPaymentType($value)
    {
        return $this->setParameter('filterPaymentType', $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setFilterPsp($value)
    {
        return $this->setParameter('filterPsp', $value);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('apiKey', 'instance');

        $data = [];
        $data['apiKey'] = $this->getApiKey();
        $data['instance'] = $this->getInstance();
        $data['filterCurrency'] = $this->getFilterCurrency() ?? null;
        $data['filterPaymentType'] = $this->getFilterPaymentType() ?? null;
        $data['filterPsp'] = $this->getFilterPsp() ?? null;

        return $data;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getInstance()
    {
        return $this->getParameter('instance');
    }

    /**
     * @return string
     */
    public function getFilterCurrency()
    {
        return $this->getParameter('filterCurrency');
    }

    /**
     * @return string
     */
    public function getFilterPaymentType()
    {
        return $this->getParameter('filterPaymentType');
    }

    /**
     * @return int
     */
    public function getFilterPsp()
    {
        return $this->getParameter('filterPsp');
    }

    /**
     * @param array $data
     * @return FetchPaymentMethodsResponse
     * @throws InvalidRequestException
     */
    public function sendData($data)
    {
        try {
            $payrexx = new Payrexx($data['instance'], $data['apiKey']);
            $paymentMethod = new PaymentMethod();

            if (!empty($data['filterCurrency'])) {
                $paymentMethod->setFilterCurrency($data['filterCurrency']);
            }
            if (!empty($data['filterPaymentType'])) {
                $paymentMethod->setFilterPaymentType($data['filterPaymentType']);
            }
            if (!empty($data['filterPsp'])) {
                $paymentMethod->setFilterPsp($data['filterPsp']);
            }

            $response = $payrexx->getAll($paymentMethod);
        } catch (PayrexxException $e) {
            throw new InvalidRequestException($e->getMessage());
        }

        return $this->response = new FetchPaymentMethodsResponse($this, $response);
    }
}
