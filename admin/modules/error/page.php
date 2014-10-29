<?php

	$c = chameleon::getInstance();
	$c->doAuth('cs_users', CS_PERMS_ACCESS_ADMIN, './', './', './?nmod=error');

	$c->setLayoutModule('main')->setLayoutFile('layout');
     $c->setField("PageTitle", "Error Page");

     $tpl = new n2f_template('dynamic');
     $tpl->setModule('error')->setFile('index');

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

     $c->registerContent(CS_CONTENT_ADMIN_MAIN, $tpl->fetch(), -1);
	$c->render();

?>