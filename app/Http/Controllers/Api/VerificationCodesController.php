<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Service\Api\SmsService;
use Illuminate\Http\Request;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request,SmsService $smsService)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaData['phone'];

        $key = 'verificationCode_' . str_random(15);
        $expireAt = now()->addMinute(10);

        if (!app()->environment('production')) {
            $code='123456';
        }else{
            $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT);
            $smsService->handleSendPhoneCode($phone,$code);
        }

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expireAt);
        // 清除图片验证码缓存
        \Cache::forget($request->captcha_key);

        return $this->response->array(['key' => $key, 'expired_at' => $expireAt->toDateString()])->setStatusCode(201);
    }
}
