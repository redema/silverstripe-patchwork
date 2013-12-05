<?php

/**
 * Copyright (c) 2012, Redema AB - http://redema.se/
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 * * Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * 
 * * Neither the name of Redema, nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
 *   prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class ScheduledJobTest extends FunctionalTest {
	
	public static $fixture_file = 'ScheduledJobTest.yml';
	
	public function testSchedule() {
		$super = 1;
		$test = 2;
		
		$job = ScheduledJob::register('SuperTestTask', null, null, $super, $test);
		$job->Scheduled = date('Y-m-d H:i:s', time() - 60);
		$job->write();
		
		$response = $this->get('dev/tasks/ScheduledJobTask');
		$this->assertEquals(200, $response->getStatusCode());
		
		$superTest = SuperTest::get()->filter(array(
			'Super' => $super,
			'Test' => $test
		))->First();
		
		$this->assertInstanceOf('SuperTest', $superTest);
	}
	
}

/**
 * Test implementation details.
 * @ignore
 * #@+
 */
class SuperTest extends DataObject {
	private static $db = array(
		'Super' => 'Int',
		'Test' => 'Int'
	);
}
class SuperTestTask extends DeferrableBuildTask {
	public static function schedule() {
		extract(Funky::exractable_args(func_get_args(), array(
			'ScheduledJob $scheduledJob',
			'$publisher',
			'$super',
			'$test'
		)));
		if (!is_numeric($super) || !is_numeric($test))
			throw new \InvalidArgumentException('super and test should be numbers');
		$scheduledJob->GetParams = array(
			'Super' => $super,
			'Test' => $test
		);
		$scheduledJob->write();
		return $scheduledJob;
	}
	public function run($request) {
		$superTest = new SuperTest();
		$superTest->Super = $request->getVar('Super');
		$superTest->Test = $request->getVar('Test');
		$superTest->write();
	}
}
/**
 * #@-
 */

