<?php


require_once 'path.php';


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/db/MySQL.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';
require_once PATH_LIB . 'com/mephex/nav/Navigation.php';
require_once PATH_LIB . 'com/mephex/nav/NavItem.php';
require_once PATH_LIB . 'com/mephex/user/User.php';


class SignInFormResponder extends HttpResponder
{
    protected $error;

    protected $forwardTo;
    protected $updateTimezone;


    public function __construct($forwardTo = null, $updateTimezone = false)
    {
        $this->forwardTo = $forwardTo;
        $this->updateTimezone = $updateTimezone;
    }

    public function init($args)
    {
        parent::init($args);
        $this->error = '';
    }

    public function post($args)
    {
        if($this->input->set('signIn'))
        {
            $this->input->set('forwardTo');
            $this->forwardTo = $this->input->get('forwardTo');

            $this->input->set('username');
            $this->input->set('password');
            $this->input->set('clientTime', IntegerInput::getInstance());

            $username = $this->input->get('username');
            $password = $this->input->get('password');
            $clientTime = $this->input->get('clientTime');

            $user = User::setActiveUser($username, $password);
            if(!is_null($user))
            {
                $serverTime = time();
                if($this->updateTimezone && $clientTime >= 0)
                    $user->setTimeDifference($serverTime, intval($clientTime));

                if(!is_null($this->forwardTo))
                    HttpHeader::forwardTo($this->forwardTo);
                else
                    HttpHeader::forwardTo($_SERVER['PHP_SELF']);
            }
            else
            {
                $this->error = 'An invalid username or password was provided. ';
                $this->error .= 'Note that passwords are case-sensitive.';
            }
        }
    }

    public function get($args)
    {
        $onsubmit = '';
        if($this->updateTimezone)
        {
            $onsubmit .= ' onsubmit="javascript: setToLocalTime(\'clientTime\'); return true; "';
        }
        ?>
         <form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-default" method="post"<?php echo $onsubmit; ?>>
        <?php
        if($this->error != '')
        {
            ?>
             <div class="error-message">
              <?php echo $this->error; ?>
             </div>
            <?php
        }
        ?>
          <fieldset>
           <legend>Sign In</legend>
           <div class="field">
            <input type="text" class="text" name="username" id="username" />
            <label><em>Username</em></label>
           </div>
           <div class="field">
            <input type="password" class="text" name="password" />
            <label><em>Password</em></label>
           </div>
          </fieldset>
          <fieldset class="submit">
           <input type="submit" class="submit" name="signIn" value="Sign In" />
         <?php
         if(!is_null($this->forwardTo))
         {
           ?><input type="hidden" name="forwardTo" value="<?php echo $this->forwardTo; ?>" /><?php
         }
         ?>
           <input type="hidden" name="clientTime" id="clientTime" value="-1" />
          </fieldset>
         </form>
        <?php
    }
}


?>
