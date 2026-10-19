<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 * @property string|null $role
 * @property string|null $region_id
 * @property string|null $province_id
 * @property string|null $city_municipality_id
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_PROVINCIAL_ENCODER = 'provincial_encoder';
    public const ROLE_CITY_ENCODER = 'city_encoder';
    public const ROLE_PROVINCIAL_SUPERVISOR = 'provincial_supervisor';
    public const ROLE_CITY_SUPERVISOR = 'city_supervisor';
    public const ROLE_REGIONAL_VIEWER = 'regional_viewer';
    public const ROLE_PROVINCIAL_VIEWER = 'provincial_viewer';
    public const ROLE_CITY_VIEWER = 'city_viewer';
    public const ROLE_VIEWER = 'viewer';

    public function isSuperAdmin(): bool
    {
        return $this->getRoleId() === self::ROLE_SUPER_ADMIN;
    }

    public function getRoleId(): ?string
    {
        $role = $this->role ?? $this->getRoleAssignment();
        return $role ? strtolower(trim($role)) : null;
    }

    public function getRoleAssignment(): ?string
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->getId());
        return $roles ? strtolower(array_key_first($roles)) : null;
    }

    public function hasRole(string $role): bool
    {
        return $this->getRoleId() === strtolower(trim($role));
    }

    public function getAllowedRoadClassifications(): array
{
    switch ($this->getRoleId()) {

        case self::ROLE_PROVINCIAL_ENCODER:
            return [
                'Provincial'
            ];

        case self::ROLE_CITY_ENCODER:
            return [
                'City',
                'Barangay'
            ];

        case 'municipal_encoder':
            return [
                'Municipal',
                'Barangay'
            ];

        default:
            return [
                'Provincial',
                'City',
                'Municipal',
                'Barangay'
            ];
    }
}

    public function isEncoder(): bool
    {
        return in_array(
            $this->getRoleId(),
            [self::ROLE_PROVINCIAL_ENCODER, self::ROLE_CITY_ENCODER],
            true
        );
    }

    public function hasPermission(string $permission, array $params = []): bool
    {
        if (Yii::$app->user->can($permission, $params)) {
            return true;
        }

        if ($permission === 'road.create') {
            return $this->isEncoder();
        }

        return false;
    }

    public function canAccessJurisdiction(): bool
    {
        return $this->isSuperAdmin() || !empty($this->region_id) || !empty($this->province_id) || !empty($this->city_municipality_id);
    }

    public function canAccessRoad(array $road): bool
    {
        // Super Admin has access to all geographic areas.
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getRoleId();

        $regionCode = isset($road['RegionCode'])
            ? (string) $road['RegionCode']
            : null;

        $provinceCode = isset($road['ProvinceCo'])
            ? (string) $road['ProvinceCo']
            : null;

        $cityMunicipalityCode = isset($road['CityMunCod'])
            ? (string) $road['CityMunCod']
            : null;

        switch ($role) {

            // Regional access
            case self::ROLE_REGIONAL_VIEWER:
                return !empty($this->region_id)
                    && $regionCode === (string) $this->region_id;

            // Provincial access
            case self::ROLE_PROVINCIAL_ENCODER:
            case self::ROLE_PROVINCIAL_SUPERVISOR:
            case self::ROLE_PROVINCIAL_VIEWER:
                return !empty($this->province_id)
                    && $provinceCode === (string) $this->province_id;

            // City / Municipality access
            case 'municipal_encoder':
            case self::ROLE_CITY_ENCODER:
            case self::ROLE_CITY_SUPERVISOR:
            case self::ROLE_CITY_VIEWER:
                return !empty($this->city_municipality_id)
                    && $cityMunicipalityCode === (string) $this->city_municipality_id;

            // Generic viewer:
            // use the most specific jurisdiction assigned to the user.
            case self::ROLE_VIEWER:

                if (!empty($this->city_municipality_id)) {
                    return $cityMunicipalityCode === (string) $this->city_municipality_id;
                }

                if (!empty($this->province_id)) {
                    return $provinceCode === (string) $this->province_id;
                }

                if (!empty($this->region_id)) {
                    return $regionCode === (string) $this->region_id;
                }

                return false;

            default:
                return false;
        }
    }

    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['role', 'region_id', 'province_id', 'city_municipality_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds an active user by email address.
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne([
            'email' => trim($email),
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
