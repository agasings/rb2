<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$readme_file = $g['path_widget'].$widget.'/readme.txt';
$readme_skin = @fopen($readme_file, 'r');
$readme = @fread($readme_skin, filesize($readme_file));
$readme = nl2br($readme);

if ($g['mobile']&&$_SESSION['pcmode']!='Y') $is_mobile = 1;
else $is_mobile = 0;

//카테고리출력
function getCategoryShowSelect($table,$j,$parent,$depth,$uid,$hidden,$cat) {
	static $j;
	$html = '';
	$CD=getDbSelect($table,'depth='.($depth+1).' and parent='.$parent.($hidden ? ' and hidden=0':'').' order by gid asc','*');
	while($C=db_fetch_array($CD)){
		$j++;
		$html .= '<option class="selectcat'.$C['depth'].'" value="'.$C['uid'].'"'.($C['uid']==$cat?' selected="selected"':'').'>';
		if(!$depth) $html .= 'ㆍ';
		for($i=1;$i<$C['depth'];$i++) $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if ($C['depth'] > 1) $html .= 'ㄴ';
		$html .= $C['name'].'</option>';
		if ($C['is_child']) $html .=  getCategoryShowSelect($table,$j,$C['uid'],$C['depth'],$uid,$hidden,$cat);
	}
	return $html;
}

$widget = $_POST['widget'];
$wdgvar = $_POST['wdgvar'];
$area = $_POST['area'];

$wdgvar = str_replace('[','', $wdgvar);
$wdgvar = str_replace(']','', $wdgvar);

$wdgvar_arr = explode('^',$wdgvar);
$wdgkey_arr = explode(',',$wdgvar_arr[3]);
$_wdgvar = array();
foreach ($wdgkey_arr as $key) {
  $key_arr = explode('=',$key);
  $_wdgvar[$key_arr[0]]=$key_arr[1];
}

