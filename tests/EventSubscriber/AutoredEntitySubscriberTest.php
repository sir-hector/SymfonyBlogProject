<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\EventSubscriber\AutoredEntitySubscriber;
use Monolog\Test\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;

class AutoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration(){
        $result = AutoredEntitySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(['getAuthenticatedUser',EventPriorities::PRE_WRITE],$result[KernelEvents::VIEW]);
    }
}