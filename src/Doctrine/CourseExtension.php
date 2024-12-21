<?php

declare(strict_types=1);

namespace App\Extension;

use App\Entity\Course;
use App\Extension\UserRelationExtension;

class CourseExtension extends UserRelationExtension
{
  /**
   * @return string
   */
  public function getResourceClass(): string
  {
    return Course::class;
  }
}
