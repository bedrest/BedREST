<?php

namespace BedRest\Tests\Mapping;

use BedRest\Tests\BaseTestCase;
use BedRest\Mapping\ResourceMeta;

/**
 * ResourceMetaTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetaTest extends BaseTestCase
{
    public function testName()
    {
        $rm = new ResourceMeta('Test');
        
        $this->assertEquals('Test', $rm->getName());
    }
    
    public function testServiceClass()
    {
        $rm = new ResourceMeta('Test');
        $rm->setServiceClass('Services\Test');
        
        $this->assertEquals('Services\Test', $rm->getServiceClass());
    }
    
    public function testDefaultServiceMethods()
    {
        $rm = new ResourceMeta('Test');
        
        $methods = $rm->getServiceMethods();
        
        $this->assertArrayHasKey('index', $methods);
        $this->assertArrayHasKey('get', $methods);
        $this->assertArrayHasKey('post', $methods);
        $this->assertArrayHasKey('put', $methods);
        $this->assertArrayHasKey('delete', $methods);
        
        $this->assertEquals('index', $methods['index']);
        $this->assertEquals('get', $methods['get']);
        $this->assertEquals('post', $methods['post']);
        $this->assertEquals('put', $methods['put']);
        $this->assertEquals('delete', $methods['delete']);
        
        $this->assertEquals('index', $rm->getServiceMethod('index'));
        $this->assertEquals('get', $rm->getServiceMethod('get'));
        $this->assertEquals('post', $rm->getServiceMethod('post'));
        $this->assertEquals('put', $rm->getServiceMethod('put'));
        $this->assertEquals('delete', $rm->getServiceMethod('delete'));
    }
    
    public function testSetServiceMethods()
    {
        $rm = new ResourceMeta('Test');
        
        $methods = array(
            'index' => 'list',
            'get' => 'fetch',
            'post' => 'create',
            'put' => 'update',
            'delete' => 'remove'
        );
        
        $rm->setServiceMethods($methods);
        
        $this->assertEquals('list', $rm->getServiceMethod('index'));
        $this->assertEquals('fetch', $rm->getServiceMethod('get'));
        $this->assertEquals('create', $rm->getServiceMethod('post'));
        $this->assertEquals('update', $rm->getServiceMethod('put'));
        $this->assertEquals('remove', $rm->getServiceMethod('delete'));
        
        $rm->setServiceMethod('get', 'getSingle');
        
        $this->assertEquals('getSingle', $rm->getServiceMethod('get'));
    }
}
