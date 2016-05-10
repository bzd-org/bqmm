<?php
namespace Api\Controller;
use Common\Controller\AppframeController;
class GuestbookController extends AppframeController{
	
	protected $guestbook_model;
	
	function _initialize() {
		parent::_initialize();
		$this->guestbook_model=D("Common/Guestbook");
		$this->sdkaccess_model=D("Common/Sdkaccess");
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
					$full_name = htmlentities($_REQUEST['full_name']);
					$title = htmlentities($_REQUEST['title']);
					$email = htmlentities($_REQUEST['email']);
					$msg = htmlentities($_REQUEST['msg']);

					//发送邮件
					$body = '
			            <h3>姓名：'.$full_name.'</h3>
			            <h3>邮箱：'.$email.'</h3>
			            <h3>公司：'.$title.'</h3>
			            <h3>留言：'.$msg.'</h3>
			        ';
					$this->sendmail($email, $full_name, $title, $body);

					// $this->success("留言成功！");
					$this->majaxReturn(0, '留言成功！');
				} else {
					// $this->error("留言失败！");
					$this->majaxReturn(1, '留言失败！');
				}
			} else {
				// $this->error($this->guestbook_model->getError());
				$this->majaxReturn(1, $this->guestbook_model->getError());
			}
		}
	}

	//保存留言
	public function savesdkaccess()
	{
		if (IS_POST) {
			if ($this->sdkaccess_model->create()) {
				$result=$this->sdkaccess_model->add();
				if ($result!==false) {
					$product = htmlentities($_REQUEST['product']);
					$fullname = htmlentities($_REQUEST['fullname']);
					$company = htmlentities($_REQUEST['company']);
					$email = htmlentities($_REQUEST['email']);
					$qq = htmlentities($_REQUEST['qq']);

					//发送邮件
					$body = '
			            <h3>姓名：'.$fullname.'</h3>
			            <h3>邮箱：'.$email.'</h3>
			            <h3>Q Q：'.$qq.'</h3>
			            <h3>公司：'.$company.'</h3>
			            <h3>产品：'.$product.'</h3>
			        ';
					$this->sendmail($email, $fullname, $company, $body);

					// $this->success("留言成功！");
					$this->majaxReturn(0, '留言成功！');
				} else {
					// $this->error("留言失败！");
					$this->majaxReturn(1, '留言失败！');
				}
			} else {
				// $this->error($this->sdkaccess_model->getError());
				$this->majaxReturn(1, $this->sdkaccess_model->getError());
			}
		}
	}
}
