<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class SecurityController extends Controller
{
    /**
     * Display security settings page
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('admin.security.index', [
            'user' => $user,
            'two_factor_enabled' => !is_null($user->two_factor_secret),
            'two_factor_confirmed' => !is_null($user->two_factor_confirmed_at)
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.security.index')
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    /**
     * Enable two factor authentication (Using Fortify)
     */
    public function enableTwoFactor(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $user = auth()->user();
        
        // تفعيل فقط إذا لم يكن مفعل من قبل
        if (is_null($user->two_factor_secret)) {
            $enable($user);
            
            // مسح التأكيد لإجبار المستخدم على تأكيد السر الجديد
            $user->two_factor_confirmed_at = null;
            $user->save();
            
            return redirect()->route('admin.security.qr-code')
                ->with('success', 'تم توليد رمز QR جديد. يرجى مسحه وتأكيد التفعيل.');
        }

        return redirect()->route('admin.security.index')
            ->with('info', 'المصادقة الثنائية مفعلة بالفعل. لإعادة التعيين، قم بتعطيلها أولاً.');
    }

    /**
     * Confirm two factor authentication
     */
    public function confirmTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        
        if ($user->two_factor_secret && is_null($user->two_factor_confirmed_at)) {
            // Verify the code using Google2FA
            $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
            $secret = decrypt($user->two_factor_secret);
            
            $valid = $google2fa->verifyKey($secret, $request->code);
            
            if ($valid) {
                $user->two_factor_confirmed_at = now();
                $user->save();
                
                return redirect()->route('admin.security.index')
                    ->with('success', 'تم تأكيد المصادقة الثنائية بنجاح');
            }
            
            return redirect()->back()
                ->withErrors(['code' => 'الرمز المدخل غير صحيح'])
                ->withInput();
        }

        return redirect()->route('admin.security.index')
            ->with('error', 'فشل تأكيد المصادقة الثنائية');
    }

    /**
     * Disable two factor authentication (Using Fortify)
     */
    public function disableTwoFactor(Request $request, DisableTwoFactorAuthentication $disable)
    {
        $user = auth()->user();
        
        if (!is_null($user->two_factor_secret)) {
            $disable($user);

            return redirect()->route('admin.security.index')
                ->with('success', 'تم إيقاف المصادقة الثنائية بنجاح');
        }

        return redirect()->route('admin.security.index')
            ->with('info', 'المصادقة الثنائية غير مفعلة');
    }

    /**
     * Show QR Code for 2FA setup
     */
    public function showQrCode()
    {
        $user = auth()->user();
        
        if (is_null($user->two_factor_secret)) {
            return redirect()->route('admin.security.index')
                ->with('error', 'يجب تفعيل المصادقة الثنائية أولاً');
        }

        $qrCode = null;
        try {
            $qrCode = $user->twoFactorQrCodeSvg();
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }

        return view('admin.security.qr-code', [
            'user' => $user,
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes()
    {
        $user = auth()->user();
        
        if (is_null($user->two_factor_recovery_codes)) {
            return redirect()->route('admin.security.index')
                ->with('error', 'لا توجد رموز استرداد متاحة');
        }

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return view('admin.security.recovery-codes', [
            'codes' => $codes
        ]);
    }

    /**
     * Generate new recovery codes
     */
    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate)
    {
        $user = auth()->user();
        
        if (!is_null($user->two_factor_secret)) {
            $generate($user);
            
            return redirect()->route('admin.security.recovery-codes')
                ->with('success', 'تم إنشاء رموز استرداد جديدة');
        }

        return redirect()->route('admin.security.index')
            ->with('error', 'يجب تفعيل المصادقة الثنائية أولاً');
    }
}
