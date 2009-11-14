<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * CasserverPgt filter form base class.
 *
 * @package    filters
 * @subpackage CasserverPgt *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseCasserverPgtFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ticket'            => new sfWidgetFormFilterInput(),
      'client_hostname'   => new sfWidgetFormFilterInput(),
      'iou'               => new sfWidgetFormFilterInput(),
      'service_ticket_id' => new sfWidgetFormDoctrineChoice(array('model' => 'CasSt', 'add_empty' => true)),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'ticket'            => new sfValidatorPass(array('required' => false)),
      'client_hostname'   => new sfValidatorPass(array('required' => false)),
      'iou'               => new sfValidatorPass(array('required' => false)),
      'service_ticket_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'CasSt', 'column' => 'id')),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('casserver_pgt_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasserverPgt';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'ticket'            => 'Text',
      'client_hostname'   => 'Text',
      'iou'               => 'Text',
      'service_ticket_id' => 'ForeignKey',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
    );
  }
}