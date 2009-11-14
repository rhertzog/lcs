<?php

/**
 * CasTgt form base class.
 *
 * @package    form
 * @subpackage cas_tgt
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCasTgtForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'ticket'           => new sfWidgetFormInput(),
      'client_hostname'  => new sfWidgetFormInput(),
      'username'         => new sfWidgetFormInput(),
      'extra_attributes' => new sfWidgetFormTextarea(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorDoctrineChoice(array('model' => 'CasTgt', 'column' => 'id', 'required' => false)),
      'ticket'           => new sfValidatorString(array('max_length' => 255)),
      'client_hostname'  => new sfValidatorString(array('max_length' => 255)),
      'username'         => new sfValidatorString(array('max_length' => 255)),
      'extra_attributes' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
      'created_at'       => new sfValidatorDateTime(array('required' => false)),
      'updated_at'       => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cas_tgt[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasTgt';
  }

}
