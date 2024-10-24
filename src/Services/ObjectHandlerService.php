<?php

namespace App\Services;

use DateTime;

class ObjectHandlerService
{

  /**
   * @var RequestCheckerService
   */
  private RequestCheckerService $requestCheckerService;

  /**
   * @param RequestCheckerService 
   */
  public function __construct(RequestCheckerService $requestCheckerService)
  {
    $this->requestCheckerService = $requestCheckerService;
  }

  /**
   * setObjectData
   *
   * @param  mixed $object
   * @param  mixed $fields
   * @return mixed
   */
  public function setObjectData(mixed $object, array $fields): mixed
  {
    foreach ($fields as $key => $value) {
      $method = 'set' . ucfirst($key);

      if (str_contains(strtolower($key), 'date',)) {
        $value = new DateTime($value);
      }

      if (!method_exists($object, $method)) {
        continue;
      }

      $object->$method($value);
    }

    $this->requestCheckerService->validateRequestDataByConstraints($object);

    return $object;
  }
}
