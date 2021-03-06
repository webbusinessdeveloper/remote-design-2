<?php
/**
 * Created by PhpStorm.
 * User: MacBookEr
 * Date: 11/16/14
 * Time: 6:13 PM
 */

namespace tests;


use App\MyStuff\AppFactory;
use App\MyStuff\AppInvoker;

class AppInvokerTest extends \PHPUnit_Framework_TestCase {

    public function test_appInvoker_addStateToObject_method_adds_state_to_an_object()
    {
        $invoker = new AppInvoker();

        $factory = new AppFactory();

        $state = $factory->createNewState('on', 'off');
        $light = $factory->createNewObject('light', 'kitchen');

        $invoker->addStateToObject($light, $state);

        $this->assertEquals('light in the kitchen is on', $light->activate());
        $this->assertEquals('light in the kitchen is off', $light->deactivate());
    }

    public function test_appInvoker_addControllerToRemote_method_adds_controller_to_a_remote()
    {
        $invoker = new AppInvoker();

        $factory = new AppFactory();

        $state = $factory->createNewState('on', 'off');
        $light = $factory->createNewObject('light', 'kitchen');

        $invoker->addStateToObject($light, $state);

        $slot = $factory->createNewController($light);

        $remote = $factory->createNewRemote();

        $this->assertEquals(0, count($remote->controller));

        $invoker->addControllerToRemote($remote, $slot);

        $this->assertEquals(1, count($remote->controller));
    }

    public function test_appInvoker_activateControllerOnRemote_method_activates_controller_on_remote()
    {
        $invoker = new AppInvoker();

        $factory = new AppFactory();

        $state = $factory->createNewState('on', 'off');
        $light = $factory->createNewObject('light', 'kitchen');
        $invoker->addStateToObject($light, $state);
        $slot = $factory->createNewController($light);

        $state2 = $factory->createNewState('on', 'off');
        $fan = $factory->createNewObject('fan', 'office');
        $invoker->addStateToObject($fan, $state2);
        $slot2 = $factory->createNewController($fan);

        $remote = $factory->createNewRemote();

        $remote->addController($slot)->addController($slot2);

        $this->assertEquals('light in the kitchen is on', $invoker->activateControllerOnRemote($remote, 1));
        $this->assertEquals('fan in the office is on', $invoker->activateControllerOnRemote($remote, 2));
    }

    public function test_appInvoker_deactivateControllerOnRemote_method_deactivates_a_controller_on_remote()
    {
        $invoker = new AppInvoker();

        $factory = new AppFactory();

        $state = $factory->createNewState('on', 'off');
        $light = $factory->createNewObject('light', 'kitchen');
        $invoker->addStateToObject($light, $state);
        $slot = $factory->createNewController($light);

        $state2 = $factory->createNewState('on', 'off');
        $fan = $factory->createNewObject('fan', 'office');
        $invoker->addStateToObject($fan, $state2);
        $slot2 = $factory->createNewController($fan);

        $remote = $factory->createNewRemote();

        $remote->addController($slot)->addController($slot2);

        $invoker->activateControllerOnRemote($remote, 1);
        $invoker->activateControllerOnRemote($remote, 2);

        $this->assertEquals('light in the kitchen is off', $invoker->deactivateControllerOnRemote($remote, 1));
        $this->assertEquals('fan in the office is off', $invoker->deactivateControllerOnRemote($remote, 2));
    }

    public function test_appInvoker_undoOnRemote_method_will_call_undo_method_on_remote()
    {
        $invoker = new AppInvoker();

        $factory = new AppFactory();

        $state = $factory->createNewState('on', 'off');
        $light = $factory->createNewObject('light', 'kitchen');
        $invoker->addStateToObject($light, $state);
        $slot = $factory->createNewController($light);

        $state2 = $factory->createNewState('on', 'off');
        $fan = $factory->createNewObject('fan', 'office');
        $invoker->addStateToObject($fan, $state2);
        $slot2 = $factory->createNewController($fan);

        $remote = $factory->createNewRemote();

        $remote->addController($slot)->addController($slot2);

        $this->assertEquals('light in the kitchen is on',$invoker->activateControllerOnRemote($remote, 1));
        $this->assertEquals('fan in the office is on', $invoker->activateControllerOnRemote($remote, 2));

        $this->assertEquals('light in the kitchen is off', $invoker->deactivateControllerOnRemote($remote, 1));
        $this->assertEquals('fan in the office is off', $invoker->deactivateControllerOnRemote($remote, 2));

        $this->assertEquals('fan in the office is on', $invoker->undoOnRemote($remote));
        $this->assertEquals('light in the kitchen is on', $invoker->undoOnRemote($remote));

        $this->assertEquals('fan in the office is off', $invoker->undoOnRemote($remote));
        $this->assertEquals('light in the kitchen is off', $invoker->undoOnRemote($remote));

        $this->assertEquals('Cant undo. You have to do something first.', $invoker->undoOnRemote($remote));
    }
}
