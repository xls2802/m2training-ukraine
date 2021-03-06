<?php

namespace Training\TestOM\Model;

/**
 * Class PlayWithTest
 * @package Training\TestOM\Model
 */
class PlayWithTest
{
    /**
     * @var Test
     */
    private Test $testObject;
    /**
     * @var TestFactory
     */
    private TestFactory $testObjectFactory;
    /**
     * @var ManagerCustomImplementation
     */
    private ManagerCustomImplementation $manager;

    /**
     * PlayWithTest constructor.
     * @param Test $testObject
     * @param TestFactory $testObjectFactory
     * @param ManagerCustomImplementation $manager
     */
    public function __construct(
        Test $testObject,
        TestFactory $testObjectFactory,
        ManagerCustomImplementation $manager
    ) {
        $this->testObject = $testObject;
        $this->testObjectFactory = $testObjectFactory;
        $this->manager = $manager;
    }
    public function run()
    {
        // test object with constructor arguments managed by di.xml
        $this->testObject->log();
        // test object with custom constructor arguments
        // some arguments are defined here, others - from di.xml
        $customArrayList = ['item1' => 'aaaaa', 'item2' => 'bbbbb'];
        $newTestObject = $this->testObjectFactory->create([
            'arrayList' => $customArrayList,
            'manager' => $this->manager
        ]);
        $newTestObject->log();
    }
}
