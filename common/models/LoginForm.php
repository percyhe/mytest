<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    /**
     * @return bool
     */
    public function loginAdmin()
    {
        if ($this->validate()) {
            
            $controller = Yii::$app->controller->id;
            $action = Yii::$app->controller->action->id;
            $permissionName = '/'.$controller.'/'.$action;
           
          if($user = $this->getUser()){
           
              if(!Yii::$app->authManager->checkAccess( $user->id , $permissionName )){
                  $this->addError('username', 'You don\'t have permission to login.');
                  
              }
          
            /*   if(!\Yii::$app->user->can($permissionName) && Yii::$app->getErrorHandler()->exception === null){
                  // throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
              
                  $this->addError('username', 'You don\'t have permission to login.');
              } */
              else{
              
                  return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
              }
          }
           
       
         /*    if (User::isUserAdmin($this->username)) {
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            } */
           // $this->addError('username', 'You don\'t have permission to login.');
        } else {
            $this->addError('password', Yii::t('app', 'Incorrect username or password.'));
        }
        return false;
    }
}
