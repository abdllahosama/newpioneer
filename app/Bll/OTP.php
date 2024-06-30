<?php

namespace App\Bll;

use \App\Models\Otp as OTPModel;

class OTP
{
    protected $module;
    protected $type;
    protected $data;
    protected $email;
    protected $action;
    private int $maxAttempts = 5;

    public function __construct($module, $action, $data, $type = 'phone')
    {
        $this->module = $module;
        $this->action = $action;
        $this->type = $type;
        $this->data = $data;
    }

    public function sendOtp()
    {

        if (!$this->checkMaxAttempts()) {
            return false;
        }
        $otp = $this->generateOtp();
        if(!$otp){
            return false;
        }
        $this->saveOtp($otp);
        return true;
    }

    public function verifyOtp($userOtp)
    {

        $otp = OTPModel::where($this->type, $this->data)
            ->where('otp', $userOtp)
            ->where('module', $this->module)
            ->where('action', $this->action)
            ->where('expire_at', '>', now())
            ->orderBy('id', 'desc')->first();
        if ($otp) {
            $otp->delete();
            return true;
        }
        return false;
    }

    public function resendOtp()
    {
        if (!$this->checkMaxAttempts()) {
            return false;
        }
        $otp = $this->generateOtp();
        if(!$otp){
            return false;
        }
        $this->saveOtp($otp);
        return true;
    }

    public function checkVrifyedData()
    {
        $otp = OTPModel::where($this->type, $this->data)
            ->orderBy('id', 'desc')
            ->where('module', $this->module)
            ->where('action', $this->action)
            ->withTrashed()
            ->first();
        if ($otp && $otp->deleted_at?->addMinutes(10) > now()) {
            return true;
        }
        return false;
    }

    private function generateOtp()
    {

        $otp = rand(1000, 99999);
        $msegat = new Msegat($this->data, $msg = "Your verification is: $otp");
        $msegat =  $msegat->sendSMS();
        if(!$msegat){
            return false;
        }
        return $otp;
    }

    private function checkMaxAttempts()
    {
        // get attempts last 24 hours
        $attempts = OTPModel::where($this->type, $this->data)
            ->where('created_at', '>', now()->subDay())
            ->withTrashed()
            ->count();

        if ($attempts >= $this->maxAttempts) {
            return false;
        }
        return true;
    }

    private function saveOtp($otp)
    {
        OTPModel::create([
            $this->type => $this->data,
            'module' => $this->module,
            'action' => $this->action,
            'otp' => $otp,
            'expire_at' => now()->addMinutes(5)
        ]);
    }

}
