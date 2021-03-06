<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class LoginController extends BaseController
{
	//页面跳转
    public function jump()
	{
		return view('admin.index.jump');
    }
	
    /**
     * 登录页面
     */
	public function login()
	{
		if(isset($_SESSION['admin_user_info']))
		{
			header("Location: ".route('admin'));
			exit;
		}
		
        return view('admin.login.login');
    }
    
    /**
     * 登录处理页面
     */
    public function dologin()
    {
        if(!empty($_POST["username"])){$username = $_POST["username"];}else{$username='';exit;}//用户名
        if(!empty($_POST["pwd"])){$pwd = md5($_POST["pwd"]);}else{$pwd='';exit;}//密码
		
        $admin_user = DB::table('admin_user')->where(array('username' => $username, 'pwd' => $pwd))->orWhere(array('email' => $username, 'pwd' => $pwd))->first();
		
        if($admin_user)
        {
			$admin_user_info = object_to_array($admin_user, 1);
			$admin_user_info['rolename'] = DB::table('admin_user_role')->where(array('id'=>$admin_user->role_id))->value('name');
			
			$_SESSION['admin_user_info'] = $admin_user_info;
			
			DB::table('admin_user')->where(array('id'=>$admin_user->role_id))->update(array('logintime' => time()));
			
			return redirect()->route('admin');
        }
        else
        {
            return redirect()->route('admin_login');
        }
    }

    //退出登录
    public function logout()
    {
		session_unset();
        session_destroy();// 退出登录，清除session
		success_jump('退出成功！', route('home'));
    }
    
    //密码恢复
    public function recoverpwd()
    {
        $data["username"] = "admin888";
        $data["pwd"] = "21232f297a57a5a743894a0e4a801fc3";
        
        if(DB::table('admin_user')->where('id', 1)->update($data))
        {
            success_jump('密码恢复成功！', route('admin_login'));
        }
		else
		{
			error_jump('密码恢复失败！', route('home'));
		}
    }
	
	/**
     * 判断用户名是否存在
     */
    public function userexists()
    {
		$map['username'] = "";
        if(isset($_POST["username"]) && !empty($_POST["username"]))
        {
            $map['username'] = $_POST["username"];
        }
		else
		{
			return 0;
		}
        
        return DB::table("admin_user")->where($map)->count();
    }
	
	//测试
	public function test()
	{
		//管理员菜单
		/* for ($x=1; $x<=103; $x++)
		{
			DB::table('access')->insert(['role_id' => 1, 'menu_id' => $x]);
		} */
		
		echo '123';
    }
}