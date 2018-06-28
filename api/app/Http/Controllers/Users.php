<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Guard;
use App\User;
use App\Yetki;
use DB;
use JWTAuth;
use Request;
use Validator;

class Users extends Controller
{
    /**
     * yeni kullanıcı ekle
     *
     * @return json array
     */
    public function saveUser()
    {

        if (!Guard::yetki('Yeni')) {
            return response()->json(array('status' => false, 'guard' => false, 'msg' => 'Erişim yok!'));
        }

        $sonuc = Request::all();

        //id varsa update et..
        if (isset($sonuc['id'])) {
            return $this->updateUser();
        }

        //gerekli alanlar
        $validator = Validator::make(Request::all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $e = $validator->errors();
            return response()->json(array('status' => false, 'err'=> $e, 'msg' => 'Lütfen Formu Kontrol Ediniz'));
        }

        //password değerini şifrelememiz gerekiyor. Laravel auth işlemlerinde default olarak bcrypt ile şifrelenmiş değere bakıyor
        $sonuc['password'] = bcrypt($sonuc['password']);

        //kullanıcımızı oluşturalım.
        $user = User::create($sonuc);

        $user = $user->toArray();

        $yetkiler = Request::input('yetkiler');

        foreach ($yetkiler as $yetki) {
            $yetki['user_id'] = $user['id'];
            $yetki['id'] = null;

            Yetki::create($yetki);
        }
        return response()->json(array('status' => true, 'msg' => 'Kayıt eklendi'));
    }

    /**
     * Kullanıcı düzelt
     *
     * @return json array
     */
    public function updateUser()
    {

        if (!Guard::yetki('Guncelle')) {
            return response()->json(array('status' => false, 'guard' => false, 'msg' => 'Erişim yok!'), 401);
        }
        $user = User::find(Request::input('id'));

        $validator = Validator::make(Request::all(), [
            'name' => 'required|min:3,',
            'email' => 'required|email|unique:users,email,' . $user['id'],

        ]);

        if ($validator->fails()) {

            $e = $validator->errors();

            return response()->json(array('status' => false, 'err'=> $e, 'msg' => 'Lütfen Formu Kontrol Ediniz'));
        }

        $input = Request::all();

        if (!empty($input['password'])) {
            $input['password'] = bcrypt(Request::input('password'));
        }

        DB::table('yetki')->where('user_id', '=', $user['id'])->delete();

        $yetkiler = Request::input('yetkiler');
        foreach ($yetkiler as $yetki) {
            $yetki['user_id'] = $user['id'];
            $yetki['id'] = null;

            Yetki::create($yetki);
        }

        $input['role'] = ($user['role'] == 'super') ? 'super' : $input['role'];

        $user->fill($input)->save();

        //kullanıcımızı oluşturalım.
        return response()->json(array('status' => true, 'msg' => 'İşlem Tamam'));
    }

    /**
     * Kullanıcı listesi
     *
     * @return json array
     */
    public function listUsers()
    {

        if (!Guard::yetki('Liste')) {
            return response()->json(array('status' => false, 'guard' => false, 'msg' => 'Erişim yok!'));
        }

        $users = User::orderBy('id', 'DESC')->where('musteri', '!=', "1")->with('yetkiler')->get();
        return response()->json(array('users'=>$users,'status'=>true));

    }

    public function deleteUser()
    {
        if (!Guard::yetki('Sil')) {
            return response()->json(array('status' => false, 'guard' => false, 'msg' => 'Erişim yok!'));
        }
        $id = Request::input('id');

        $user = User::find($id);

        if ($user['role'] == 'admin') {
            //son adminse silme
            $adminUser = User::where('role', '=', 'admin')->count();
            if ($adminUser === 1) {
                return response()->json(array('status' => false, 'msg' => 'Son Admin Kullanıcısı Silinemez'));
            } else {
                $sonuc = $user->delete();
            }

        } else {
            $sonuc = $user->delete();
        }

        if ($sonuc) {
            return response()->json(array('status' => true, 'msg' => 'Kayıt Silindi'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Kayıt Silinemedi'));
        }
    }

    public function yetkiler()
    {
        //default yetkilerler db'yi donat
        //$this->yetkiDefault();

        $bolum = Request::input('bolum');
        $user = Request::get('gUser');
        $yetkiler = User::
            leftJoin('yetki', 'users.id', '=', 'yetki.user_id')
            ->where('users.id', '=', $user['id'])
            ->where('yetki.bolum', '=', $bolum)
            ->first(['role', 'giris', 'bolum', 'bolumAdi', 'yeni', 'duzelt', 'sil']);

        return response()->json($yetkiler);

    }

    public function guard()
    {
        $user = \App\User::find(Request::get('gUser')->id);

        $sonuc = array(
            'name' => $user->name,
            'id' => $user->id,
            'role' => empty($user->role) ? 'user' : $user->role,
        );

      
        return response()->json($sonuc);
       

    }

    public function profilGetir()
    {
        $access_token = Request::header('Authorization');
        $user = JWTAuth::toUser(substr($access_token, 7));
        return response()->json($user);
    }

    public function profilKaydet()
    {

        $access_token = Request::header('Authorization');
        $user = JWTAuth::toUser(substr($access_token, 7));
        $user = User::find($user['id']);

        $validator = Validator::make(Request::all(), [
            'name' => 'required|min:3,',
            'email' => 'required|email|unique:users,email,' . $user['id'],

        ]);

        if ($validator->fails()) {
            $e = $validator->errors();
            return response()->json(array('status' => false, 'msg' => $e));
        }
        $input = Request::all();
        if (!empty($input['password'])) {
            $input['password'] = bcrypt(Request::input('password'));
        }

        $user->fill($input)->save();

        //kullanıcımızı oluşturalım.
        return response()->json(array('status' => true, 'msg' => 'İşlem Tamam'));
    }

    
}
