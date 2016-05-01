<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 首页
 */
class IndexController extends HomebaseController {
    //首页
	public function index() {
        //index_banner
        $index_banner = sp_getslide('index_banner', 99);
        $this->assign('index_banner', $index_banner);

        //合作案例
        $this->slidehzal();

        //合作伙伴
        $this->slidehzhb();

    	$this->display(":index");
    }

    //获取合作案例
    public function slidehzal()
    {
        //获取幻灯片-合作案例分类
        $slidehzalprefix = 'hzal_banner';
        $slidehzallist = M('slide_cat')->where(array('cat_idname'=>array('like','%'.$slidehzalprefix.'%')))->select();
        //获取幻灯片内容
        if (is_array($slidehzallist)) {
            foreach ($slidehzallist as $key=>$slide) {
                $slidebanner = sp_getslide($slide['cat_idname']);
                $slidehzallist[$key]['slidebanner'] = is_array($slidebanner) ? $slidebanner : array();
            }
        }

        // dump($slidehzallist);exit;
        $this->assign('slidehzallist', $slidehzallist);
    }

    //获取合作伙伴
    public function slidehzhb()
    {
        $slidehzhblist = sp_getslide('hzhb_banner');
        $slidehzhblist[0]['class'] = 'slt';
        
        $this->assign('slidehzhblist', $slidehzhblist);
    }
}