<?php

namespace app\models;

/**
 * Added by JD
 * See http://www.yiiframework.com/doc-2.0/guide-security-passwords.html
 */
use Yii;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            /**
             * Added by JD
             * See http://www.yiiframework.com/doc-2.0/guide-security-passwords.html
             * $hash = Yii::$app->getSecurity()->generatePasswordHash(password)
             */
            //'password' => 'admin', // old
            'password' => '$2y$13$0/r76NqDc2X4wdb96ezZEuNKctP12crU83I2U5iuNL3RNyHC/b7Fy',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        /**
         * Added by JD
         * See http://www.yiiframework.com/doc-2.0/guide-security-passwords.html
         * $hash = Yii::$app->getSecurity()->generatePasswordHash(password)
         */
        //return $this->password === $password; // old
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}
