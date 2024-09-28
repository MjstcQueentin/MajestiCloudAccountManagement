<?php

/**
 * PHP interface for MajestiCloud API
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 * @version 3
 */
class MajestiCloudAPI
{
    const API_ROOT = API_ROOT;
    const API_KEY = API_KEY;

    const CLIENT_ID = CLIENT_ID;
    const CLIENT_REDIRECT_URI = CLIENT_REDIRECT_URI;
    private const CLIENT_SECRET = CLIENT_SECRET;

    private $ch;
    private $access_token;

    function __construct($access_token = null)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, ["X-MAJESTICLOUD-CLIENT: " . $_SERVER["REMOTE_ADDR"]]);

        if (!empty($access_token)) {
            $this->access_token = $access_token;
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer " . $access_token,
                "X-MAJESTICLOUD-CLIENT: " . $_SERVER["REMOTE_ADDR"]
            ]);
        }
    }

    function __destruct()
    {
        curl_close($this->ch);
    }

    private function parse_response($response, $throw_4xx = true)
    {
        $decoded_message = json_decode($response, true) ? json_decode($response, true)["message"] : urlencode($response);

        if (curl_errno($this->ch) != 0) {
            throw new MajestiCloudAPIException(curl_error($this->ch));
        } elseif ($throw_4xx && curl_getinfo($this->ch, CURLINFO_HTTP_CODE) >= 400) {
            throw new MajestiCloudAPIException(curl_getinfo($this->ch, CURLINFO_HTTP_CODE) . " : " . $decoded_message);
        } elseif (curl_getinfo($this->ch, CURLINFO_HTTP_CODE) >= 500) {
            throw new MajestiCloudAPIException(curl_getinfo($this->ch, CURLINFO_HTTP_CODE) . " : " . $decoded_message);
        } else {
            return json_decode($response, true);
        }
    }

    public function oauth_authorize($email, $pwd, $client_uuid, $redirect_uri, $code_challenge = null, $code_challenge_method = null)
    {
        $fields = [
            "username" => trim($email),
            "password" => $pwd,
            "client_uuid" => $client_uuid,
            "redirect_uri" => $redirect_uri,
            "api_key" => self::API_KEY
        ];

        if (!empty($code_challenge) && !empty($code_challenge_method)) {
            $fields["code_challenge"] = $code_challenge;
            $fields["code_challenge_method"] = $code_challenge_method;
        }

        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/oauth/authorize.php",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields)
        ]);

        return $this->parse_response(curl_exec($this->ch), false);
    }

    public function oauth_token($authorization_code, $code_verifier = null)
    {
        $fields = [
            "authorization_code" => trim($authorization_code),
            "client_uuid" => self::CLIENT_ID,
            "client_secret" => self::CLIENT_SECRET
        ];

        if (!empty($code_verifier)) {
            $fields["code_verifier"] = $code_verifier;
        }

        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/oauth/token.php",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields)
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function client_get($uuid = "")
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/client.php?uuid=" . $uuid,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        return $this->parse_response(curl_exec($this->ch))["data"];
    }

    public function client_patch($client = []) {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/client.php",
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => $client
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function sessions_get()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/session/",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        return $this->parse_response(curl_exec($this->ch))["data"];
    }

    public function session_delete($uuid)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/session/?uuid=" . $uuid,
            CURLOPT_CUSTOMREQUEST => "DELETE",
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function sessions_current_get()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/session/current.php",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        return $this->parse_response(curl_exec($this->ch))["data"];
    }

    public function session_current_delete()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/session/current.php",
            CURLOPT_CUSTOMREQUEST => "DELETE",
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_get()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        return $this->parse_response(curl_exec($this->ch))["data"];
    }

    public function user_post(string $email, string $clear_pwd, string $name)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/",
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query([
                "email" => $email,
                "password" => $clear_pwd,
                "name" => $name,
                "api_key" => self::API_KEY
            ])
        ]);

        return $this->parse_response(curl_exec($this->ch), false);
    }

    public function user_patch($form_data = [])
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/",
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => http_build_query($form_data)
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_delete($reverse = false)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . ($reverse ? "/user/?reverse=true" : "/user/"),
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_password_set($old, $new)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/password.php",
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query([
                "current" => $old,
                "new" => $new
            ])
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_profile_picture_get()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/profile_picture.php",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        $image = curl_exec($this->ch);
        if (curl_getinfo($this->ch, CURLINFO_HTTP_CODE) != 200) {
            $image = file_get_contents(__DIR__ . "/../assets/images/default-profile.png");
            $base64 = base64_encode($image);
            $mime = mime_content_type(__DIR__ . "/../assets/images/default-profile.png");
        } else {
            $base64 = base64_encode($image);
            $mime = curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE);
        }

        return "data:$mime;base64,$base64";
    }

    public function user_profile_picture_set($local_path)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/profile_picture.php",
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => file_get_contents($local_path),
            CURLOPT_HTTPHEADER => [
                'Content-Type: ' . mime_content_type($local_path),
                "Authorization: Bearer " . $this->access_token
            ]
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_profile_picture_delete()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/profile_picture.php",
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_totp_post()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/totp.php",
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => "POST"
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function user_totp_delete()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/totp.php",
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function send_validation_email($for)
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/user/send_validation_email.php?for=" . urlencode($for),
            CURLOPT_HTTPGET => true,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        return $this->parse_response(curl_exec($this->ch));
    }

    public function session_get()
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => self::API_ROOT . "/session/",
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPGET => true
        ]);

        return $this->parse_response(curl_exec($this->ch))["data"];
    }
}

/**
 * MajestiCloud API Exceptions
 */
class MajestiCloudAPIException extends Exception
{
}
