<?php

namespace Drupal\challenge_component\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use \Drupal\user\Entity\User;
use Drupal\Core\Url;
Use \Drupal\Core\Routing;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax;
/**
 * Implementing a form.
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_Search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $theme_type_terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('article_theme');
    $output = "<ul>";
    foreach ($theme_type_terms as $term) {
      $output .= "<a href='/content-search?lense[]=article_theme:" . $term->tid . "'><li tid='".$term->tid."'>" . $term->name ."</li></a>";
    }
    $output .= "</ul>"; 
    
    $form['search_record'] = [
      '#type' => 'textfield',
      '#attributes' => ['class' => ['form-control search_input_field']],
      '#placeholder' => t('Search..'), 
      '#ajax' => [
        'event' => 'change',
        'wrapper' => 'chapter-formats',
        'callback' => '::_search_autosubmit_callback',
      ],
    ];
    
    $form['theme_tags'] = [
      '#type' => 'markup',
      '#markup' => $output, 
    ];
    
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => ['class' => ['btn btn-primary']],
      '#value' => $this->t('Save Changes'),
    ];
    $form['#theme'] = 'custom_search_form';
    return $form;
  }

  /**
   * Submitting the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

  public function _search_autosubmit_callback(array $form, FormStateInterface $form_state) {
    $search_text = $form_state->getValue('search_record');
    $url = Url::fromUserInput('/content-search?keyword=' . $search_text)->toString();
    $response = new AjaxResponse();
    $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand($url));
    return $response;
  }
}
