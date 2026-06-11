<?php

return [

    /*
  |--------------------------------------------------------------------------
  | Daily upload limit per user
  |--------------------------------------------------------------------------
  |
  | Maximum number of images a single user can upload per calendar day.
  |
  */

    'daily_upload_limit' => (int) env('IMAGE_DAILY_UPLOAD_LIMIT', 100_000),

];
