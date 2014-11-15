<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return array(
    'mode'=>'dev',
    'recaptcha' => array(
    		'name' => 'recaptcha',
            'pubKey' => '6Ldoz-cSAAAAADanfK6AWgr98b1_q5gsoauWRgo1',
    		'privKey' => '6Ldoz-cSAAAAAD9kG6hIWgj9dQh6WtGvv1FwsNZY',

    )
);
