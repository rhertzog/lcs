<?php

/**
 * CasserverPgt form base class.
 *
 * @package    form
 * @subpackage casserver_pgt
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCasserverPgtForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'ticket'            => new sfWidgetFormInput(),
      'client_hostname'   => new sfWidgetFormInput(),
      'iou'               => new sfWidgetFormInput(),
      'service_ticket_id' => new sfWidgetFormDoctrineChoice(array('model' => 'CasSt', 'add_empty' => false)),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorDoctrineChoice(array('model' => 'CasserverPgt', 'column' => 'id', 'required' => false)),
      'ticket'            => new sfValidatorString(array('max_length' => 255)),
      'client_hostname'   => new sfValidatorString(array('max_length' => 255)),
      'iou'               => new sfValidatorString(array('max_length' => 255)),
      'service_ticket_id' => new sfValidatorDoctrineChoice(array('model' => 'CasSt')),
      'created_at'        => new sfValidatorDateTime(array('required' => false)),
      'updated_at'        => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('casserver_pgt[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasserverPgt';
  }

}
