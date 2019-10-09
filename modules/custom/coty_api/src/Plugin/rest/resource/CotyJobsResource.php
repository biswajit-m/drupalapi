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
 *   id = "coty_api_jobs",
 *   label = @Translation("Coty Jobs API"),
 *   uri_paths = {
 *     "canonical" = "/coty_api/v1/job/{id}"
 *   }
 * )
 */
class CotyJobsResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns a watchdog log entry for the specified ID.
   *
   * @param int $id
   *   The ID of the watchdog log entry.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the log entry.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the log entry was not found.
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   Thrown when no log entry was provided.
   */
  public function get($id = NULL) {
    if ($id) {
      $node = Node::load($id);
      // Make sure it's a node.
      if ($node instanceof \Drupal\node\NodeInterface) {
        $res = $this->getFormattedNode($node);
        return new ResourceResponse($res);
      }

      throw new NotFoundHttpException(t('Job with ID @id was not found', ['@id' => $id]));
    }

    throw new BadRequestHttpException(t('No job found'));
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
