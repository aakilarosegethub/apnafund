<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseApiController
{
    /**
     * User Registration
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['name']) || empty($data['email']) || empty($data['mobile']) || empty($data['password']) || empty($data['ccode'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Something Went Wrong!"
                ], 401);
            }

            // Split name into firstname and lastname
            $nameParts = explode(' ', trim($data['name']), 2);
            $firstname = strip_tags($this->h->real_string($nameParts[0]));
            $lastname = isset($nameParts[1]) ? strip_tags($this->h->real_string($nameParts[1])) : '';
            $email = strip_tags($this->h->real_string($data['email']));
            $mobile = strip_tags($this->h->real_string($data['mobile']));
            $ccode = strip_tags($this->h->real_string($data['ccode']));
            $password = strip_tags($this->h->real_string($data['password']));
            
            // Generate username from name
            $usernameBase = strtolower(preg_replace('/[^a-z0-9]/', '', $firstname . ($lastname ? $lastname : '')));
            if (empty($usernameBase)) {
                $usernameBase = 'user' . time();
            }
            $username = $usernameBase;
            $counter = 1;
            // Check if username exists and make it unique
            while (true) {
                $checkUsername = $this->h->queryfire("select * from users where username='" . $username . "'");
                if (!$checkUsername || $checkUsername->num_rows == 0) {
                    break;
                }
                $username = $usernameBase . $counter;
                $counter++;
            }

            // Check if users table exists, if not return helpful error
            $tableCheck = $this->h->queryfire("SHOW TABLES LIKE 'users'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                return response()->json([
                    "ResponseCode" => "500",
                    "Result" => "false",
                    "ResponseMsg" => "Users table does not exist. Please run migrations or create the table.",
                    "Error" => "Table 'users' doesn't exist. Run: php artisan migrate OR create table using create_users_table.sql"
                ], 500);
            }

            // Check if mobile already exists
            $checkmob = $this->h->queryfire("select * from users where mobile='" . $mobile . "' and country_code='" . $ccode . "'");
            if ($checkmob && $checkmob->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Mobile Number Already Used!"
                ], 401);
            }

            // Check if email already exists
            $checkemail = $this->h->queryfire("select * from users where email='" . $email . "'");
            if ($checkemail && $checkemail->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email Address Already Used!"
                ], 401);
            }

            $table = "users";
            // Using users table fields: firstname, lastname, username, email, mobile, password, country_code, status
            // created_at will be automatically set by Laravel
            $field_values = ["firstname", "lastname", "username", "email", "mobile", "password", "country_code", "status"];
            $data_values = [$firstname, $lastname, $username, $email, $mobile, $password, $ccode, 1];

            $check = $this->h->insertDataId_Api($field_values, $data_values, $table);

            if ($check) {
                $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, password, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where id=" . $check . "");
                if ($c && $c->num_rows > 0) {
                    $userData = $c->fetch_assoc();
                    // Combine firstname and lastname as name for API response
                    $userData['name'] = trim(($userData['firstname'] ?? '') . ' ' . ($userData['lastname'] ?? ''));
                    // Format rdate if needed
                    if (isset($userData['rdate']) && $userData['rdate']) {
                        $userData['rdate'] = date("Y-m-d H:i:s", strtotime($userData['rdate']));
                    } else {
                        // If created_at is null, use current timestamp
                        $userData['rdate'] = date("Y-m-d H:i:s");
                    }
                    // Try to get settings, but don't fail if table doesn't exist
                    $set = $this->h->fetchData("SELECT * FROM `settings` LIMIT 1");
                    if (!$set) {
                        // Try alternative table name
                        $set = $this->h->fetchData("SELECT * FROM `tbl_setting` LIMIT 1");
                    }
                    $currency = '';
                    if ($set) {
                        $currency = $set['currency'] ?? $set['site_currency'] ?? '';
                    }

                    return response()->json([
                        "UserLogin" => $userData,
                        "currency" => $currency,
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "ResponseMsg" => "Sign Up Done Successfully!"
                    ]);
                }
            }

            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Registration Failed! Please try again."
            ], 401);
        } catch (\Exception $e) {
            // Log error details
            $errorMsg = $e->getMessage();
            $errorTrace = $e->getTraceAsString();
            
            // Try to log, but don't fail if logging fails
            try {
                \Log::error('Registration Error: ' . $errorMsg . ' | Trace: ' . substr($errorTrace, 0, 500));
            } catch (\Exception $logError) {
                // Logging failed, continue anyway
            }
            
            // Return error response with more details in development
            $responseMsg = "An error occurred during registration. Please try again.";
            if (config('app.debug')) {
                $responseMsg = "Error: " . $errorMsg;
            }
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => $responseMsg,
                "Error" => config('app.debug') ? $errorMsg : null
            ], 500);
        }
    }

    /**
     * User Login
     */
    public function login(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['mobile']) || empty($data['password']) || empty($data['ccode'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $mobile = strip_tags($this->h->real_string($data['mobile']));
        $password = strip_tags($this->h->real_string($data['password']));
        $ccode = strip_tags($this->h->real_string($data['ccode']));

        $chek = $this->h->queryfire("select * from users where (mobile='" . $mobile . "' or email='" . $mobile . "') and status = 1 and password='" . $password . "' and country_code='" . $ccode . "'");
        $status = $this->h->queryfire("select * from users where status = 1");

        if ($status && $status->num_rows != 0) {
            if ($chek && $chek->num_rows != 0) {
                $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, password, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where (mobile='" . $mobile . "' or email='" . $mobile . "') and status = 1 and password='" . $password . "'");
                $c = $c->fetch_assoc();
                // Combine firstname and lastname as name for API response
                if ($c) {
                    $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
                    // Format rdate if needed
                    if (isset($c['rdate'])) {
                        $c['rdate'] = date("Y-m-d H:i:s", strtotime($c['rdate']));
                    }
                }

                return response()->json([
                    "UserLogin" => $c,
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "Login successfully!"
                ]);
            } else {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid Email/Mobile No or Password!!!"
                ], 401);
            }
        } else {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Your profile has been blocked by the administrator, preventing you from using our app as a regular user."
            ], 401);
        }
    }

    /**
     * Forget Password
     */
    public function forgetPassword(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['mobile']) || empty($data['password']) || empty($data['ccode'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went wrong try again !"
            ], 401);
        }

        $mobile = strip_tags($this->h->real_string($data['mobile']));
        $password = strip_tags($this->h->real_string($data['password']));
        $ccode = strip_tags($this->h->real_string($data['ccode']));

        $counter = $this->h->queryfire("select * from users where mobile='" . $mobile . "' and country_code='" . $ccode . "'");

        if ($counter && $counter->num_rows != 0) {
            $table = "users";
            $field = array('password' => $password);
            $where = "where mobile='" . $mobile . "' and country_code='" . $ccode . "'";

            $check = $this->h->updateData_Api($field, $table, $where);

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Password Changed Successfully!!!!!"
            ]);
        } else {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "mobile Not Matched!!!!"
            ], 401);
        }
    }

    /**
     * Social Login
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['email'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $email = strip_tags($this->h->real_string($data['email']));

        $chek = $this->h->queryfire("select * from users where email='" . $email . "'");

        if ($chek && $chek->num_rows != 0) {
            $status = $this->h->queryfire("select * from users where status = 1 and email='" . $email . "'");
            if ($status && $status->num_rows != 0) {
                $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, password, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where email='" . $email . "'");
                $c = $c->fetch_assoc();
                // Combine firstname and lastname as name for API response
                if ($c) {
                    $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
                    // Format rdate if needed
                    if (isset($c['rdate'])) {
                        $c['rdate'] = date("Y-m-d H:i:s", strtotime($c['rdate']));
                    }
                }

                return response()->json([
                    "UserLogin" => $c,
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "Login successfully!"
                ]);
            } else {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Your profile has been blocked by the administrator, preventing you from using our app as a regular user."
                ], 401);
            }
        } else {
            return response()->json([
                "ResponseCode" => "201",
                "Result" => "false",
                "ResponseMsg" => "Account Not Found!!"
            ], 201);
        }
    }

    /**
     * Check Mobile Number
     */
    public function checkMobile(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['mobile']) || empty($data['ccode'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $mobile = strip_tags($this->h->real_string($data['mobile']));
        $code = strip_tags($this->h->real_string($data['ccode']));

        $chek = $this->h->queryfire("select * from users where mobile='" . $mobile . "' and country_code='" . $code . "'")->num_rows;

        if ($chek != 0) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Already Exist Mobile Number!"
            ], 401);
        } else {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "New Number!"
            ]);
        }
    }
}

