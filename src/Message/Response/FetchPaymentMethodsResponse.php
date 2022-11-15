<?php

namespace Omnipay\Payrexx\Message\Response;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\FetchPaymentMethodsResponseInterface;
use Omnipay\Common\PaymentMethod;

/**
 * @see https://developers.payrexx.com/reference#retrieve-a-transaction
 */
class FetchPaymentMethodsResponse extends AbstractResponse implements FetchPaymentMethodsResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->data->getStatus() === 'confirmed';
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods(): array
    {
        return array_map(function ($x) {
            return new PaymentMethod($x->getId(), $x->getName());
        }, $this->data);
    }
}
