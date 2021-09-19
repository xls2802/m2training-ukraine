<?php

namespace Training\TestOM\Model;

class Test
{
    private $manager;
    private $arrayList;
    private $name;
    private $number;
    private $managerFactory;

    /**
     * Test constructor.
     * @param ManagerInterface $manager
     * @param $name
     * @param int $number
     * @param array $arrayList
     */
    public function __construct(
        ManagerInterface $manager,
        $name,
        int $number,
        array $arrayList,
        ManagerInterfaceFactory $managerFactory
    ) {
        $this->manager = $manager;
        $this->name = $name;
        $this->number = $number;
        $this->arrayList = $arrayList;
        $this->managerFactory = $managerFactory;
    }

    /**
     * @return void
     */
    public function log()
    {
        print_r(get_class($this->manager));
        echo '<br>';
        print_r($this->name);
        echo '<br>';
        print_r($this->number);
        echo '<br>';
        print_r($this->arrayList);
        echo '<br>';
        $newManager = $this->managerFactory->create();
        print_r(get_class($newManager));

        //path to generated factory - generated/code/Training/TestOM/Model/ManagerInterfaceFactory.php
    }
}
