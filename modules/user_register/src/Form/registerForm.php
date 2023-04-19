<?php

namespace Drupal\user_register\Form;

use Drupal;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\row\Fields;

/**
 * Provides a user_register form.
 */
class registerForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'user_register_register';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['fullname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('FullName'),
      '#required' => TRUE,
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
    ];
    $form['birthday'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthday'),
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $str = "/^([a-zA-Z0-9ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i";
    if (!preg_match($str, trim($form_state->getValue('fullname')))) {
      $form_state->setErrorByName('fullname', $this->t('Enter the vaild fullname'));
    }
    if (!preg_match('/[0-9]/', trim($form_state->getValue('phone')))) {
      $form_state->setErrorByName('phone', $this->t('Enter the vaild Phone'));
    }

    if (!\Drupal::service('email.validator')->isValid(trim($form_state->getValue('email')))) {
      $form_state->setErrorByName('email', $this->t('Enter the valid email'));
    }

    $dateOfBirth = trim($form_state->getValue('birthday'));
    $today = date("Y-m-d");
    $diff = date_diff(date_create($dateOfBirth), date_create($today));
    if ($diff->format('%y') < 18) {
      $form_state->setErrorByName('birthday', $this->t('Under 18+'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $form_tmp = $form_state->getValues();

    $form_data['fullname'] = $form_tmp['fullname'];
    $form_data['email'] = $form_tmp['email'];
    $form_data['phone'] = $form_tmp['phone'];
    $form_data['birthday'] = $form_tmp['birthday'];
    $form_data['description'] = $form_tmp['description'];

    $conn = \Drupal::service('database');
    $result = $conn->insert('user_register')->fields($form_data)->execute();

    $this->messenger()->addStatus($this->t('The information has been saved successfully !.'));
    $form_state->setRedirect('<front>');
  }
}
