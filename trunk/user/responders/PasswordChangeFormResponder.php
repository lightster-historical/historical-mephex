<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/db/MySQL.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';
require_once PATH_LIB . 'com/mephex/nav/Navigation.php';
require_once PATH_LIB . 'com/mephex/nav/NavItem.php';
require_once PATH_LIB . 'com/mephex/user/User.php';


class PasswordChangeFormResponder extends HttpResponder
{
    protected $error;

    public function init($args)
    {
        parent::init($args);
        $this->error = '';
    }

    public function post($args)
    {
        $this->input->set('password');
        $this->input->set('new_password');
        $this->input->set('confirm_password');

        $user = User::getActiveUser();
        if(!$user->verifyPassword($this->input->get('password'), false))
        {
            $this->error = 'The current password could not be authenticated.<br />';
            $this->error .= '<br />Note that passwords are case-sensitive.';
        }
        else if($this->input->get('new_password') == '')
        {
            $this->error = 'A password must be provided.';
        }
        else if($this->input->get('new_password') != $this->input->get('confirm_password'))
        {
            $this->error = 'The new passwords do not match.<br />';
            $this->error .= '<br />Note that passwords are case-sensitive.';
        }
        else
        {
            $user->setPassword($this->input->get('new_password'));
            User::setActiveUser($user->getUsername(), $this->input->get('new_password'));
            $this->error = 'Your password has been updated.';
        }
    }

    public function get($args)
    {
        ?>
         <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <?php
        if($this->error != '')
        {
            ?>
            <div style="text-align: center; font-weight: bold; line-height: 75%; background-color: #cc0000; color: #ffffff; margin: 2px 3px 2px 0; padding: 5px; ">
              <?php echo $this->error; ?>
             </div>
            <?php
        }
        ?>
          <dl class="detail">
           <dt>Current Password</dt>
           <dd><input type="password" class="text" name="password" value="" /></dd>
           <dt>New Password</dt>
           <dd><input type="password" class="text" name="new_password" value="" /></dd>
           <dt>Confirm New Password</dt>
           <dd><input type="password" class="text" name="confirm_password" value="" /></dd>
           <dt></dt>
           <dd><input type="submit" class="submit" name="save" value="Save" /></dd>
          </dl>
         </form>
         <?php
    }
}


?>
