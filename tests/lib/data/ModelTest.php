<?php

namespace Atwood\tests\lib\data;

use Atwood\lib\data\Stache;
use Atwood\lib\data\Model;

class ModelTest extends \Atwood\lib\test\AtwoodTest {

	public function testAccessors() {
		$db = MockModel::getDb();
		$this->assertInstanceOf('\MongoDB', $db);

		$col = MockModel::getCol();
		$this->assertInstanceOf('\MongoCollection', $col);
	}

	public function testCreate() {
		$mm		= new MockModel(array(
			'theKeys' => 'toHappiness'
		));

		$status = $mm->save();
		$this->assertTrue($status, 'MockModel did not save');
		$this->assertTrue($mm->wasValidated);
	}

	public function testReadOne() {
		$mm		= new MockModel(array(
			'theKeys' => 'toHappiness'
		));
		$mm->save();

		$this->assertNotNull($mm->id);

		// clear Stache
		Stache::clearAllInstances();

		// attempt read
		$mmR	= MockModel::readOne($mm->id);
		$this->assertNotNull($mmR);
		// attempt ready using string id
		$mmS	= MockModel::readOne((string)$mm->id);
		$this->assertNotNull($mmR);

		// attempt missed read
		$mmM	= MockModel::readOne('abcdef123456');
		$this->assertNull($mmM);
	}

	public function testReadMany() {
		for ($i = 0; $i < 100; ++$i) {
			$model	= new MockModel(array('i'=>$i));
			$status	= $model->save();
			$this->assertTrue($status);
		}

		$col	= MockModel::getCol();
		$cursor	= $col->find(array('i' => array(':gte' => 20)));
		$models	= MockModel::readMany($cursor, 7);

		$this->assertEquals(80, $cursor->count());
		$this->assertEquals(7, count($models));
		$this->assertEquals(26, $models[6]->doc['i']);
	}

	/**
	 * @depends testReadMany
	 */
	public function testTruncateTearDown() {
		// 100 items were inserted, how many are available now?
		$col	= MockModel::getCol();
		$count	= $col->count(array());
		$this->assertEquals(0, $count);
	}

	public function testUpdate() {
		$model	= new MockModel(array(
			'foo'	=> 'bar'
		));

		$status	= $model->save();
		$this->assertTrue($status);

		$status	= $model->update('bing', 'foo');
		$this->assertTrue($status);

		$model	= MockModel::readOne($model->id);
		$this->assertNotNull($model);

		$this->assertEquals('bing', $model->doc['foo']);
	}

	public function testDelete() {
		$models	= array();
		for ($i = 0; $i < 100; ++$i) {
			$model	= new MockModel(array('i'=>$i));
			$status	= $model->save();
			$this->assertTrue($status);

			$models[]	= $model;
		}

		$col	= MockModel::getCol();
		$count	= $col->count(array());
		$this->assertEquals(100, $count);

		for ($i = 0; $i < 56; ++$i) {
			$models[$i]->delete();
		}

		$count	= $col->count(array());
		$this->assertEquals(44, $count);
	}

	public function testSaveDeleted() {
		$model	= new MockModel();
		$model->delete();

		$this->setExpectedException('\Atwood\lib\fx\exception\ModelException');
		$model->save();
	}

	public function testUpdateNonExist() {
		$this->setExpectedException('\Atwood\lib\fx\exception\ModelException');

		$model	= new MockModel(array('foo' => 'bar'));
		$model->update('foo', 'bat');
	}

	public function testCreateExisting() {
		$this->setExpectedException('\Atwood\lib\fx\exception\ModelException');

		$model	= new MockModel(array('foo' => 'bar'));
		$model->save();
		$model->save();
	}
}

class MockModel extends Model {
	public $wasValidated	= false;

	public function validate() {
		$this->wasValidated = true;
		return !empty($this->doc['theKeys']);
	}
}
