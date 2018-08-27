<?php

namespace Netresearch\OPS\Test\Unit\Model;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFinal()
    {
        $status = \Netresearch\OPS\Model\Status::AUTHORIZED;
        $this->assertTrue(\Netresearch\OPS\Model\Status::isFinal($status));
        $status = \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT;
        $this->assertFalse(\Netresearch\OPS\Model\Status::isFinal($status));
    }

    public function testIsIntermediate()
    {
        $status = \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT;
        $this->assertTrue(\Netresearch\OPS\Model\Status::isIntermediate($status));
        $status = \Netresearch\OPS\Model\Status::AUTHORIZED;
        $this->assertFalse(\Netresearch\OPS\Model\Status::isIntermediate($status));
    }


    public function testIsCapture()
    {
        $captureStatus = [
            \Netresearch\OPS\Model\Status::PAYMENT_REQUESTED,
            \Netresearch\OPS\Model\Status::PAYMENT_PROCESSING,
            \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN,
            \Netresearch\OPS\Model\Status::PAYMENT_REFUSED,
            \Netresearch\OPS\Model\Status::PAYMENT_DECLINED_BY_ACQUIRER,
            \Netresearch\OPS\Model\Status::PAYMENT_PROCESSED_BY_MERCHANT,
            \Netresearch\OPS\Model\Status::REFUND_REVERSED,
            \Netresearch\OPS\Model\Status::PAYMENT_IN_PROGRESS
        ];
        foreach ($captureStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::isCapture($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::isCapture(\Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED));
    }

    public function testIsRefund()
    {
        $refundStatus = [
            \Netresearch\OPS\Model\Status::REFUNDED,
            \Netresearch\OPS\Model\Status::REFUND_PENDING,
            \Netresearch\OPS\Model\Status::REFUND_UNCERTAIN,
            \Netresearch\OPS\Model\Status::REFUND_REFUSED,
            \Netresearch\OPS\Model\Status::REFUNDED_OK,
            \Netresearch\OPS\Model\Status::REFUND_PROCESSED_BY_MERCHANT,
        ];
        foreach ($refundStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::isRefund($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::isCapture(\Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED));
    }

    public function testIsVoid()
    {
        $voidStatus =  [
            \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED,
            \Netresearch\OPS\Model\Status::DELETION_WAITING,
            \Netresearch\OPS\Model\Status::DELETION_UNCERTAIN,
            \Netresearch\OPS\Model\Status::DELETION_REFUSED,
            \Netresearch\OPS\Model\Status::AUTHORIZED_AND_CANCELLED_OK,
        ];
        foreach ($voidStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::isVoid($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::isVoid(\Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED));
    }


    public function testIsAuthorize()
    {
        $authStatus =  [
            \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
            \Netresearch\OPS\Model\Status::AUTHORIZED,
            \Netresearch\OPS\Model\Status::AUTHORIZED_WAITING_EXTERNAL_RESULT,
            \Netresearch\OPS\Model\Status::AUTHORIZATION_WAITING,
            \Netresearch\OPS\Model\Status::AUTHORIZED_UNKNOWN,
            \Netresearch\OPS\Model\Status::STAND_BY,
            \Netresearch\OPS\Model\Status::OK_WITH_SHEDULED_PAYMENTS,
            \Netresearch\OPS\Model\Status::NOT_OK_WITH_SHEDULED_PAYMENTS,
            \Netresearch\OPS\Model\Status::AUTHORISATION_TO_BE_REQUESTED_MANUALLY
        ];
        foreach ($authStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::isAuthorize($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::isAuthorize(\Netresearch\OPS\Model\Status::REFUND_PENDING));
    }

    public function testIsWaitingStatus()
    {
        $waitingStatus = [
            \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT,
            \Netresearch\OPS\Model\Status::WAITING_FOR_IDENTIFICATION,
            \Netresearch\OPS\Model\Status::STORED_WAITING_EXTERNAL_RESULT
        ];
        foreach ($waitingStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::isSpecialStatus($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::isSpecialStatus(\Netresearch\OPS\Model\Status::REFUND_PENDING));
    }


    public function testCanResendPaymentInfo()
    {
        $canResendInfoStatus = [
            \Netresearch\OPS\Model\Status::NOT_OK_WITH_SHEDULED_PAYMENTS,
            \Netresearch\OPS\Model\Status::CANCELED_BY_CUSTOMER,
            \Netresearch\OPS\Model\Status::AUTHORISATION_DECLINED,
            \Netresearch\OPS\Model\Status::AUTHORISATION_TO_BE_REQUESTED_MANUALLY,
            \Netresearch\OPS\Model\Status::PAYMENT_UNCERTAIN,
            \Netresearch\OPS\Model\Status::PAYMENT_REFUSED,
        ];
        foreach ($canResendInfoStatus as $status) {
            $this->assertTrue(\Netresearch\OPS\Model\Status::canResendPaymentInfo($status));
        }
        $this->assertFalse(\Netresearch\OPS\Model\Status::canResendPaymentInfo(\Netresearch\OPS\Model\Status::REFUND_PENDING));
    }
}
