<?php

use L4\Tests\BackwardCompatibleTestCase;
use Mockery as m;

class CacheMemcachedConnectorTest extends BackwardCompatibleTestCase
{

    protected function tearDown(): void
    {
        m::close();
    }


    public function testServersAreAddedCorrectly()
    {
        $connector = $this->getMock('Illuminate\Cache\MemcachedConnector', array('getMemcached'));
        $memcached = m::mock('stdClass');
        $memcached->shouldReceive('addServer')->once()->with('localhost', 11211, 100);
		$memcached->shouldReceive('getVersion')->once()->andReturn(true);
		$connector->expects($this->once())->method('getMemcached')->will($this->returnValue($memcached));
		$result = $connector->connect(array(array('host' => 'localhost', 'port' => 11211, 'weight' => 100)));

		$this->assertSame($result, $memcached);
	}


	/**
	 * @expectedException RuntimeException
	 */
	public function testExceptionThrownOnBadConnection()
	{
		$connector = $this->getMock('Illuminate\Cache\MemcachedConnector', array('getMemcached'));
		$memcached = m::mock('stdClass');
		$memcached->shouldReceive('addServer')->once()->with('localhost', 11211, 100);
		$memcached->shouldReceive('getVersion')->once()->andReturn(false);
		$connector->expects($this->once())->method('getMemcached')->will($this->returnValue($memcached));
		$result = $connector->connect(array(array('host' => 'localhost', 'port' => 11211, 'weight' => 100)));
	}

}
