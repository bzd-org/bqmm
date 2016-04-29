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
		
		$this->assign($content);
		$smeta=json_decode($content['smeta'],true);
		$tplname=isset($smeta['template'])?$smeta['template']:"";
		
		$tplname=sp_get_apphome_tpl($tplname, "page");
		
        //SEO信息
        $site_options=get_site_options();
        $seo = array(
            'title'       => $site_options['site_seo_title'],
            'keywords'    => $site_options['site_seo_keywords'],
            'description' => $site_options['site_seo_description'],
        );
        $this->assign('seo', $seo);

        //表情包
        if ($id == 2) $this->biaoqingbao();
        
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

    //表情包
    public function biaoqingbao()
    {
        $termid = 2;

        //表情包分类信息
        $term = sp_get_term($termid);
        $this->assign('term', $term);

        //子分类信息
        $subterms = sp_get_child_terms($termid);
        foreach ($subterms as $k=>$subterm) {
            $tag = 'order:istop desc, post_date desc;';
            $posts = sp_sql_posts_paged_bycatid($subterm['term_id'],$tag,6);

            $flag = 1;
            $nnn = 0;
            foreach ($posts['posts'] as $pk=>$post) {
                $posts['posts'][$pk]['smeta'] = json_decode($post['smeta'], true);

                if ($pk==0 && $post['istop']) {
                    $posts['posts'][$pk]['isbanner'] = 1;
                } else if ($pk==1 && $posts['posts'][0]['isbanner']==1) {
                    $posts['posts'][$pk]['isfirst'] = 1;
                } else {
                    if ($flag==1 && $nnn<2) {
                        $posts['posts'][$pk]['isleft'] = 1;
                    } else if ($flag==2 && $nnn<2) {
                        $posts['posts'][$pk]['isright'] = 1;
                    } else {
                        if ($flag == 1) {
                            $posts['posts'][$pk]['isright'] = 1;
                            $flag = 2;
                        } else if ($flag == 2) {
                            $posts['posts'][$pk]['isleft'] = 1;
                            $flag = 1;
                        }
                        $nnn = 0;
                    }
                    $nnn++;
                }
            }

            $subterms[$k]['posts'] = is_array($posts) ? $posts : array();
        }
        $this->assign('subterms', $subterms);
        // dump($subterms);exit;
    }
}