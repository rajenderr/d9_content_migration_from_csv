<?php
/**
 * @file
 * Contains \Drupal\myairport_bulk_content_upload\Form\MyairportBulkContentUploadForm.
 */
namespace Drupal\myairport_bulk_content_upload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;

/**
 * Contribute form.
 */
class MyairportBulkContentUploadForm extends FormBase {
	/**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myairport_bulk_content_upload_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

  	$validators = array(
      'file_validate_extensions' => array('csv'),
    );
    $form['nodes_upload'] = [
      '#type' => 'managed_file',
      '#prefix' => "<div class='myairport_csv_file_upload'>",
      '#suffix' => "</div><p id ='myairport_csv_file_upload' class ='form-group error-description'></p>",
      '#name' => 'users_upload',
      '#title' => t('Content Bulk Upload'),
      '#size' => 20,
      '#description' => t('Select the CSV file to be import content'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Import Content'),
      '#attributes' => ['disabled' => 'disabled'],
    ];
    $form['#cache']['max-age'] = 0;

    $nodedata = 'path,';
    $nodedata = 'airport,';
    $nodedata .= 'category,';
    $nodedata .= 'language,';
    $nodedata .= 'name,';
    $nodedata = 'promotions,';
    $nodedata .= 'shortDesc,';
    $nodedata = 'subCategory,';
    $nodedata = 'terminal,';
    $nodedata = 'type,';
    $nodedata .= 'level,';
    $nodedata .= 'longitude,';
    $nodedata = 'locationId,';
    $nodedata .= 'squareImage,';
    $nodedata .= 'banner,';
    $nodeFields = substr($nodedata, 0, -1);
    $result = '</tr></table>';
    $sampleFile = 'myairport_bulk_content_upload.csv';
    $handle = fopen("sites/default/files/" . $sampleFile, "w+") or die("There is no permission to create log file. Please give permission for sites/default/file!");
    fwrite($handle, $nodeFields);
    $result .= '<a class= "btn-download" style="float:left;" href="' . $base_url . '/sites/default/files/' . $sampleFile . '">Download Sample CSV</a>';
    $form['myairport-content-sample-csv'] = [
      '#type' => 'markup',
      '#markup' => '<div class = "button button-download">'.$result.'</>',
    ];
    
    $form['#cache']['max-age'] = 0;
    $form['#attached']['library'][] = 'myairport_bulk_content_upload/myairport-bulk-content-upload';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = \Drupal::database();
  	$file = \Drupal::entityTypeManager()->getStorage('file')->load($form_state->getValue('nodes_upload')[0]);
  	$form_values = $form_state->getValues('nodes_upload');
  	$file_name = $file->get('filename')->value;
  	$file_uri = $file->get('uri')->value;

    if($file_uri) {
      $data = $this->csvtoarray($file_uri, ',');
      foreach($data as $row) {
        $operations[] = array('\Drupal\myairport_bulk_content_upload\MyairportBulkContent::myairportAddNew', array($row));
      }
      
      $batch = array(
        'title' => t('Creating content...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\myairport_bulk_content_upload\MyairportBulkContent::myairportAddNewContentFinishedCallback',
      );
      batch_set($batch);
    }
  	
  }

  public function csvtoarray($filename='', $delimiter) {

    if(!file_exists($filename) || !is_readable($filename)) return FALSE;
    $header = NULL;
    $data = array();

    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
      {
        if (!$header) {
          $header = $row;
        } else {
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }

    return $data;
  }

}

?>