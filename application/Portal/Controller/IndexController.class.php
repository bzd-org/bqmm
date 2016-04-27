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
        //SEO信息
        $site_options=get_site_options();
        $seo = array(
            'title'       => $site_options['site_seo_title'],
            'keywords'    => $site_options['site_seo_keywords'],
            'description' => $site_options['site_seo_description'],
        );
        $this->assign('seo', $seo);

        //index_banner
        $index_banner = sp_getslide('index_banner');
        $this->assign('index_banner', $index_banner);

        $this->slidehzal();

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
}