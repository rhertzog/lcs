<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCasTgt extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('cas_tgt');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'primary' => true, 'autoincrement' => true, 'length' => '4'));
        $this->hasColumn('ticket', 'string', 255, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('client_hostname', 'string', 255, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('username', 'string', 255, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '255'));
        $this->hasColumn('extra_attributes', 'string', 2147483647, array('type' => 'string', 'length' => '2147483647'));
    }

    public function setUp()
    {
        $this->hasMany('CasSt as CasTgts', array('local' => 'id',
                                                 'foreign' => 'tgt_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}