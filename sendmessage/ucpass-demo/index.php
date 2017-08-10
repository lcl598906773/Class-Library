<?php
//载入ucpass类
require_once('lib/Ucpaas.class.php');

//初始化必填
$options['accountsid']='655f72ceac46059ef455204e00ae922a';
$options['token']='fb0acd0ac902bdbc7321e28efb1e0111';


//初始化 $options必填
$ucpass = new Ucpaas($options);

//开发者账号信息查询默认为json或xml

// echo $ucpass->getDevinfo('xml');

//申请client账号
//$appId = "xxxx";
//$clientType = "0";
//$charge = "0";
//$friendlyName = '';
//$mobile = "18612345678";

//echo $ucpass->applyClient($appId, $clientType, $charge, $friendlyName, $mobile);

//删除client账号
//$appId = "xxxx";
//$clientNumber='xxxxx';

//echo $ucpass->releaseClient($clientNumber,$appId);

//删除client账号
//$appId = "xxxx";
//$start = "0";
//$limit = "100";
//
//echo $ucpass->getClientList($appId,$start,$limit);

//以Client账号方式查询Client信息
//$appId = "xxxx";
//$clientNumber='xxxx';
//
//echo $ucpass->getClientInfo($appId,$clientNumber);

//以手机号码方式查询Client信息
//$appId = "xxxx";
//$mobile = "18612345678";

//echo $ucpass->getClientInfoByMobile($appId,$mobile);

//应用话单下载,通过HTTPS POST方式提交请求，云之讯融合通讯开放平台收到请求后，返回应用话单下载地址及文件下载检验码。
//day 代表前一天的数据（从00:00 – 23:59）；week代表前一周的数据(周一 到周日)；month表示上一个月的数据（上个月表示当前月减1，如果今天是4月10号，则查询结果是3月份的数据）
//$appId = "xxxx";
//$date = "day";

//echo $ucpass->getBillList($appId,$date);

//Client充值,通过HTTPS POST方式提交充值请求，云之讯融合通讯开放平台收到请求后，返回Client充值结果。
//$appId = "xxxx";
//$clientNumber='xxxx';
//$clientType = "0";
//$charge = "0";

//echo $ucpass->chargeClient($appId,$clientNumber,$clientType,$charge);

//双向回拨,云之讯融合通讯开放平台收到请求后，将向两个电话终端发起呼叫，双方接通电话后进行通话。
//$appId = "xxxx";
//$fromClient = "xxxx";
//$to = "18612345678";
//$fromSerNum = '';
//$toSerNum = '';

//echo $ucpass->callBack($appId,$fromClient,$to);

//语音验证码,云之讯融合通讯开放平台收到请求后，向对象电话终端发起呼叫，接通电话后将播放指定语音验证码序列
//$appId = "xxxx";
//$verifyCode = "1256";
//$to = "18612345678";

//echo $ucpass->voiceCode($appId,$verifyCode,$to);

//短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
$nums = '123456789';
$yzm = substr(str_shuffle($nums),0,4);

$appId = "ea07ccd86b2949718c4b88a509cca98e";
$to = "18510632487";
$templateId = "40807";
$param='lclbbs,' . $yzm . ',3';

$res = $ucpass->templateSMS($appId,$to,$templateId,$param);
$result = substr($res, 21, 6);
 var_dump($result);