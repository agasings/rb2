<?php
//게시물링크
function getPostLink($arr)
 {
    return RW('m=bbs&bid='.$arr['bbsid'].'&uid='.$arr['uid'].($GLOBALS['s']!=$arr['site']?'&s='.$arr['site']:''));
 }
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 5;

$bbsque = 'mbruid='.$_M['uid'];
if ($account) $bbsque .= ' and site='.$account;
if ($bid) $bbsque .= ' and bbs='.$bid;
if ($d_start) $bbsque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $bbsque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($where && $keyw)
{
	if (strstr('[name][nic][id][ip]',$where)) $bbsque .= " and ".$where."='".$keyw."'";
	else if ($where == 'term') $bbsque .= " and d_regis like '".$keyw."%'";
	else $bbsque .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['bbsdata'],$bbsque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['bbsdata'],$bbsque);
$TPG = getTotalPage($NUM,$recnum);
?>

<div class="manager-list pt-3 px-3">

	<form name="searchForm" action="<?php echo $g['s']?>/" method="get">
  	<input type="hidden" name="r" value="<?php echo $r?>">
  	<input type="hidden" name="m" value="<?php echo $m?>">
  	<input type="hidden" name="module" value="<?php echo $module?>">
  	<input type="hidden" name="front" value="<?php echo $front?>">
  	<input type="hidden" name="tab" value="<?php echo $tab?>">
  	<input type="hidden" name="uid" value="<?php echo $_M['uid']?>">
  	<input type="hidden" name="p" value="<?php echo $p?>">
  	<input type="hidden" name="iframe" value="<?php echo $iframe?>">

    <div class="d-flex justify-content-between align-items-center">
      <div>
    		<small><?php echo sprintf('총 %d건',$NUM)?></small>
        <span class="badge badge-pill badge-dark"><?php echo $p?>/<?php echo $TPG?> 페이지</span>
        <button type="button" class="btn btn-link btn-sm muted-link" data-toggle="collapse" data-target="#search-more-bbs" onclick="sessionSetting('sh_bbslist','1','','1');">
          고급검색
        </button>
    	</div>

      <div class="form-inline">

        <select name="siteuid" class="form-control form-control-sm custom-select" onchange="this.form.submit();">
          <option value="">전체 사이트</option>
          <?php $SITES = getDbArray($table['s_site'],'','*','gid','asc',0,$p)?>
          <?php while($S = db_fetch_array($SITES)):?>
          <option value="<?php echo $S['uid']?>"<?php if($S['uid']==$siteuid):?> selected<?php endif?>><?php echo $S['name']?> (<?php echo $S['id']?>)</option>
          <?php endwhile?>
        </select>

        <select name="bid" class="form-control form-control-sm custom-select ml-1" onchange="this.form.submit();">
          <option value="">전체 게시판</option>
          <?php $_BBSLIST = getDbArray($table['bbslist'],'','*','gid','asc',0,1)?>
          <?php while($_B=db_fetch_array($_BBSLIST)):?>
          <option value="<?php echo $_B['uid']?>"<?php if($_B['uid']==$bid):?> selected="selected"<?php endif?>>ㆍ<?php echo $_B['name']?>(<?php echo $_B['id']?>)</option>
          <?php endwhile?>
          <?php if(!db_num_rows($_BBSLIST)):?>
          <option value="">등록된 게시판이 없습니다.</option>
          <?php endif?>
        </select>

      </div><!-- /.form-inline -->
    </div><!-- /.d-flex -->

    <div id="search-more-bbs" class="mt-3 collapse<?php if($_SESSION['sh_bbslist']):?> show<?php endif?>">
      <div class="d-flex justify-content-between align-items-center">
        <div class="form-inline input-daterange" id="datepicker">
          <input type="text" class="form-control form-control-sm" name="d_start" placeholder="시작일" value="<?php echo $d_start?>">
          <span class="px-1">~</span>
          <input type="text" class="form-control form-control-sm" name="d_finish" placeholder="종료일" value="<?php echo $d_finish?>">
          <button class="btn btn-light btn-sm ml-1" type="submit"><i class="fa fa-search"></i></button>
        </div><!-- /.form-inline -->

        <div class="form-inline">
          <select name="where" class="form-control form-control-sm custom-select">
            <option value="subject|tag"<?php if($where=='subject|tag'):?> selected="selected"<?php endif?>>제목+태그</option>
            <option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>본문</option>
          </select>
          <input type="text" name="keyw" class="form-control form-control-sm" placeholder="검색어를 입력해주세요." value="<?php echo $keyw?>">
          <button class="btn btn-light ml-1" type="submit"><i class="fa fa-search"></i>검색</button>
          <button class="btn btn-light ml-1" type="button" onclick="this.form.keyw.value='';this.form.submit();">리셋</button>
        </div><!-- /.form-inline -->
      </div><!-- /.d-flex -->
    </div><!-- /.collapse -->
  </form>

