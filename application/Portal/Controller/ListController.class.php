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
 * 文章列表
*/
class ListController extends HomebaseController {

	//文章内页
	public function index() {
		$term=sp_get_term($_GET['id']);
		
		if(empty($term)){
		    header('HTTP/1.1 404 Not Found');
		    header('Status:404 Not Found');
		    if(sp_template_file_exists(MODULE_NAME."/404")){
		        $this->display(":404");
		    }
		    	
		    return ;
		}

        $this->seo['title'] = $term['seo_title'];
        $this->seo['keywords'] = $term['seo_keywords'].' '.$this->seo['keywords'];
        $this->seo['description'] = $term['seo_description'].' '.$this->seo['description'];
        $this->assign('seo', $this->seo);

        $type = intval($_GET['type']);
		$cat_id = intval($_GET['id']);
        if (!$cat_id && $type=="xw") $cat_id = 1;
        if (!$cat_id && $type=="bq") $cat_id = 2;
		
		$tplname=$term["list_tpl"];
    	$tplname=sp_get_apphome_tpl($tplname, "list");

    	$this->assign($term);
    	$this->assign('cat_id', $cat_id);

        //新闻动态
        $this->xinwendongtai();
        //表情包
        $this->biaoqingbao($cat_id);

        $p = intval($_GET['p']);
        $p = $p ? $p : 1;
        $this->assign('p', $p);

    	$this->display(":$tplname");
	}
	
	public function nav_index(){
		$navcatname="文章分类";
		$datas=sp_get_terms("field:term_id,name");
		$navrule=array(
				"action"=>"List/index",
				"param"=>array(
						"id"=>"term_id"
				),
				"label"=>"name");
		exit(sp_get_nav4admin($navcatname,$datas,$navrule));
	}

    //新闻动态
    public function xinwendongtai()
    {
        $termid = 1;
        $pagesize = 7;

        //表情包分类信息
        $term = sp_get_term($termid);
        $this->assign('term', $term);

        $tag = 'order:istop desc, post_date desc;';
        $pagelink = array('index'=>'news.html', 'list'=>'news.html&p={page}');
        $posts = sp_sql_posts_paged_bycatid($termid, $tag, $pagesize, '{liststart}{list}{listend}', $pagelink);

        $this->assign('mpage', $posts['page']);
        $this->assign('pcount', ceil($posts['count']/$pagesize));

        $posts = $posts['posts'];
        foreach ($posts as $pk=>$post) {
            $posts[$pk]['smeta'] = json_decode($post['smeta'], true);
        }

        // dump($posts);exit;
        $this->assign("posts", $posts);
    }

    //表情包
    public function biaoqingbao($termid=2)
    {
        $termid = $termid ? $termid : 2;

        //表情包分类信息
        $term = sp_get_term($termid);
        $this->assign('term', $term);

        //子分类信息
        $subterms = sp_get_child_terms($termid);
        $subterms = array_merge(array($term), $subterms);

        $aaa = 0;
        foreach ($subterms as $k=>$subterm) {
            $tag = 'cid:'.$subterm['term_id'].'order:istop desc, post_date desc;limit:0,6;';
            $posts = sp_sql_posts($tag);

            $flag = 1;
            $nnn = 0;
            foreach ($posts as $pk=>$post) {
                $posts[$pk]['smeta'] = json_decode($post['smeta'], true);

                if ($pk==0 && $post['istop']) {
                    $posts[$pk]['isbanner'] = 1;
                } else if ($pk==1 && $posts[0]['isbanner']==1) {
                    $posts[$pk]['isfirst'] = 1;
                } else {
                    if ($flag==1 && $nnn<2) {
                        $posts[$pk]['isleft'] = 1;
                    } else if ($flag==2 && $nnn<2) {
                        $posts[$pk]['isright'] = 1;
                    } else {
                        if ($flag == 1) {
                            $posts[$pk]['isright'] = 1;
                            $flag = 2;
                        } else if ($flag == 2) {
                            $posts[$pk]['isleft'] = 1;
                            $flag = 1;
                        }
                        $nnn = 0;
                    }
                    $nnn++;
                }

                if ($pk%2 === 0) {
                    $posts[$pk]['misleft'] = 1;
                } else {
                    $posts[$pk]['misright'] = 1;
                }
            }

            if ($aaa==0 && !empty($posts)) {
                $subterms[$k]['sk'] = 1;
                $aaa = 1;
            }

            $subterms[$k]['posts'] = is_array($posts) ? $posts : array();
        }
        $this->assign('subterms', $subterms);
        // dump($subterms);exit;
    }

    //ajax表情包
    public function ajaxbqb()
    {
    	$termid = $_REQUEST['termid'];
    	if (!$termid) $this->majaxReturn(1, '数据出错！');

    	$page = (int)$_REQUEST['page'];

    	$start = 6+($page-1)*4;
    	$end = $start+4;
    	$tag = 'order:istop desc, post_date desc;';
        $posts = sp_sql_posts_bycatid($termid,$tag);

        $termposts = array();
        $flag = 1;
        $nnn = 0;
        foreach ($posts as $pk=>$post) {
            $posts[$pk]['smeta'] = json_decode($post['smeta'], true);

            if ($pk==0 && $post['istop']) {
                $posts[$pk]['isbanner'] = 1;
            } else if ($pk==1 && $posts[0]['isbanner']==1) {
                $posts[$pk]['isfirst'] = 1;
            } else {
                if ($flag==1 && $nnn<2) {
                    $posts[$pk]['isleft'] = 1;
                } else if ($flag==2 && $nnn<2) {
                    $posts[$pk]['isright'] = 1;
                } else {
                    if ($flag == 1) {
                        $posts[$pk]['isright'] = 1;
                        $flag = 2;
                    } else if ($flag == 2) {
                        $posts[$pk]['isleft'] = 1;
                        $flag = 1;
                    }
                    $nnn = 0;
                }
                $nnn++;
            }

            if ($pk%2 === 0) {
                $posts[$pk]['misleft'] = 1;
            } else {
                $posts[$pk]['misright'] = 1;
            }

            if ($pk>=$start && $pk<$end) $termposts[] = $posts[$pk];
        }

        $this->assign('termposts', $termposts);
        $html = $this->fetch("/Public/postsboxitem");

        $this->majaxReturn(0,'',array(
        	'loadmorehide' => $end>=count($posts) ? 1 : 0,
        	'html' => $html
        ));
    }
}
