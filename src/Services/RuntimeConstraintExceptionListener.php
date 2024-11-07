<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class RuntimeConstraintExceptionListener
{
  /**
   * onKernelException
   *
   * @param ExceptionEvent $event
   */
  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    $code = $this->getCode($exception);
    $errors = $this->getErrors($exception);

    $event->setResponse(new JsonResponse([
      "data" => [
        "code" => $code,
        "errors" => $errors,
      ]
    ], $code));
  }

  /**
   * getCode
   *
   * @param Throwable $exception
   * @return int
   */
  public function getCode(Throwable $exception): int
  {
    return array_key_exists($exception->getCode(), Response::$statusTexts)
      ? $exception->getCode()
      : Response::HTTP_UNPROCESSABLE_ENTITY;
  }

  /**
   * getErrors
   *
   * @param Throwable $exception
   * @return array
   */
  public function getErrors(Throwable $exception): array
  {
    if ($exception instanceof ConstraintViolationListInterface) {
      return $this->getErrorsFromConstraintViolationList($exception);
    }

    if ($tmpErrors = json_decode($exception->getMessage(), true)) {
      return $this->getAssociativeErrors($tmpErrors["data"]["errors"] ?? $tmpErrors);
    }

    return [[$exception->getMessage()]];
  }

  /**
   * getErrorsFromConstraintViolationList
   *
   * @param ConstraintViolationListInterface $violationList
   * @return array
   */
  private function getErrorsFromConstraintViolationList(ConstraintViolationListInterface $violationList): array
  {
    $errors = [];
    foreach ($violationList as $violation) {
      $errors[] = [
        'field' => $violation->getPropertyPath(),
        'message' => $violation->getMessage(),
      ];
    }
    return $errors;
  }

  /**
   * getAssociativeErrors
   *
   * @param array $errorsArray
   * @return array
   */
  private function getAssociativeErrors(array $errorsArray): array
  {
    $errors = [];
    foreach ($errorsArray as $field => $errorMessages) {
      $errors[] = [
        'field' => $field,
        'messages' => (array) $errorMessages,
      ];
    }
    return $errors;
  }
}
