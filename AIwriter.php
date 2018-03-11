<?php
$servername = "localhost";
$username = "root";
$password = "123456";
// 创建连接
$conn = mysql_connect($servername, $username, $password);
// 检测连接
if (!$conn) {
    die("连接失败: " . mysql_error());
}
mysql_select_db("congrate", $conn);
mysql_query("set names 'utf8'");
//$obj = new AIwriter();
//print $obj->execute('test');
//print "\n";

class AIwriter{
    public function execute($Username){
        //获取通用祝福语
        $sql1 = "select * from bless where bless_type=4";
        $sql2 = "select * from bless where bless_type=5";
        $sql3 = "select * from bless where bless_type=6";
        $result1 = $this->mysql_query_result($sql1);
        $result2 = $this->mysql_query_result($sql2);
        $result3 = $this->mysql_query_result($sql3);

        $tmp[] = $result1[rand(0, count($result1)-1)][2];
        $tmp[] = $result2[rand(0, count($result2)-1)][2];
        $tmp[] = $result3[rand(0, count($result3)-1)][2];
        
        $num = time()%4;
        if($num >= 2){
            $str = implode('。', $tmp);
        }else{
            $str = $tmp[$num].'。'.$tmp[$num+1];
        }

        //获取用户身份
        $sql4 = "select * from person where person_name = '$Username'";
        $personInfo = $this->mysql_query_result($sql4);
        if(empty($personInfo)){
            $personType = 0;
        }else{
            $personType = $personInfo[0][2];
        }

        if($personType != '0'){
            //获取身份对应的祝福语
            $sql5 = "select * from bless where bless_type=$personType";
            $result5 = $this->mysql_query_result($sql5);
            $str .= '。'.$result5[rand(0, count($result5)-1)][2];
        }

        $nickname = empty($personInfo[0][5])?$personInfo[0][1]:$personInfo[0][5];
        $coloregg = (time()%10 == 5)?$this->trans('\ue112').$personInfo[0][3].$this->trans('\ue112'):'';

        //使用软银版的emoji表情
        $str = $nickname.' '.$this->trans('\ue312').'新春来临之际，'.$this->trans('\ue30d').'狗年大吉！旺！旺！旺！'.$this->trans('\ue052').$this->trans('\ue312').
                $str.'。过年好！拜年了！'.$this->trans('\ue057').$this->trans('\ue30c').$coloregg;
        return $str;
    }

    public function mysql_query_result($sql){
        $result = mysql_query($sql);
        $ret = array();
        while(@$res = mysql_fetch_row($result)){
            $ret[] = $res;
        }
        return $ret;
    }

    public function trans($str){
        $str = '{"result_str":"' . $str . '"}'; // 组合成json格式
        $strarray = json_decode ( $str, true ); // json转换为数组，利用 JSON 对 \uXXXX 的支持来把转义符恢复为 Unicode 字符
        return $strarray ['result_str'];
    }
}
