<?php

	$c = chameleon::getInstance();
	$c->setLayoutModule('main')->setLayoutFile('layout');

	$tpl = new n2f_template('dynamic');
     $tpl->setSkin($c->getSetting('cs_front_skin'))->setModule('error')->setFile('index');

     // Look for our error codes
     if (isset($_REQUEST['error_code'])) {
          switch ($_REQUEST['error_code']) {
               case N2F_ERRCODE_MODULE_FAILURE:
                    $error_message = S('N2F_ERRCODE_MODULE_FAILURE', array($_REQUEST['nmod']));
                    break;
               default:
                    $error_message = "We're not sure what happened, but it must have been bad.  Maybe you should try again?";
                    break;
          }
     } else {
          $error_message = "We're not sure what happened, but it must have been bad.  Maybe you should try again?";
     }

     $tpl->setField('error_message', $error_message);
     $tpl->render();

	$c->setField('PageTitle', 'Chameleon Framework - Error');
     $c->registerContent(CS_CONTENT_FRONT_MAIN, $tpl->fetch(), -1);
	$c->render();

?>