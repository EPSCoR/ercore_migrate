<?php

namespace Drupal\ercore_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for the Users.
 *
 * @MigrateSource(
 *   id = "users"
 * )
 */
class Users extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users', 'u');
    $query->fields('u', [
      'uid',
      'mail',
      'name',
      'pass',
      'created',
      'access',
      'timezone',
      'status',
      'login',
    ]);
    $query->innerJoin('node', 'n', 'n.uid = u.uid');
    $query->condition('u.uid', 0, '!=');
    $query->distinct();

    // First name.
    $query->addField('fn', 'field_er_fname_value', 'first_name');
    $query->leftJoin('field_data_field_er_fname', 'fn', "fn.entity_id = u.uid");

    // Last name.
    $query->addField('ln', 'field_er_lname_value', 'last_name');
    $query->leftJoin('field_data_field_er_lname', 'ln', "ln.entity_id = u.uid");

    // Actual doctoral date.
    $query->addField('dact', 'field_er_act_doctor_value', 'doc_act');
    $query->leftJoin('field_data_field_er_act_doctor', 'dact', "dact.entity_id = u.uid");

    // Anticipated doctoral date.
    $query->addField('dant', 'field_er_ant_doctor_value', 'doc_ant');
    $query->leftJoin('field_data_field_er_ant_doctor', 'dant', "dant.entity_id = u.uid");

    // Actual masters date.
    $query->addField('mact', 'field_er_act_masters_value', 'master_act');
    $query->leftJoin('field_data_field_er_act_masters', 'mact', "mact.entity_id = u.uid");

    // Anticipated masters date.
    $query->addField('mant', 'field_er_ant_masters_value', 'master_ant');
    $query->leftJoin('field_data_field_er_ant_masters', 'mant', "mant.entity_id = u.uid");

    // Actual undergraduate date.
    $query->addField('uact', 'field_er_act_under_value', 'under_act');
    $query->leftJoin('field_data_field_er_act_under', 'uact', "uact.entity_id = u.uid");

    // Anticipated undergraduate date.
    $query->addField('uant', 'field_er_ant_under_value', 'under_ant');
    $query->leftJoin('field_data_field_er_ant_under', 'uant', "uant.entity_id = u.uid");
    /*
    field_ercore_user_doc_act
    field_ercore_user_master_act
    field_ercore_user_under_act
    field_ercore_user_advisor_mentor
    field_ercore_user_doc_ant
    field_ercore_user_master_ant
    field_ercore_user_under_ant
    field_ercore_user_first_gen
    field_ercore_user_collecting
    field_ercore_user_epscore_paid
    field_ercore_user_boards
    field_ercore_user_component
    field_ercore_user_data_desc
    field_ercore_user_data_url
    field_ercore_user_department
    field_ercore_user_role_desc
    field_ercore_user_disabilities
    field_ercore_user_fac_support
    field_ercore_user_part_160
    field_ercore_user_employee_id
    field_ercore_user_ethnicity
    field_ercore_user_funding_src
    field_ercore_user_gender
    field_ercore_user_data_mgr
    field_ercore_user_rcr_status
    field_ercore_user_hired_date
    field_ercore_user_lead_team
    field_ercore_user_data_ongoing
    field_ercore_user_mentor_plan_dt
    field_ercore_user_direct_fund
    field_ercore_user_effort_months
    field_ercore_senior_role
    field_ercore_user_name
    field_ercore_user_partic_inst
    field_ercore_user_end
    field_ercore_user_start
    field_ercore_user_phone_mobile
    field_ercore_user_phone_work
    user_picture
    field_ercore_user_mentor_plan
    field_ercore_prefer_no_answer
    field_ercore_user_link
    field_ercore_user_program
    field_ercore_user_race
    field_ercore_user_rcr_per
    field_ercore_user_rcr_per_up
    field_ercore_user_rcr_on
    field_ercore_user_rcr_on_up
    field_ercore_user_second_email
    field_ercore_user_veteran
    field_ercore_user_data_date
    field_ercore_user_address
        */
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'created' => $this->t('Created Time'),
      'access' => $this->t('Access Time'),
      'timezone' => $this->t('Timezone'),
      'mail' => $this->t('Email'),
      'status' => $this->t('Status'),
      'login' => $this->t('Login field'),
      'first_name' => $this->t('First Name'),
      'last_name' => $this->t('Last Name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
    $query = $this->select('users_roles', 'r');
    $query->fields('r', ['rid']);
    $query->condition('r.uid', $uid, '=');
    $record = $query->execute()->fetchAllKeyed();
    $row->setSourceProperty('roles', array_keys($record));

    return parent::prepareRow($row);
  }

}
