<?php
include 'AIwriter.php';
index();
//微信api接入验证   
function index()  
{  
    //获得几个参数  
    $token     = 'shaopengwei';//此处填写之前开发者配置的token  
    $nonce     = $_GET['nonce'];  
    $timestamp = $_GET['timestamp'];  
    $echostr   = $_GET['echostr'];  
    $signature = $_GET['signature'];  
    //参数字典序排序  
    $array = array();  
    $array = array($nonce, $timestamp, $token);  
    sort($array);  
    //验证  
    $str = sha1( implode( $array ) );//sha1加密  
    //对比验证处理好的str与signature,若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，成为开发者成功，否则接入失败。  
    if($str == $signature && $echostr){  
        //第一次接入微信api有echostr这个参数，之后就没有了  
        echo $echostr;
    }else{
        //接入成功后的其他处理，生成xml格式内容返回回复信息
        $inputInfo = $GLOBALS["HTTP_RAW_POST_DATA"];
        $objInfo = simplexml_load_string($inputInfo, 'SimpleXMLElement', LIBXML_NOCDATA);
        //首次关注欢迎语
        if($objInfo->MsgType == "event" && $objInfo->Event == 'subscribe'){
            $word = '感谢您的关注，我是小软^—^！告诉我你的名字，查收主人对你的新年祝福哟~~（祝福无限，还有彩蛋哟，你敢不敢多试试？）';
        }else{
            $arrInput = json_decode(json_encode($objInfo->Content),true);
            //根据输入信息进行机器写作逻辑
            $objAiWriter = new AIwriter();
            $word = $objAiWriter->execute($arrInput[0]);
        }
        $tpl = "<xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>
          <MsgType><![CDATA[%s]]></MsgType>
          <Content><![CDATA[%s]]></Content>
          <FuncFlag>0</FuncFlag>
          </xml>";
        $ret = sprintf($tpl, $objInfo->FromUserName, $objInfo->ToUserName, time(), 'text', $word);

        echo $ret;
    }
} 
