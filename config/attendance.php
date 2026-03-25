<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WFH Anchor Movement Limit
    |--------------------------------------------------------------------------
    |
    | This global default controls how far a work-from-home student may move
    | away from the original time-in point before time-out is rejected.
    | Keep this app-wide for now so the rule stays simple, then promote it
    | to site policy later if the business wants per-site behavior.
    |
    */

    'wfh_anchor_limit_m' => 20,

];