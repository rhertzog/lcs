<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * CasSt filter form base class.
 *
 * @package    filters
 * @subpackage CasSt *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseCasStFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ticket'                   => new sfWidgetFormFilterInput(),
      'service'                  => new sfWidgetFormFilterInput(),
      'client_hostname'          => new sfWidgetFormFilterInput(),
      'username'                 => new sfWidgetFormFilterInput(),
      'type'                     => new sfWidgetFormFilterInput(),
      'consumed'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'proxy_granting_ticket_id' => new sfWidgetFormFilterInput(),
      'tgt_id'                   => new sfWidgetFormDoctrineChoice(array('model' => 'CasTgt', 'add_empty' => true)),
      'created_at'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'ticket'                   => new sfValidatorPass(array('required' => false)),
      'service'                  => new sfValidatorPass(array('required' => false)),
      'client_hostname'          => new sfValidatorPass(array('required' => false)),
      'username'                 => new sfValidatorPass(array('required' => false)),
      'type'                     => new sfValidatorPass(array('required' => false)),
      'consumed'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'proxy_granting_ticket_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tgt_id'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'CasTgt', 'column' => 'id')),
      'created_at'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('cas_st_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CasSt';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'ticket'                   => 'Text',
      'service'                  => 'Text',
      'client_hostname'          => 'Text',
      'username'                 => 'Text',
      'type'                     => 'Text',
      'consumed'                 => 'Date',
      'proxy_granting_ticket_id' => 'Number',
      'tgt_id'                   => 'ForeignKey',
      'created_at'               => 'Date',
      'updated_at'               => 'Date',
    );
  }
}