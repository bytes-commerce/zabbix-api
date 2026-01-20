<?php

declare(strict_types=1);

namespace BytesCommerce\ZabbixApi\Tests;

use BytesCommerce\ZabbixApi\Event;
use BytesCommerce\ZabbixApi\ZabbixApiException;
use BytesCommerce\ZabbixApi\ZabbixClientInterface;
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
    private ZabbixClientInterface $zabbixClient;

    private Event $event;

    protected function setUp(): void
    {
        $this->zabbixClient = $this->createMock(ZabbixClientInterface::class);
        $this->event = new Event($this->zabbixClient);
    }

    public function testGetWithDefaultOutput(): void
    {
        $params = ['source' => Event::SOURCE_TRIGGER, 'value' => 1];
        $expectedParams = ['source' => Event::SOURCE_TRIGGER, 'value' => 1, 'output' => 'extend'];
        $expectedResult = [['eventid' => '1', 'clock' => '1672531200']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('event.get', $expectedParams)
            ->willReturn($expectedResult);

        $result = $this->event->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testGetWithCustomOutput(): void
    {
        $params = [
            'output' => ['eventid', 'clock', 'value'],
            'selectHosts' => ['hostid', 'name'],
            'source' => Event::SOURCE_TRIGGER
        ];
        $expectedResult = [['eventid' => '1', 'clock' => '1672531200', 'value' => 1]];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('event.get', $params)
            ->willReturn($expectedResult);

        $result = $this->event->get($params);

        self::assertSame($expectedResult, $result);
    }

    public function testAcknowledgeValid(): void
    {
        $params = [
            'eventids' => ['20415', '20416'],
            'action' => Event::ACTION_ACKNOWLEDGE | Event::ACTION_MESSAGE,
            'message' => 'Problem is being investigated and acknowledged.',
            'severity' => 4
        ];
        $expectedResult = ['eventids' => ['20415', '20416']];

        $this->zabbixClient->expects(self::once())
            ->method('call')
            ->with('event.acknowledge', $params)
            ->willReturn($expectedResult);

        $result = $this->event->acknowledge($params);

        self::assertSame($expectedResult, $result);
    }

    public function testAcknowledgeInvalidMissingEventIds(): void
    {
        $params = [
            'action' => Event::ACTION_ACKNOWLEDGE,
            'message' => 'Test message'
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Event acknowledge requires eventids array');

        $this->event->acknowledge($params);
    }

    public function testAcknowledgeInvalidEventIdsNotArray(): void
    {
        $params = [
            'eventids' => '20415',
            'action' => Event::ACTION_ACKNOWLEDGE
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Event acknowledge requires eventids array');

        $this->event->acknowledge($params);
    }

    public function testAcknowledgeInvalidMissingAction(): void
    {
        $params = [
            'eventids' => ['20415'],
            'message' => 'Test message'
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Event acknowledge requires action integer');

        $this->event->acknowledge($params);
    }

    public function testAcknowledgeInvalidActionNotInt(): void
    {
        $params = [
            'eventids' => ['20415'],
            'action' => 'acknowledge'
        ];

        $this->expectException(ZabbixApiException::class);
        $this->expectExceptionMessage('Event acknowledge requires action integer');

        $this->event->acknowledge($params);
    }

    public function testConstants(): void
    {
        // Test source constants
        self::assertSame(0, Event::SOURCE_TRIGGER);
        self::assertSame(1, Event::SOURCE_DISCOVERY);
        self::assertSame(2, Event::SOURCE_AUTOREGISTRATION);
        self::assertSame(3, Event::SOURCE_INTERNAL);
        self::assertSame(4, Event::SOURCE_SERVICE);

        // Test action constants
        self::assertSame(1, Event::ACTION_CLOSE);
        self::assertSame(2, Event::ACTION_ACKNOWLEDGE);
        self::assertSame(4, Event::ACTION_MESSAGE);
        self::assertSame(8, Event::ACTION_SEVERITY);

        // Test bitmask combinations
        $acknowledgeAndMessage = Event::ACTION_ACKNOWLEDGE | Event::ACTION_MESSAGE;
        self::assertSame(6, $acknowledgeAndMessage);

        $fullAction = Event::ACTION_CLOSE | Event::ACTION_ACKNOWLEDGE | Event::ACTION_MESSAGE | Event::ACTION_SEVERITY;
        self::assertSame(15, $fullAction);
    }
}
