<?php

namespace Omnipay\Payrexx\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Payrexx\Message\Response\ChargeTransactionResponse;
use Payrexx\Models\Request\Transaction;
use Payrexx\Payrexx;
use Payrexx\PayrexxException;

/**
 * @see https://developers.payrexx.com/reference#retrieve-a-transaction
 */
class ChargeTransactionRequest extends AbstractRequest
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
    public function setReferenceId($value)
    {
        return $this->setParameter('referenceId', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPurpose($value)
    {
        return $this->setParameter('purpose', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('apiKey', 'instance', 'transactionReference', 'amount', 'currency');

        $data = [];
        $data['apiKey'] = $this->getApiKey();
        $data['instance'] = $this->getInstance();
        $data['id'] = $this->getTransactionReference();
        $data['currency'] = $this->getCurrency();
        $data['amount'] = $this->getAmountInteger();
        $data['referenceId'] = $this->getReferenceId() ?? null;
        $data['purpose'] = $this->getPurpose() ?? null;
        $data['email'] = $this->getEmail() ?? null;

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
    public function getReferenceId()
    {
        return $this->getParameter('referenceId');
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->getParameter('purpose');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * @param array $data
     * @return ChargeTransactionResponse
     * @throws InvalidRequestException
     */
    public function sendData($data)
    {
        try {
            $payrexx = new Payrexx($data['instance'], $data['apiKey']);
            $transaction = new Transaction();
            $transaction->setId($data['id']);
            $transaction->setCurrency($data['currency']);
            $transaction->setAmount($data['amount']);
            $transaction->setPurpose($data['purpose']) ?? null;
            $transaction->setReferenceId($data['referenceId']) ?? null;

            if (!empty($data['email'])) {
                $transaction->addField('email', $data['email']);
            }

            $response = $payrexx->charge($transaction);
        } catch (PayrexxException $e) {
            throw new InvalidRequestException($e->getMessage());
        }

        return $this->response = new ChargeTransactionResponse($this, $response);
    }
}
