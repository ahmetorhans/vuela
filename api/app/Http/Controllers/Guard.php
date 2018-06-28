<?php

namespace App\Http\Controllers;

use App\User;
use App\Yetkidefault;
use DB;
use JWTAuth;
use Request;
use Mail;

class Guard extends Controller
{

    /**
     * Jwt Login
     *
     * @return json
     */
    public function login()
    {

        //post'dan gelen email ve password değerlerini kontrol et. Doğruysa token oluştur ve gönder

        $credentials = Request::only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false, 'msg' => 'Bilgiler hatalı']);
            }
        } catch (JWTException $e) {

            return response()->json(['status' => false, 'msg' => 'Token oluşturulamadı'], 500);
        }
        return response()->json(compact('token'));
    }

    public static function yetki($islem)
    {

        self::yetkiDefault();

        $user = Request::get('gUser');

        $aut = \App\Yetki::where('user_id', $user->id)->first();

        if ($user->role == 'admin') {
            return true;
        }

        if (empty($islem)) {
            return false;
        }
        if (empty($aut->id)) {
            return false;
        }
        if (empty($user->id)) {
            return false;
        }

        //mevcut controller ve method'u bul..
        $currentAction = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $currentAction);
        $controller = preg_replace('/.*\\\/', '', $controller);

        //gelen Controller auth tablosundaki ile aynı ise
        if ($aut->bolum == $controller) {
            switch ($islem) {
                case 'Liste':
                    if ($aut->giris == 1) {
                        return true;
                    }
                    break;
                case 'Yeni':
                    if ($aut->yeni == 1) {
                        return true;
                    }
                    break;
                case 'Guncelle':
                    if ($aut->duzelt == 1) {
                        return true;
                    }
                    break;
                case 'Sil':
                    if ($aut->sil == 1) {
                        return true;
                    }
                    break;

                default:
                    return false;
                    break;
            }

        }
        //default false;
        return false;
    }
    private static function yetkiDefault()
    {

        $user = User::get();

        $yetkiler = \App\Yetkidefault::all('bolum', 'bolumAdi')->toArray();

        foreach ($user as $val) {

            foreach ($yetkiler as $ny) {

                $yetki = \App\Yetki::where('user_id', '=', $val['id'])
                    ->where('bolum', '=', $ny['bolum'])
                    ->get();

                if (count($yetki) == 0) {
                    DB::table('yetki')->insert(
                        ['bolum' => $ny['bolum'], 'user_id' => $val['id'], 'bolumAdi' => $ny['bolumAdi']]
                    );
                }

            }

        }
        $yetki = \App\Yetkidefault::orderBy('id', 'DESC')->get();
        return response()->json($yetki);
    }
    public function resetPassword()
    {

        $user = User::where('email', '=', Request::input('email'))->first();

        if (count($user) === 1) {
            $newPasswordStr = str_random(8);

            $user->password = bcrypt($newPasswordStr);
            $user->save();

            $user->newPasswordStr = $newPasswordStr;
            try {
                Mail::send('resetMail', $user->toArray(), function ($message) {
                    $message->to(Request::input('email'), '')->subject
                        ('Parola Sıfırlama');
                    $message->from('send@wmatik.com', 'Parola Sıfırlama');
                });
            } catch (\Exception $e) {
                return response()->json(array('status' => false, 'msg' => $e->getMessage()));
            }

            return response()->json(array('status' => true, 'msg' => 'Parolanız Eposta Adresine Gönderildi.'));
        }
        return response()->json(array('status' => false, 'msg' => 'Eposta Kayıtlı Değil'));

    }

}
