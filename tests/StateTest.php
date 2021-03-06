<?php
/**
 * Created by PhpStorm.
 * User: MacBookEr
 * Date: 11/11/14
 * Time: 7:55 PM
 */

namespace Tests\StateTest;


use App\MyStuff\Slot;
use App\MyStuff\State;

class StateTest extends \PHPUnit_Framework_TestCase {

    public function test_state_instantiates_with_activate_and_deactivate_set()
    {
        $state = new State('open', 'close');

        $this->assertEquals('open', $state->getActivator());
        $this->assertEquals('close', $state->getDeactivator());

    }

    public function test_state_can_instantiate_with_low_and_high_settings()
    {
        $state = new State('open', 'close', 'low', 'high');

        $this->assertEquals('low', $state->getLow());
        $this->assertEquals('high', $state->getHigh());
    }

    public function test_state_can_call_activate_and_deactivate_function()
    {
        $state = new State('open', 'close');

        $this->assertEquals(' is open', $state->activateTest());
        $this->assertEquals(' is close', $state->deactivateTest());
    }

    public function test_state_is_set_with_a_currentState()
    {
        $state = new State('on', 'off');

        $this->assertEquals(' is off', $state->getCurrentState());
    }

    public function test_state_currentState_will_update_with_each_changeOfState()
    {
        $state = new State('on', 'off');

        $this->assertEquals(' is off', $state->getCurrentState());

        $this->assertEquals(' is on', $state->changeOfState($state->activateTest()));

        $this->assertEquals(' is off', $state->changeOfState($state->deactivateTest()));


    }

    public function test_state_previousState_will_update_with_each_changeOfState()
    {
        $state = new State('on', 'off');

        $state->changeOfState($state->activateTest());

        $this->assertEquals(' is off', $state->getPreviousState());

    }

    public function test_state_previousStateLog_will_hold_all_previous_states()
    {
        $state = new State('on', 'off');

        $state->changeOfState($state->activateTest());

        $this->assertEquals(1, $state->getPreviousStateLogCount());

        $state->changeOfState($state->deactivateTest());

        $this->assertEquals(2, $state->getPreviousStateLogCount());

        $state->changeOfState($state->activateTest());

        $this->assertEquals(3, $state->getPreviousStateLogCount());
    }

    public function test_state_previousStateLog_will_return_last_state_then_remove_from_log()
    {
        $state = new State('on', 'off');

        $state->changeOfState($state->activateTest());

        $this->assertEquals(1, $state->getPreviousStateLogCount());

        $state->changeOfState($state->deactivateTest());

        $this->assertEquals(2, $state->getPreviousStateLogCount());

        $state->changeOfState($state->activateTest());

        $this->assertEquals(3, $state->getPreviousStateLogCount());

        $this->assertEquals(' is off', $state->getLastPreviousStateFromLogThenPop());

        $this->assertEquals(2, $state->getPreviousStateLogCount());

        $this->assertEquals(' is on', $state->getLastPreviousStateFromLogThenPop());

        $this->assertEquals(1, $state->getPreviousStateLogCount());

        $this->assertEquals(' is off', $state->getLastPreviousStateFromLogThenPop());

        $this->assertEquals(0, $state->getPreviousStateLogCount());
    }

    public function test_state_undo_function_will_return_state_to_its_previous_state()
    {
        $state = new State('on', 'off');

        $state->changeOfState($state->activateTest());
        $state->changeOfState($state->deactivateTest());
        $state->changeOfState($state->activateTest());

        $this->assertEquals(' is off', $state->undo());
        $this->assertEquals(' is on', $state->undo());
        $this->assertEquals(' is off', $state->undo());
    }

    public function test_state_undo_function_will_update_states_previousState_correctly()
    {
        $state = new State('on', 'off');

        $state->changeOfState($state->activateTest());//previous = off
        $state->changeOfState($state->deactivateTest());//prevoius = on
        $state->changeOfState($state->activateTest());// previous = off

        $state->undo();
        $this->assertEquals(' is on', $state->previousState);

        $state->undo();
        $this->assertEquals(' is off', $state->previousState);
    }

    public function test_state_undo_function_checks_that_state_is_undoable()
    {
        $state = new State('on', 'off');

        $this->assertEquals('cant undo', $state->undo());

        $state->changeOfState($state->activateTest());//previous = off
        $state->changeOfState($state->deactivateTest());//prevoius = on
        $state->changeOfState($state->activateTest());// previous = off

        $state->undo();
        $this->assertEquals(' is on', $state->previousState);

        $state->undo();
        $this->assertEquals(' is off', $state->previousState);

        $state->undo();
        $this->assertEquals(null, $state->previousState);

        $this->assertEquals('cant undo', $state->undo());

    }

    public function test_state_changeOfState_is_called_when_activated_and_deactivated()
    {
        $state = new State('on', 'off');

        $this->assertEquals(null, $state->previousState);

        $state->activate();

        $this->assertEquals(' is off', $state->previousState);

        $state->deactivate();

        $this->assertEquals(' is on', $state->previousState);
    }



}
