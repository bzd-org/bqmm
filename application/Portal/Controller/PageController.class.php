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
class PageController extends HomebaseController{
    public function index() {
        $id=$_GET['id'];
        $content=sp_sql_page($id);
        
        if(empty($content)){
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            if(sp_template_file_exists(MODULE_NAME."/404")){
                $this->display(":404");
            }
             
            return ;
        }

        $this->seo['title'] = $content['post_title'];
        $this->seo['keywords'] = $content['post_keywords'].' '.$this->seo['keywords'];
        $this->seo['description'] = $content['post_excerpt'].' '.$this->seo['description'];
        $this->assign('seo', $this->seo);
        
        $this->assign($content);
        $smeta=json_decode($content['smeta'],true);
        $tplname=isset($smeta['template'])?$smeta['template']:"";
        
        $tplname=sp_get_apphome_tpl($tplname, "page");

        //FAQ
        $faq = sp_getslide('faq', 99);
        $this->assign('faq', $faq);

        //about_banner
        $about_banner = sp_getslide('about_banner');
        $this->assign('about_banner', $about_banner);

        //about_bqy
        $about_bqy = sp_getslide('about_bqy');
        $this->assign('about_bqy', $about_bqy[0]);

        //about_syh
        $about_syh = sp_getslide('about_syh');
        $this->assign('about_syh', $about_syh[0]);

        //about_product
        $about_product = sp_getslide('about_product');
        foreach ($about_product as $k=>$product) {
            if ($k == 0) {
                $about_product[$k]['li_class'] = '';
                $about_product[$k]['box_class'] = 'ptl';
            }
            if ($k == 1) {
                $about_product[$k]['li_class'] = '';
                $about_product[$k]['box_class'] = 'ptm';
            }
            if ($k == 2) {
                $about_product[$k]['li_class'] = 'last';
                $about_product[$k]['box_class'] = 'ptr';
            }
        }
        $this->assign('about_product', $about_product);

        //about_honours
        $about_honours = sp_getslide('about_honours', 99);
        foreach ($about_honours as $k=>$d) {
            $slide_des = explode('|', $d['slide_des']);
            $about_honours[$k]['time1'] = $slide_des[0];
            $about_honours[$k]['time2'] = $slide_des[1];
            $about_honours[$k]['ico'] = $slide_des[2];
        }
        rsort($about_honours);
        $this->assign('about_honours', $about_honours);

        //about_culture
        $about_culture = sp_getslide('about_culture', 99);
        $about_culture_cache = $about_culture;
        $about_culture = array();
        $i = 0;
        $f = 0;
        foreach ($about_culture_cache as $k=>$d) {
            if ($f==1 || $f==3) $about_culture[$f]['class'] = 'cd';
            if ($i < 2) {
                $about_culture[$f]['culture'][] = $d;
                $i++;
            }
            if ($i >= 2) {
                $i = 0;
                $f++;
            }
        }
        $this->assign('about_culture', $about_culture);

        //about_contact
        $about_contact = sp_getslide('about_contact');
        $this->assign('about_contact', $about_contact);

        //hlinks
        $hlinks = sp_getlinks();
        $this->assign('hlinks', $hlinks);

        // dump($hlinks);exit;
        $this->display(":$tplname");
    }
    
    public function nav_index(){
        $navcatname="页面";
        $datas=sp_sql_pages("field:id,post_title;");
        $navrule=array(
                "action"=>"Page/index",
                "param"=>array(
                        "id"=>"id"
                ),
                "label"=>"post_title");
        exit( sp_get_nav4admin($navcatname,$datas,$navrule) );
    }
}