<?php

namespace Drupal\ercore_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\taxonomy\Entity\Term;

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

    // Adviser Mentor field combines two text fields into an entity reference.
    // First generation is a new field.
    // Collecting data.
    $query->addField('cv', 'field_er_collecting_data_bool_value', 'collecting');
    $query->leftJoin('field_data_field_er_collecting_data_bool', 'cv', "cv.entity_id = u.uid");

    // EPSCoR Paid.
    $query->addField('ep', 'field_er_paid_value', 'epscor_paid');
    $query->leftJoin('field_data_field_er_paid', 'ep', "ep.entity_id = u.uid");

    // Boards and Commissions.
    $query->addField('sb', 'field_er_committees_value', 'source_board');
    $query->leftJoin('field_data_field_er_committees', 'sb', "sb.entity_id = u.uid");

    // Senior Roles.
    $query->addField('sr', 'field_er_most_sen_proj_role_value', 'senior_role');
    $query->leftJoin('field_data_field_er_most_sen_proj_role', 'sr', "sr.entity_id = u.uid");

    // Institution.
    $query->addField('i', 'field_er_inst_ref_target_id', 'institution');
    $query->leftJoin('field_data_field_er_inst_ref', 'i', "i.entity_id = u.uid");

    // Components.
    $query->addField('c', 'field_er_components_target_id', 'component');
    $query->leftJoin('field_data_field_er_components', 'c', "c.entity_id = u.uid AND c.bundle = :bundle",
      [':bundle' => 'user']
    );

    // Data Description.
    $query->addField('dd', 'field_er_type_of_data_value', 'data_desc');
    $query->leftJoin('field_data_field_er_type_of_data', 'dd', "dd.entity_id");

    // User Description.
    $query->addField('rd', 'field_er_description_value', 'role_desc');
    $query->leftJoin('field_data_field_er_description', 'rd', "rd.entity_id = u.uid AND rd.bundle = :bundle",
      [':bundle' => 'user']
    );

    // Hired date.
    $query->addField('hd', 'field_er_hired_date_value', 'hired_date');
    $query->leftJoin('field_data_field_er_hired_date', 'hd', "hd.entity_id = u.uid");

    // Demographics.
    // Gender.
    $query->addField('ge', 'field_er_gender_value', 'gender');
    $query->leftJoin('field_data_field_er_gender', 'ge', "ge.entity_id = u.uid");

    // Ethnicity.
    $query->addField('et', 'field_er_ethnicity_value', 'ethnicity');
    $query->leftJoin('field_data_field_er_ethnicity', 'et', "et.entity_id = u.uid");

    // Disabilities.
    $query->addField('di', 'field_er_disabilities_value', 'disabilities');
    $query->leftJoin('field_data_field_er_disabilities', 'di', "di.entity_id = u.uid");

    // Race.
    $query->addField('ra', 'field_er_race_value', 'race');
    $query->leftJoin('field_data_field_er_race', 'ra', "ra.entity_id = u.uid");

    // Veteran.
    $query->addField('vt', 'field_er_veteran_status_value', 'veteran');
    $query->leftJoin('field_data_field_er_veteran_status', 'vt', "vt.entity_id = u.uid");

    // Phone mobile.
    $query->addField('pm', 'field_er_phone_personal_value', 'phone_mobile');
    $query->leftJoin('field_data_field_er_phone_personal', 'pm', "pm.entity_id = u.uid");

    // Phone work.
    $query->addField('pw', 'field_er_phone_work_value', 'phone_work');
    $query->leftJoin('field_data_field_er_phone_work', 'pw', "pw.entity_id = u.uid");

    // Professional link.
    $query->addField('pl', 'field_er_professional_link_url', 'link');
    $query->leftJoin('field_data_field_er_professional_link', 'pl', "pl.entity_id = u.uid");

    // Leadership.
    $query->addField('lt', 'field_er_leadership_team_value', 'lead_team');
    $query->leftJoin('field_data_field_er_leadership_team', 'lt', "lt.entity_id = u.uid");

    // Faculty support.
    $query->addField('fs', 'field_er_faculty_any_support_value', 'fac_support');
    $query->leftJoin('field_data_field_er_faculty_any_support', 'fs', "fs.entity_id = u.uid");

    // RCR status.
    $query->addField('rcr', 'field_er_rcr_completion_bool_value', 'rcr_status');
    $query->leftJoin('field_data_field_er_rcr_completion_bool', 'rcr', "rcr.entity_id = u.uid");

    // RCR online.
    $query->addField('rcro', 'field_er_rcr_online_value', 'rcr_on');
    $query->leftJoin('field_data_field_er_rcr_online', 'rcro', "rcro.entity_id = u.uid");

    // RCR online upload.
    $query->addField('rcrou', 'field_er_rcr_online_ul_fid', 'rcr_on_up');
    $query->leftJoin('field_data_field_er_rcr_online_ul', 'rcrou', "rcrou.entity_id = u.uid");

    // RCR in person.
    $query->addField('rcrp', 'field_er_rcr_in_person_value', 'rcr_per');
    $query->leftJoin('field_data_field_er_rcr_in_person', 'rcrp', "rcrp.entity_id = u.uid");

    // RCR in person upload.
    $query->addField('rcrpu', 'field_er_rcr_inperson_ul_fid', 'rcr_per_up');
    $query->leftJoin('field_data_field_er_rcr_inperson_ul', 'rcrpu', "rcrpu.entity_id = u.uid");

    // Participation dates.
    $query->addField('p', 'field_er_participating_date_value', 'start');
    $query->addField('p', 'field_er_participating_date_value2', 'end');
    $query->leftJoin('field_data_field_er_participating_date', 'p', "p.entity_id = u.uid");

    // Mentoring plan date.
    $query->addField('mp', '	field_er_mentoring_sign_off_value', 'mentor_plan_date');
    $query->leftJoin('field_data_field_er_mentoring_sign_off', 'mp', "mp.entity_id = u.uid");

    // Mentoring plan upload.
    $query->addField('mpu', 'field_er_mentoring_plan_ul_fid', 'mentor_plan');
    $query->leftJoin('field_data_field_er_mentoring_plan_ul', 'mpu', "mpu.entity_id = u.uid");

    // EPSCoR paid.
    $query->addField('ep', 'field_er_paid_value', 'epscor_paid');
    $query->leftJoin('field_data_field_er_paid', 'ep', "ep.entity_id = u.uid");

    // Months direct funding.
    $query->addField('df', 'field_er_months_of_effort_value', 'direct_fund');
    $query->leftJoin('field_data_field_er_months_of_effort', 'df', "df.entity_id = u.uid");

    // Months effort.
    $query->addField('me', 'field_er_person_month_value', 'effort_months');
    $query->leftJoin('field_data_field_er_person_month', 'me', "me.entity_id = u.uid");

    // Funding sources.
    $query->addField('src', 'field_er_list_funding_sources_value', 'funding_src');
    $query->leftJoin('field_data_field_er_list_funding_sources', 'src', "src.entity_id = u.uid");

    // 160 hours.
    $query->addField('one', 'field_er_160hours_value', 'part_160');
    $query->leftJoin('field_data_field_er_160hours', 'one', "one.entity_id = u.uid");

    // Department.
    $query->addField('dept', 'field_er_department_value', 'department');
    $query->leftJoin('field_data_field_er_department', 'dept', "dept.entity_id = u.uid");

    // Data frequency.
    $query->addField('fq', 'field_er_data_frequency_value', 'data_ongoing');
    $query->leftJoin('field_data_field_er_data_frequency', 'fq', "fq.entity_id = u.uid");

    // Data date.
    $query->addField('ds', 'field_er_data_submit_date_value', 'data_date');
    $query->leftJoin('field_data_field_er_data_submit_date', 'ds', "ds.entity_id = u.uid");

    // Data manager.
    $query->addField('dm', 'field_er_has_this_user_value', 'data_mgr');
    $query->leftJoin('field_data_field_er_has_this_user', 'dm', "dm.entity_id = u.uid");

    // Data URL.
    $query->addField('du', 'field_er_data_url_url', 'data_url');
    $query->leftJoin('field_data_field_er_data_url', 'du', "du.entity_id = u.uid");

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
    // Roles.
    $uid = $row->getSourceProperty('uid');
    $query = $this->select('users_roles', 'r');
    $query->fields('r', ['rid']);
    $query->condition('r.uid', $uid, '=');
    $record = $query->execute()->fetchAllKeyed();
    $row->setSourceProperty('roles', array_keys($record));

    // Board and Commissions.
    $name = $row->getSourceProperty('source_board');
    if (!empty($name)) {
      $vocab = 'ercore_boards_and_committees';
      $term = taxonomy_term_load_multiple_by_name($name, $vocab);
      if ($term) {
        reset($term);
        $tid = key($term);
      }
      else {
        $term = Term::create([
          'vid' => $vocab,
          'langcode' => 'en',
          'name' => $name,
        ]);
        $updated = $term->save();
        $tid = $term->id();
      }
      $row->setSourceProperty('boards', $tid);
    }

    // Setting education dates.
    $doc_ant = $row->getSourceProperty('doc_act');
    $row->setSourceProperty('doc_ant', self::processUserDates($doc_ant));
    $doc_act = $row->getSourceProperty('doc_act');
    $row->setSourceProperty('doc_act', self::processUserDates($doc_act));

    $master_ant = $row->getSourceProperty('master_act');
    $row->setSourceProperty('master_ant', self::processUserDates($master_ant));
    $master_act = $row->getSourceProperty('doc_act');
    $row->setSourceProperty('master_act', self::processUserDates($master_act));

    $under_ant = $row->getSourceProperty('under_act');
    $row->setSourceProperty('under_ant', self::processUserDates($under_ant));
    $under_act = $row->getSourceProperty('under_act');
    $row->setSourceProperty('under_act', self::processUserDates($under_act));

    // Hired date.
    $hired = $row->getSourceProperty('hired_date');
    $row->setSourceProperty('hired_date', self::processUserDates($hired));
    // Start date.
    $start = $row->getSourceProperty('start');
    $row->setSourceProperty('start', self::processUserDates($start));

    // End date.
    $end = $row->getSourceProperty('end');
    $row->setSourceProperty('end', self::processUserDates($end));

    // Mentoring date.
    $mentor = $row->getSourceProperty('mentor_plan_date');
    $row->setSourceProperty('mentor_plan_date', self::processUserDates($mentor));

    // RCR Online.
    $rcr_on = $row->getSourceProperty('rcr_on');
    $row->setSourceProperty('rcr_on', self::processUserDates($rcr_on));
    // RCR In Person.
    $rcr_per = $row->getSourceProperty('rcr_per');
    $row->setSourceProperty('rcr_per', self::processUserDates($rcr_per));

    return parent::prepareRow($row);
  }

  /**
   * Process user account dates.
   *
   * @param string $date
   *   Receive dates in Drupal 7 format.
   *
   * @return string|null
   *   Returns string or null.
   */
  public function processUserDates($date) {
    $return = NULL;
    if (!empty($date)) {
      $exploded = explode(' ', $date);
      $return = $exploded[0];
    }
    return $return;
  }

}
