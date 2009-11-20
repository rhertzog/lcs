<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCasLt extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('cas_lt');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'primary' => true, 'autoincrement' => true, 'length' => '4'));
        $this->hasColumn('ticket', 'string', 255, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('client_hostname', 'string', 255, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('consumed', 'timestamp', 25, array('type' => 'timestamp', 'length' => '25'));
    }

    public function setUp()
    {
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}