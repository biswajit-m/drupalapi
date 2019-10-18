<?php

namespace Drupal\coty_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\node\Entity\Node;
use \Drupal\node\NodeInterface;

/**
 * Provides a resource for database watchdog log entries.
 *
 * @RestResource(
 *   id = "coty_api_job_list",
 *   label = @Translation("Coty Job List API"),
 *   uri_paths = {
 *     "canonical" = "/coty_api/v1/job-list"
 *   }
 * )
 */
class CotyJobListResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns a watchdog log entry for the specified ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the log entry.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the log entry was not found.
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   Thrown when no log entry was provided.
   */
  public function get() {
    $jobs = $this->getJobNode();

    if (!empty($jobs)) {
      return new ResourceResponse($jobs);
    }

    throw new BadRequestHttpException(t('No job found'));
  }

  public function getJobNode() {
    $nids = \Drupal::entityQuery('node')->condition('type','article')->execute();
    // $query = \Drupal::entityQuery('node')
    //   ->condition('status', 1)
    //   ->condition('type', 'article')
    //   ->execute();
    // echo "<pre>";
    // print_r($nids);
    // print_r($query);
    $nodes = Node::loadMultiple($nids);

    $jobs = [];
    foreach ($nodes as $node) {
      $jobs[] = $this->getFormattedNode($node);
    }

    return $jobs;
  }

  public function getFormattedNode($node) {
    $res = [];

    //echo "<pre>"; print_r($node->get('field_image')->getValue());die;
    $res['name'] = $node->get('title')->value;
    $res['nid'] = $node->id();
    $res['uid'] = $node->get('uid')->target_id;
    $res['lang'] = $node->get('langcode')->value;
    $res['description'] = $node->get('body')->value;
    $res['image'] = file_create_url($node->field_image->entity->getFileUri());

    return $res;
  }

}
