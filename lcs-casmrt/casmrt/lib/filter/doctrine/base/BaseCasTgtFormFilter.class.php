<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * CasTgt filter form base class.
 *
 * @package    filters
 * @subpackage CasTgt *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseCasTgtFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ticket'           => new sfWidgetFormFilterInput(),
      'client_hostname'  => new sfWidgetFormFilterInput(),
      'username'         => new sfWidgetFormFilterInput(),
      'extra_attributes' => new sfWidgetFormFilterInput(),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'ticket'           => new sfValidatorPass(array('required' => false)),
      'client_hostname'  => new sfValidatorPass(array('required' => false)),
      'username'         => new sfValidatorPass(array('required' => false)),
      'extra_attributes' => new sfValidatorPass(array('required' => false)),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('cas_tgt_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasTgt';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'ticket'           => 'Text',
      'client_hostname'  => 'Text',
      'username'         => 'Text',
      'extra_attributes' => 'Text',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
    );
  }
}