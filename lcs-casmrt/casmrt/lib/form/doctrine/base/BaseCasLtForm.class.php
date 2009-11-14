<?php

/**
 * CasLt form base class.
 *
 * @package    form
 * @subpackage cas_lt
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCasLtForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'ticket'          => new sfWidgetFormInput(),
      'client_hostname' => new sfWidgetFormInput(),
      'consumed'        => new sfWidgetFormDateTime(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorDoctrineChoice(array('model' => 'CasLt', 'column' => 'id', 'required' => false)),
      'ticket'          => new sfValidatorString(array('max_length' => 255)),
      'client_hostname' => new sfValidatorString(array('max_length' => 255)),
      'consumed'        => new sfValidatorDateTime(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(array('required' => false)),
      'updated_at'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cas_lt[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasLt';
  }

}
