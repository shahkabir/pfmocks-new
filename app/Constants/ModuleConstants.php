<?php

namespace App\Constants;

class ModuleConstants
{   
    //These values are maintained for different LOB types
    const MODULES =     [
                            'listening'   => 'Listening',
                            'reading'     => 'Reading',
                            'writing'     => 'Writing',
                            'speaking'    => 'Speaking',
                            'general_mcq' => 'General MCQ',
                            'sop_review'  => 'SOP Review',
                            'sop_new'     => 'New SOP Writing',
                        ];

    /** Module types that go through the manual evaluator pipeline. */
    const EVALUATABLE_TYPES = ['writing', 'speaking', 'sop_review', 'sop_new'];

    /** Module types that are SOP services (resume + university + country form). */
    const SOP_TYPES = ['sop_review', 'sop_new'];
    
    // const SESSION_TYPE = [
    //                         1 => 'Auto',
    //                         2 => 'Manual'
    //                      ];

    // const BUSINESS_LOB = [
    //                         1 => 'Banglalink',
    //                         2 => 'Genex'
    //                      ];

    // const USERTYPE = [
    //                     1 => 'QA Agent',
    //                     2 => 'Teamleader'
    //                  ];

    // const TEAM_NAMES = [
    //                        1 => 'Social Media',
    //                        2 => 'B2B', 
    //                        3 => 'CMT', 
    //                        4 => 'IRU', 
    //                        5 => 'Retention', 
    //                        6 => 'Collection', 
    //                        7 => 'Inbound(mulitple)'
    //                    ];
}
