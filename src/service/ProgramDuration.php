<?php

namespace App\service;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\program;

class ProgramDuration
{

   public function calculate(Program $program): string
   {
      $duration = 0;
      foreach ($program->getSeasons() as $season) {
         foreach ($season->getEpisodes() as $episode) {
             $duration += $episode->getDuration();
         }
     }
      $days = floor($duration / (24 * 60));
      $hours = floor(($duration - ($days * 24 * 60)) / 60);
      $minutes = floor($duration % 60);

      $total = '';

      if ($days > 0) {
          $total .= $days . ' jours' . ' ';
      }

      if ($hours > 0) {
          $total .= $hours . ' heures' . ' ';
      }

      if ($minutes > 0) {
          $total .= $minutes . ' minutes' ;
      }

      return trim($total);
   }
}


