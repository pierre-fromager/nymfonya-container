<?php

namespace Nymfonya\Component\Container\Tests;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use stdClass;

/**
 * @covers Nymfonya\Component\Container::<public>
 */
class ContainerTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/config/';

    /**
     * container instance
     *
     * @var Container
     */
    protected $instance;

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->instance = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Container::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers Nymfonya\Component\Container::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Container);
    }

    /**
     * testInitReporter
     * @covers Nymfonya\Component\Container::initReporter
     * @covers Nymfonya\Component\Container::getReporter
     */
    public function testInitReporter()
    {
        $ir = self::getMethod('initReporter')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ir instanceof Container);
        $this->assertTrue(
            $this->instance->getReporter() instanceof \stdClass
        );
    }

    /**
     * testGetServices
     * @covers Nymfonya\Component\Container::getServices
     */
    public function testGetServices()
    {
        $this->assertTrue(is_array($this->instance->getServices()));
    }

    /**
     * testGetService
     * @covers Nymfonya\Component\Container::getService
     */
    public function testGetService()
    {
        $service = $this->instance->getService(Config::class);
        $this->assertTrue($service instanceof Config);
    }

    /**
     * testGetServiceException
     * @covers Nymfonya\Component\Container::getService
     */
    public function testGetServiceException()
    {
        $this->expectException(\Exception::class);
        $this->instance->getService('UnknownService');
    }

    /**
     * testSetService
     * @covers Nymfonya\Component\Container::setService
     * @covers Nymfonya\Component\Container::getService
     */
    public function testSetService()
    {
        $serviceClassname = 'dummy';
        $this->instance->setService($serviceClassname, new \stdClass());
        $this->assertTrue(
            $this->instance->getService($serviceClassname)
                instanceof \stdClass
        );
    }

    /**
     * testSetServiceNoClassException
     * @covers Nymfonya\Component\Container::setService
     */
    public function testSetServiceNoClassException()
    {
        $this->expectException(\Exception::class);
        $this->instance->setService('', new \stdClass());
    }

    /**
     * testSetServiceNotObjectException
     * @covers Nymfonya\Component\Container::setService
     */
    public function testSetServiceNotObjectException()
    {
        $this->expectException(\Exception::class);
        $this->instance->setService('1', null);
    }

    /**
     * testConstructable
     * @covers Nymfonya\Component\Container::constructable
     */
    public function testConstructable()
    {
        $cInt = self::getMethod('constructable')->invokeArgs(
            $this->instance,
            [1]
        );
        $this->assertFalse($cInt);
        $cBool = self::getMethod('constructable')->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertFalse($cBool);
    }

    /**
     * testHasService
     * @covers Nymfonya\Component\Container::hasService
     */
    public function testHasService()
    {
        $hass = self::getMethod('hasService')->invokeArgs(
            $this->instance,
            [Config::class]
        );
        $this->assertTrue($hass);
    }

    /**
     * testLoad
     * @covers Nymfonya\Component\Container::load
     */
    public function testLoad()
    {
        $checkedMethod = 'load';
        $ld = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ld instanceof Container);
    }

    /**
     * testSetServiceConfig
     * @covers Nymfonya\Component\Container::setServiceConfig
     */
    public function testSetServiceConfig()
    {
        $checkedMethod = 'setServiceConfig';
        $ssc = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [$this->config->getSettings(Config::_SERVICES)]
        );
        $this->assertTrue($ssc instanceof Container);
    }

    /**
     * testLoadException
     * @covers Nymfonya\Component\Container::load
     */
    public function testLoadException()
    {
        $this->expectException(\Exception::class);
        self::getMethod('setServiceConfig')->invokeArgs(
            $this->instance,
            [[]]
        );
        self::getMethod('load')->invokeArgs(
            $this->instance,
            []
        );
    }

    /**
     * testCreate
     * @covers Nymfonya\Component\Container::create
     */
    public function testCreate()
    {
        $createArgs = [
            self::CONFIG_PATH . Config::ENV_CLI,
            stdClass::class
        ];
        $cr = self::getMethod('create')->invokeArgs(
            $this->instance,
            [Config::class, $createArgs]
        );
        $this->assertTrue($cr instanceof Container);
        $servicesNames = array_keys($cr->getServices());
        $this->assertTrue(in_array(stdClass::class, $servicesNames));
    }

    /**
     * testIsBasicType
     * @covers Nymfonya\Component\Container::isBasicType
     */
    public function testIsBasicType()
    {
        $checkedMethod = 'isBasicType';
        $ibtInt = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [1]
        );
        $this->assertTrue($ibtInt);
        $ibtBool = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [true]
        );
        $this->assertTrue($ibtBool);
        $ibtObj = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [new \stdClass()]
        );
        $this->assertTrue($ibtObj);
        $ibtStr = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            ['strstring']
        );
        $this->assertFalse($ibtStr);
    }

    /**
     * testCreateCoreService
     * @covers Nymfonya\Component\Container::createCoreService
     */
    public function testCreateCoreService()
    {
        $basdServiceClassname = 'ImTheCoreServiceFromSpace';
        $ccs = self::getMethod('createCoreService')->invokeArgs(
            $this->instance,
            [
                $basdServiceClassname,
                [$this->instance]
            ]
        );
        $this->assertTrue($ccs instanceof Container);
        $serviceList =  array_keys($ccs->getServices());
        $this->assertFalse(
            in_array($basdServiceClassname, $serviceList)
        );
        $this->assertTrue(
            in_array('Nymfonya\Component\Config', $serviceList)
        );
    }

    /**
     * testCreateDependencies
     * @covers Nymfonya\Component\Container::createDependencies
     */
    public function testCreateDependencies()
    {
        $checkedMethod = 'createDependencies';
        $cd = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [
                [[]]
            ]
        );
        $this->assertTrue($cd instanceof Container);
        $cd = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [
                [[true, new \stdClass(), 'string']]
            ]
        );
        $this->assertTrue($cd instanceof Container);
    }

    /**
     * testInjectService
     * @covers Nymfonya\Component\Container::injectService
     */
    public function testInjectService()
    {
        $checkedMethod = 'injectService';
        $cd = self::getMethod($checkedMethod)->invokeArgs(
            $this->instance,
            [
                'key', ['value1', 'value2']
            ]
        );
        $this->assertTrue($cd instanceof Container);
    }
}
