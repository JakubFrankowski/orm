<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\ListenersInvoker;

/**
 * @group DDC-1707
 */
class DDC1707Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        try {
            $this->_schemaTool->createSchema(array(
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC1509File'),
                $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC1509Picture'),
            ));
        } catch (\Exception $ignored) {

        }
    }

    public function testPostLoadOnChild()
    {
        $class   = $this->_em->getClassMetadata(__NAMESPACE__ . '\DDC1707Child');
        $entity  = new DDC1707Child();
        $event   = new LifecycleEventArgs($entity, $this->_em);
        $invoker = new ListenersInvoker($this->_em);
        $invoke  = $invoker->getSubscribedSystems($class, \Doctrine\ORM\Events::postLoad);

        $invoker->invoke($class, \Doctrine\ORM\Events::postLoad, $entity, $event, $invoke);

        self::assertTrue($entity->postLoad);
    }
}

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorMap({"c": "DDC1707Child"})
 * @HasLifecycleCallbacks
 */
abstract class DDC1707Base
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    public $postLoad = false;

    /**
     * @PostLoad
     */
    public function onPostLoad()
    {
        $this->postLoad = true;
    }
}
/**
 * @Entity
 */
class DDC1707Child extends DDC1707Base
{
}
