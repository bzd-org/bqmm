<?php
namespace Api\Controller;
use Common\Controller\AppframeController;
class GuestbookController extends AppframeController{
	
	protected $guestbook_model;
	
	function _initialize() {
		parent::_initialize();
		$this->guestbook_model=D("Common/Guestbook");
	}
	
	function index(){
		
	}
	
	function addmsg(){
		if(!sp_check_verify_code()){
			$this->error("验证码错误！");
		}
		
		if (IS_POST) {
			if ($this->guestbook_model->create()) {
				$result=$this->guestbook_model->add();
				if ($result!==false) {
					$this->success("留言成功！");
				} else {
					$this->error("留言失败！");
				}
			} else {
				$this->error($this->guestbook_model->getError());
			}
		}
		
	}

	//保存留言
	public function savemsg()
	{
		if (IS_POST) {
			if ($this->guestbook_model->create()) {
				$result=$this->guestbook_model->add();
				if ($result!==false) {
					$full_name = $_REQUEST['full_name'];
					$title = $_REQUEST['title'];
					$email = $_REQUEST['email'];
					$msg = $_REQUEST['msg'];

					//发送邮件
					require(VENDOR_PATH."PHPMailer-5.2.14/class.phpmailer.php");
					require(VENDOR_PATH."PHPMailer-5.2.14/class.smtp.php");
					$mail = new \PHPMailer();
			        $mail->IsSMTP();                  // send via SMTP
			        // $mail->SMTPDebug = 1;
			        $mail->SMTPAuth = true;           // turn on SMTP authentication
			        $mail->SMTPSecure = "ssl"; // 安全协议
			        $mail->Host = "smtp.qq.com";   // SMTP servers
			        $mail->Port = 465;
			        $mail->Username = "250175411@qq.com";     // SMTP username  注意：普通邮件认证不需要加 @域名
			        $mail->Password = "snqsraoqvthgbiaf"; // SMTP password
			        $mail->From = "250175411@qq.com";      // 发件人邮箱
			        $mail->FromName =  "表情MM官网-留言板";  // 发件人    

			        $mail->CharSet = "GB2312";   // 这里指定字符集！    
			        $mail->Encoding = "base64";    
			        $mail->AddAddress('250175411@qq.com',"似颜绘");  // 收件人邮箱和姓名    

			        $mail->IsHTML(true);
			         // 邮件主题    
			        $mail->Subject = $subject;    
			        // 邮件内容    
			        $mail->Body = '
						<html><head>
						<meta http-equiv="Content-Language" content="zh-cn">
						<meta http-equiv="Content-Type" content="text/html; charset=GB2312">
						</head>
						<body>
						'.$full_name.'<br/>
						'.$email.'<br/>
						'.$title.'<br/>
						'.$msg.'<br/>
						</body>
						</html>
				    ';
				    $mail->AltBody ="text/html";
				    $mail->Send();

					// $this->success("留言成功！");
					$this->majaxReturn(0, '留言成功！');
				} else {
					// $this->error("留言失败！");
					$this->majaxReturn(1, '留言失败！');
				}
			} else {
				// $this->error($this->guestbook_model->getError());
				$this->majaxReturn(1, '留言失败！');
			}
		}
	}
}