</div>

<form name="adm_list_form" class="mt-3" action="<?php echo $g['s']?>/" method="post">
  <input type="hidden" name="r" value="<?php echo $r?>">
  <input type="hidden" name="module" value="<?php echo $module?>">
  <input type="hidden" name="front" value="<?php echo $front?>">
  <input type="hidden" name="tab" value="<?php echo $tab?>">
  <input type="hidden" name="p" value="<?php echo $p?>">
  <input type="hidden" name="iframe" value="<?php echo $iframe?>">
  <input type="hidden" name="m" value=""> <!-- 액션파일이 있는 모듈명  -->
  <input type="hidden" name="a" value=""> <!-- 액션명  -->

   <div class="table-responsive">
		<table class="table table-hover text-center f13">
      <colgroup>
        <col width="50">
        <col width="50">
        <col width="100">
        <col>
        <col width="130">
      </colgroup>
			<thead class="small text-muted">
				<tr>
          <th><input type="checkbox"  class="checkAll-act-list" data-toggle="tooltip" title="전체선택"></th>
          <th>번호</th>
          <th>게시판</th>
          <th>제목</th>
          <th>조회</th>
          <th>날짜</th>
				</tr>
			</thead>
			<tbody>
				<?php while($R=db_fetch_array($RCD)):?>
			    <?php $R['mobile']=isMobileConnect($R['agent'])?>
				<tr>
					<td><input type="checkbox" name="post_members[]"  onclick="checkboxCheck();" class="mbr-act-list" value="<?php echo $R['uid']?>"></td>
					<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
					<td><span class="badge badge-pill badge-dark"><?php echo $R['bbsid']?></span></td>
					<td class="text-left">
            <?php if($R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
            <?php if($R['category']):?><span class="badge badge-pill badge-dark"><?php echo $R['category']?></span><?php endif?>
            <a href="<?php echo getPostLink($R)?>" target="_blank"><?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'')?></a>
            <?php if(strstr($R['content'],'.jpg')):?><span class="badge badge-pill badge-dark"><i class="fa fa-image"></i></span><?php endif?>
            <?php if($R['upload']):?><span class="badge badge-pill badge-dark"><i class="fa fa-paperclip"></i></span><?php endif?>
            <?php if($R['hidden']):?><span class="badge badge-pill badge-dark"><i class="fa fa-lock"></i></span><?php endif?>
            <?php if($R['comment']):?><span class="badge badge-pill badge-dark"><?php echo $R['comment']?><?php echo $R['oneline']?'+'.$R['oneline']:''?></span><?php endif?>
            <?php if(getNew($R['d_regis'],24)):?><span class="text-danger"><small>N</small></span><?php endif?>
					</td>
					<td><small><?php echo $R['hit']?></small></td>
					<td class="rb-update">
						<time class="timeago small text-muted" data-toggle="tooltip" datetime="<?php echo getDateFormat($R['d_regis'],'c')?>" data-tooltip="tooltip" title="<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?>"></time>
					</td>
				</tr>
				<?php endwhile?>
			</tbody>
		</table>
    <?php if(!$NUM):?>
    <div class="text-muted text-center py-4">
      <i class="fa fa-exclamation-circle fa-2x mb-1 d-block" aria-hidden="true"></i>
      <small>데이타가 없습니다.</small>
    </div>
    <?php endif?>
   </div>

 <ul class="pagination pagination-sm justify-content-center py-3 w-100">
   <script type="text/javascript">getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
 </ul>

</form>