if (file_exists($g['path_widget'].$widget.'/_var.config.php')) {
	include_once $g['path_widget'].$widget.'/_var.config.php';

	$d['layout']['dom'] = array();

	$html='';
	$_i=1;
	foreach ($d['widget']['dom'] as $_key => $_val) {
	  $__i=sprintf('%02d',$_i);

		if ($is_mobile) {
			$html .=  '<div class="">

		              <div class="">';
		} else {
			$html .=  '<div class="card border-primary shadow-sm mb-2">
		              <div class="card-header bg-primary text-white rounded-0 d-flex justify-content-between align-items-center">
		                <span>'.$_val[0].'</span>
		                <small class=""></span>
		              </div>
		              <div class="card-body f13">';
		}

	  if (count($_val[1])) {

	    foreach ($_val[1] as $_v) {
	      $html .= '<div class="form-group">';

	      if ($_v[1]!='hidden') {
	        $html .= '<label class="text-muted f12 mb-1">'.$_v[2].'</label>';
	      }

	      if ($_v[1]=='input') {
	        $html .= '<input type="text" class="form-control" name="'.$_v[0].'" value="'.stripslashes($_wdgvar[$_v[0]]).'" placeholder="'.$_v[3].'">';
	      }

	      if ($_v[1]=='select') {
	        $html .= '<select name="'.$_v[0].'" class="form-control custom-select">';
	        $_sk=explode(',',$_v[3]);
	        foreach ($_sk as $_sa) {
	          $_sa1=explode('=',$_sa);
						if ($_wdgvar[$_v[0]]) {
							$html .= '<option value="'.$_sa1[1].'" '.($_sa1[1]==$_wdgvar[$_v[0]]?' selected':'').' >'.$_sa1[0].'</option>';
						} else {
							$html .= '<option value="'.$_sa1[1].'" '.($_sa1[1]==$_v[4]?' selected':'').' >'.$_sa1[0].'</option>';
						}
	        }
	        $html .= '</select>';
	      }

	      if ($_v[1]=='radio') {
	        $_sk=explode(',',$_v[3]);
	        foreach ($_sk as $_sa) {
	          $_sa1=explode('=',$_sa);
	          $html .= '<label class="rb-rabel"><input type="radio" name="layout_'.$_key.'_'.$_v[0].'" value="'.$_sa1[1].'"> '.$_sa1[0].'</label>';
	        }
	      }

	      if ($_v[1]=='checkbox') {
	        $_sk=explode(',',$_v[3]);
	        foreach ($_sk as $_sa) {
	          $_sa1=explode('=',$_sa);
	          $html .= '<label class="rb-rabel"><input type="checkbox" name="layout_'.$_key.'_'.$_v[0].'_chk[]" value="'.$_sa1[1].'"> '.$_sa1[0].'</label>';
	        }
	      }

	      if ($_v[1]=='textarea') {
	        $html .= '<textarea type="text" rows="'.$_v[3].'" class="form-control" name="layout_'.$_key.'_'.$_v[0].'">'.stripslashes($d['widget'][$_key.'_'.$_v[0]]).'</textarea>';
	      }

	      if ($_v[1]=='postlist') {
					$html .= '<div class="input-group">';
	        $html .= '<select name="'.$_v[0].'" class="form-control custom-select" size="1">
	                    <option value="">선택하세요.</option>
	                    <option value="" disabled>----------------------------------</option>';
	        $_sk=explode(',',$_v[3]);
	        $POSTLIST = getDbArray($table['postlist_member'],'mbruid="'.$my['uid'].'" and display<>1','*','gid','asc',0,1);
	        while ($R=db_fetch_array($POSTLIST)) {
	          $L = getUidData($table['postlist'],$R['list']);
	          $html .= '<option value="'.$L['id'].'" '.($L['id']==$_wdgvar[$_v[0]]?' selected':'').'>
	                      ㆍ'.$L['name'].'
	                    </option>';
	                  }
	        $html .= '</select>';
					$html .= '<div class="input-group-append input-group-btn"><button class="btn btn-white btn-secondary" type="button" data-toggle="tooltip" title="리스트 추가" data-act="make" data-mod="postlist">+</button></div>';
					$html .= '</div>';
	      }

	      if ($_v[1]=='postcat') {
	        $html .= '<select name="'.$_v[0].'" class="form-control custom-select">
	                    <option value="">선택하세요.</option>
	                    <option value="" disabled>----------------------------------</option>';
					$html .=	 getCategoryShowSelect($table['postcategory'],0,0,0,0,0,$_wdgvar[$_v[0]]);
	        $html .= '</select>';
	      }

	      if ($_v[1]=='bbs') {
					$html .= '<div class="input-group">';
	        $html .= '<select name="'.$_v[0].'" class="form-control custom-select" size="1">
	                    <option value="">선택하세요</option>
	                    <option value="" disabled>----------------------------------</option>';
	        $BBSLIST = getDbArray($table['bbslist'],'','*','gid','asc',0,1);
	        while ($R=db_fetch_array($BBSLIST)) {
	          $html .= '<option value="'.$R['id'].'" '.($R['id']==$_wdgvar[$_v[0]]?' selected':'').' data-name="'.$R['name'].'" data-link="'.RW('m=bbs&bid='.$R['id']).'">
	                      ㆍ'.$R['name'].'('.$R['id'].')
	                    </option>';
	                  }
	        $html .= '</select>';
				  $html .= '<div class="input-group-append input-group-btn"><button class="btn btn-white btn-secondary" type="button" data-toggle="tooltip" title="게시판 추가"  data-act="make" data-mod="bbs">+</button></div>';
					$html .= '</div>';
	      }

	      if ($_v[1]=='date') {

	        $html .= '<div class="input-group input-daterange">
	                    <input type="text" class="form-control" name="" id="" value="">
	                    <span class="input-group-append">
	                      <button class="btn btn-light" type="button"><i class="fa fa-calendar"></i></button>
	                    </span>
	                  </div>';
	      }

	      if ($_v[1]=='color') {
	        $html .= '<div class="input-group">
	                    <input type="text" class="form-control" name="layout_'.$_key.'_'.$_v[0].'" id="layout_'.$_key.'_'.$_v[0].'" value="">
	                    <span class="input-group-append">
	                      <button class="btn btn-light" type="button" data-toggle="modal"
	                      data-target=".bs-example-modal-sm"
	                      onclick="getColorLayer(getId("layout_'.$_key.'_'. $_v[0].').value.replace("#",""),"layout_'.$_key.'_'.$_v[0].');"><i class="fa fa-tint"></i></button>
	                    </span>
	                  </div>';
	      }

	      if ($_v[1]=='mediaset') {
	        $html .= '<div class="input-group">
	                    <input type="text" class="form-control rb-modal-photo-drop js-tooltip"  value="" onmousedown="" title="선택된 사진" data-toggle="modal" data-target="#modal_window">
	                    <span class="input-group-append">
	                      <button class="btn btn-light rb-modal-photo-drop js-tooltip" type="button" title="포토셋" data-toggle="modal" data-target="#modal_window"><i class="fa fa-picture-o"></i></button>
	                    </span>
	                  </div>';
	      }

	      if ($_v[1]=='videoset') {
	          $html .= '<div class="input-group">
	                      <input type="text" class="form-control rb-modal-video-drop js-tooltip" name="" id="" value="" onmousedown="" title="선택된 비디오" data-toggle="modal" data-target="#modal_window">
	                      <span class="input-group-append">
	                        <button onmousedown="" class="btn btn-light rb-modal-video-drop js-tooltip" type="button" title="비디오셋" data-toggle="modal" data-target="#modal_window"><i class="fa fa-video-camera"></i></button>
	                      </span>
	                    </div>';
	      }


	      $html .= '</div>';
	    }
	  }

		  $html .= '<div class="text-right mt-4"><a class="text-muted f12" data-toggle="collapse" href="#widgetMore"><u>위젯정보</u></a></div>';

			$html .= '<div class="collapse" id="widgetMore">

									<img src="/widgets/'.$widget.'/thumb.png" class="img-fluid my-3">
									<p class="text-muted f13">'.$readme.'</p>

									<dl class="row f12 text-muted mt-5 mb-0">
										<dt class="col-3">위젯명</dt>
										<dd class="col-9 pl-0">'.$_val[0].'</dd>
										<dt class="col-3">경로</dt>
										<dd class="col-9 pl-0">/widgets/'.$widget.'</dd>
									</dl>

									<div class="form-group mb-0"><label class="text-muted f12 mb-1 font-weight-bold">삽입코드</label><div class="input-group input-group-sm">
								  <input type="text" class="form-control rounded-0 text-muted" id="widgetCode" readonly>
								  <div class="input-group-append">
								    <button class="btn btn-white js-clipboard text-muted rounded-0" data-act="code" type="button" data-toggle="tooltip" title="" data-clipboard-target="#widgetCode">추출</button>
								  </div>
								</div></div></div>';


	  $html .=  '</div>';

		if (!$is_mobile) {
			$html .=  '<footer class="card-footer d-flex justify-content-between bg-white">
									<button type="button" data-act="cancel" class="btn btn-link text-muted">취소</button>
		              <button type="button" data-act="save" data-mod="'.($wdgvar?'edit':'add').'" data-area="'.$area.'" class="btn btn-outline-primary">
		                <span class="not-loading">'.($wdgvar?'적용':'추가').'</span>
		                <span class="is-loading"><div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">처리중...</span></div></span>
		              </button>
		            </footer>';
		}

	  $html .=  '</div>';
	}
}

$result=array();
$result['error'] = false;

$result['widget'] = $widget;
$result['page'] = $html;

echo json_encode($result);
exit;
?>
