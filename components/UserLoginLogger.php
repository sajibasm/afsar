<?php
namespace app\components;

use app\models\UserIpWhitelist;
use app\models\UserLoginLogs;
use Yii;
use yii\base\Component;
use yii\helpers\Url;

class UserLoginLogger extends Component
{

    public function getIP()
    {
        $ip = Yii::$app->request->getUserIP();

        if (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            preg_match('/^192\.168\./', $ip) ||
            preg_match('/^10\./', $ip) ||
            preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)
        ) {
            // Use a public IP (like Google DNS) for testing location
            $ip = '8.8.8.8';
        }

        return $ip;
    }

    public function logLogin($user)
    {
        try {

            $ip = $this->getIP();
            $userAgent = Yii::$app->request->userAgent;

            $location = $this->getLocationFromIP($ip);
            if (!$location) {
                Yii::warning("IP Geolocation failed for IP: $ip");
                return;
            }

            // 1. Check if IP already exists in last 20 logs
            $ipExists = UserLoginLogs::find()
                ->where(['user_id' => $user->user_id, 'ip_address' => $ip])
                ->orderBy(['created_at' => SORT_DESC])
                ->exists();

            // 2. Check if location (city, region, country) already exists in last 20 logs
            $locationExists = UserLoginLogs::find()
                ->where([
                    'user_id' => $user->user_id,
                    'city' => $location['city'],
                    'region' => $location['state_prov'],
                    'country' => $location['country_name'],
                ])
                ->orderBy(['created_at' => SORT_DESC])
                ->exists();

            // 3. Check if user has 2FA enabled
            $is2FAEnabled = !empty($user->is_2fa_enabled) && $user->is_2fa_enabled == true;

            // 🚨 Decide whether to send alert
            $sendAlert = !($ipExists || $locationExists || $is2FAEnabled);

            if(!$sendAlert){
                // Save login record
                $this->addUserLoginLog($user->user_id, $location, $userAgent);
                // Clean up logs older than 90 days
                $this->removeUserLoginLog($user->user_id);
           }

            // Send alert if needed
            if ($sendAlert) {
                $this->addUserIpWhitelist($user->user_id, $location['ip'], $userAgent);
                $this->sendLocationChangeEmail($user, $location, $userAgent);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Yii::error("Error logging user login: " . $e->getMessage());
        }
    }


    public function removeUserLoginLog($userId)
    {
        UserLoginLogs::deleteAll([
            'AND',
            ['user_id' => $userId],
            ['<', 'created_at', new \yii\db\Expression("NOW() - INTERVAL 30 DAY")]
        ]);
    }

    public function addUserLoginLog($userId, $location, $userAgent)
    {
        $log = new UserLoginLogs([
            'user_id' => $userId,
            'ip_address' => $location['ip'],
            'city' => $location['city'],
            'region' => $location['state_prov'],
            'country' => $location['country_name'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'country_flag' => $location['country_flag'],
            'user_agent' => $userAgent,
        ]);
        return $log->save(false);
    }

    public function addUserIpWhitelist($userId, $ip, $userAgent)
    {
        $userIpWhitelist = new UserIpWhitelist();
        $userIpWhitelist->user_id = $userId;
        $userIpWhitelist->ip_address = $ip;
        $userIpWhitelist->user_agent = $userAgent;
        return $userIpWhitelist->save(false);
    }


    public function getLocationFromIP($ip)
    {
        $apiKey = Yii::$app->params['ipgeolocation_api_key'];
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}";

        $response = @file_get_contents($url);
        return $response ? json_decode($response, true) : null;
    }

    private function sendLocationChangeEmail($user, $location, $userAgent)
    {
        try {
            $resetPasswordUrl = Url::to(['/admin/user/request-password-reset'], true);
            $token = hash_hmac('sha256', $user->user_id . $location['ip'], Yii::$app->params['secretKey']);
            $url = Url::to(['/admin/user/ip-confirm', 'uid' => $user->user_id, 'ip' => $location['ip'], 'token' => $token], true);
            Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setBcc([Yii::$app->params['adminEmail']])
                ->setTo($user->email)
                ->setSubject("New login from {$location['city']}, {$location['country_name']} {$location['country_flag_emoji']}")
                ->setHtmlBody("
                    <p>Hi {$user->username},</p>
                    <p>We noticed a login from a new location. Here are the details:</p>
                    <ul>
                        <li><b>IP:</b> {$location['ip']}</li>
                        <li><b>City:</b> {$location['city']}</li>
                        <li><b>Region:</b> {$location['state_prov']}</li>
                        <li><b>Country:</b> {$location['country_name']} {$location['country_flag_emoji']}</li>
                        <li><b>Device Info:</b> {$userAgent}</li>
                    </ul>
                
                    <p>If this was you, click the button below to whitelist this IP and avoid future alerts:</p>
               
                    <p>
                        <a href='{$url}' 
                           style='display: inline-block; padding: 12px 20px; font-size: 16px; 
                                  background-color: #28a745; color: white; text-decoration: none; 
                                  border-radius: 6px; font-weight: bold;'>
                           Yes, this was me – Whitelist IP
                        </a>
                    </p>
                
                    <p>If this wasn't you, please <a href='{$resetPasswordUrl}'>change your password</a> immediately.</p>")
                ->send();
        }catch (\Exception $e){
            Yii::error("Error sending location change email: " . $e->getMessage());
            print_r($e->getMessage());
        }

    }
}
