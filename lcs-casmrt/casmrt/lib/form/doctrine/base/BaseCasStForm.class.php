<?php

/**
 * CasSt form base class.
 *
 * @package    form
 * @subpackage cas_st
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCasStForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'ticket'                   => new sfWidgetFormInput(),
      'service'                  => new sfWidgetFormTextarea(),
      'client_hostname'          => new sfWidgetFormInput(),
      'username'                 => new sfWidgetFormInput(),
      'type'                     => new sfWidgetFormInput(),
      'consumed'                 => new sfWidgetFormDateTime(),
      'proxy_granting_ticket_id' => new sfWidgetFormInput(),
      'tgt_id'                   => new sfWidgetFormDoctrineChoice(array('model' => 'CasTgt', 'add_empty' => true)),
      'created_at'               => new sfWidgetFormDateTime(),
      'updated_at'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorDoctrineChoice(array('model' => 'CasSt', 'column' => 'id', 'required' => false)),
      'ticket'                   => new sfValidatorString(array('max_length' => 255)),
      'service'                  => new sfValidatorString(array('max_length' => 2147483647)),
      'client_hostname'          => new sfValidatorString(array('max_length' => 255)),
      'username'                 => new sfValidatorString(array('max_length' => 255)),
      'type'                     => new sfValidatorString(array('max_length' => 255)),
      'consumed'                 => new sfValidatorDateTime(array('required' => false)),
      'proxy_granting_ticket_id' => new sfValidatorInteger(array('required' => false)),
      'tgt_id'                   => new sfValidatorDoctrineChoice(array('model' => 'CasTgt', 'required' => false)),
      'created_at'               => new sfValidatorDateTime(array('required' => false)),
      'updated_at'               => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cas_st[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasSt';
  }

}
