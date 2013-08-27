<?php
    /*
        八月16日 18:47
        down_all 下载所有公司xls
        down_by_code 下载特定公司xls
        get_report_data_by_code 获取特定公司的财报数据
            "bdate"=>$bdate_array,"income"=>$income_array,"cost"=>$cost_array,
            "ddate"=>$ddate_array,"funds"=>$funds_array,"dtax"=>$dtax_array,"debt"=>$debt_array
        get_trade_data_by_code 获取特定公司每日交易数据
            "tradedate"=>$trade_date_array,"open"=>$open_array,"close"=>$close_array,
            "high"=>$high_array,"low"=>$low_array ,"adjclose"=>$adjclose_array,
            "change"=>$change_array
        get_inf_by_code 获取特定公司的信息
            "intro"=>$Inf","inf"=>inf_array,
            "date"=>$holder_date,"holder"=>$holder_total,"price"=>$holder_price,
            "fhoder"=>$fhoder_array,"tholder"=>$tholder_array
        get_capitalization_by_code() 获取股本变动
            "date"=>$date_array,"cap"=>$capital_array
    */
    $save_path="./tmp/THSData/";
    $filename1="benefitsimple.xls";
    $filename2="debtsimple.xls";
    $filename3="cashsimple.xls";
    $filename=array("1"=>$filename1,"2"=>$filename2,"3"=>$filename3);
    //下载所有公司的xls
    function download_all()
    {
        $code_file="com.txt";
        $code_array=file($code_file);
        foreach ($code_array as $code) {
            echo "$code";
            download_by_code(trim($code));
        }
    }

    //下载特定公司的xls
    function download_by_code($stock_code)
    {
        global $save_path,$filename,$filename1,$filename2,$filename3;
        $url = "http://basic.10jqka.com.cn/";
        //如果目录不存在则先创建目录
        if (!file_exists($save_path)){
            mkdir($save_path,0777);
        }
        //下载$filename数组中的三个文件并保存
        $xls=curl_init();
        for ($i=1; $i<=3 ; $i++) { 
            curl_setopt($xls, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
            curl_setopt($xls, CURLOPT_URL, $url.$stock_code."/xls/".$filename[$i]);
            $fp = fopen(($save_path.$stock_code."_".$filename[$i]), "w");
            curl_setopt($xls, CURLOPT_FILE , $fp);
            curl_setopt($xls, CURLOPT_TIMEOUT,60);
            for ($time=1; $time <= 10 ; $time++) { 
                $result=curl_exec($xls);
                if($result===true)
                {
                    break;
                }
                if ($time==10) {
                    echo "cUrl Error in $stockcode xls download: ".curl_error($ch)."\n";
                }
            }
            fclose($fp);
        }
        curl_close($xls);
    }

    //根据公司代码获取数据
    //返回$result_array "键值"对应数组
    //"bdate"利润表报告期 "income"营业总收入 "cost"营业总成本 "ddate"资产负债表报告期 "dtax" 递延所得税
    //"funds"货币资金   "debt"负债合计
    function get_report_data_by_code($stock_code)
    {
        global $save_path,$filename,$filename1,$filename2,$filename3;
        require_once 'Classes/PHPExcel.php';
        //创建对象
        $PHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel5();
        //加载利润表
        $PHPExcel = $PHPReader->load($save_path.$stock_code."_".$filename1); 
        $sheetCount = $PHPExcel->getSheetCount();
        $currentSheet = $PHPExcel->getSheet(1);
        //取得一共有多少列  getHighestColumn()返回的是列的字母编号。。。。
        $allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());
        //取得一共有多少行
        $allRow = $currentSheet->getHighestRow();

        for($currentRow = 1;$currentRow<=$allRow;$currentRow++)
        {    
            //对于第一行我们要对比内容 找出我们需要的列
            if($currentRow==1)
            {
                for($currentColumn=0 ;$currentColumn<=$allColumn;$currentColumn++)
                {
                    $value=$currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();
                    //用于保存所需列的索引
                    if($value=="时间\科目") $bdate_col=$currentColumn;
                    if($value=="营业总收入") $income_col=$currentColumn;
                    if($value=="营业总成本") $cost_col=$currentColumn;
                }
            }
            else
            {
                $bdate_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($bdate_col,$currentRow)->getValue();
                $income_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($income_col,$currentRow)->getValue();
                $cost_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($cost_col,$currentRow)->getValue();
            }
        }
        //加载资产负债表
        $PHPExcel = $PHPReader->load($save_path.$stock_code."_".$filename2); 
        $sheetCount = $PHPExcel->getSheetCount();
        $currentSheet = $PHPExcel->getSheet(1);
        $allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());
        $allRow = $currentSheet->getHighestRow();
        for($currentRow = 1;$currentRow<=$allRow;$currentRow++)
        {    
            if($currentRow==1)
            {
                for($currentColumn=0 ;$currentColumn<=$allColumn;$currentColumn++)
                {
                    $value=$currentSheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();
                    if($value=="时间\科目") $ddate_col=$currentColumn;
                    if($value=="货币资金") $funds_col=$currentColumn;
                    if($value=="递延所得税资产") $dtax_col=$currentColumn;
                    if($value=="负债合计") $debt_col=$currentColumn;
                }
            }
            else
            {
                $ddate_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($ddate_col,$currentRow)->getValue();
                $funds_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($funds_col,$currentRow)->getValue();
                $dtax_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($dtax_col,$currentRow)->getValue();
                $debt_array[$currentRow-2]=$currentSheet->getCellByColumnAndRow($debt_col,$currentRow)->getValue();
            }
        }
        $result_array=array("bdate"=>$bdate_array,"income"=>$income_array,"cost"=>$cost_array,"ddate"=>$ddate_array,"funds"=>$funds_array,"dtax"=>$dtax_array,"debt"=>$debt_array);
        return $result_array;
    }

    //获得雅虎上每日交易的数据
    //上证综指000001
    //深证综指399106
    function get_trade_data_by_code($stockcode)
    {
        //提前声明数组
        $result_array=array();
        $trade_date_array=array();
        $adjclose_array=array();
        $open_array=array();
        $close_array=array();
        $high_array=array();
        $low_array=array();
        //$change_array=array();
        if (preg_match("/^6|^000001$/", $stockcode)) 
        {
            //http://ichart.finance.yahoo.com
            //IP地址: 67.195.146.181ARIN
            //IP地址: 106.10.164.251新加坡
            $url_yahoo="http://67.195.146.181/table.csv?s=".$stockcode.".SS";//&a=1&b=1&c=1990&d=7&e=17&f=2013&g=d&ignore=.csv";
        }
        else
        {
            $url_yahoo="http://67.195.146.181/table.csv?s=".$stockcode.".SZ";//&a=1&b=1&c=1990&d=7&e=17&f=2013&g=d&ignore=.csv";
        }
        //初始化curl
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_yahoo);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        //获取
        for ($time=1; $time <= 30 ; $time++) {
            //echo "Time： $time...\n";
            $yahoo_all_string=curl_exec($ch);
            //如果没有出错就取数据
            if ( !($yahoo_all_string===false) ) {
                $yahoo_all_array=explode("\n", $yahoo_all_string);
                foreach ($yahoo_all_array as $key=>$yahoo_day_string) {
                    if($key==0) continue;//第一个不要
                    if($key==count($yahoo_all_array)-1) break;//最后一个是空的 不要
                    $yahoo_day_array=explode(",", $yahoo_day_string);
                    ////////////////////////////////
                    //即使获取到数据，也有可能是错误的，出错时进行下一次循环
                    if(!isset($yahoo_day_array[1]))
                    {
                        continue 2;
                    }
                    /////////////////////////////////
                    array_push($trade_date_array,$yahoo_day_array[0]);
                    array_push($open_array, $yahoo_day_array[1]);
                    array_push($high_array, $yahoo_day_array[2]);
                    array_push($low_array, $yahoo_day_array[3]);
                    array_push($close_array, $yahoo_day_array[4]);
                    array_push($adjclose_array,$yahoo_day_array[6]);
                }
                curl_close($ch);
                break;
            }
            //如果都没获取成功，给出错误提示
            if ($time==30) {
                echo "Error: get_trade_data_by_code(".$stockcode.") error\n";
                curl_close($ch);
            }
        }
        //,"change"=>$change_array   暂时先不要了。计算上问题太多，而且不够精确
        $result_array=array("tradedate"=>$trade_date_array,"open"=>$open_array,"close"=>$close_array,
            "high"=>$high_array,"low"=>$low_array ,"adjclose"=>$adjclose_array);
        return $result_array;
    }


    //根据公司代码获取公司信息
    function get_inf_by_code($stockcode)
    {
        //先声明
        $inf_array=array();//大量公司信息
        $Inf="";//公司简介
        $holder_date=array();//对应的日期
        $holder_total=array();//对应日期的股东人数
        $holder_price=array();//对应日期的股价
        $fhoder_array=array();//十大流动股东
        $tholder_array=array();//十大股东

        /*获取同花顺上的"公司资料"页面上的公司信息等数据 */
        //$url_ths= "http://stockpage.10jqka.com.cn/".$stockcode."/company/";
        $url_ths="http://basic.10jqka.com.cn/".$stockcode."/company.html";
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_ths);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        for ($time=1; $time <= 10 ; $time++) { 
            //echo "Time ： $time...\n";
            $string=curl_exec($ch);
            //如果没有获取出错，就进行匹配数据的操作，否则继续循环十次
            if(!($string===false)){
                $string=iconv("gbk", "utf-8", $string);
                //公司简介 $Inf string
                $mode = "/<p class=\"none tip lh24\">[\w\W]*?</";
                if ( !preg_match($mode, $string ,$AllArray) ) //是否能匹配到”显示更多“
                {
                    $mode2="/<p class=\"tip lh24\">[\w\W]*?</";
                    if(!preg_match($mode2, $string ,$AllArray))
                    {
                        echo "get_inf Not match,$stockcode\n";
                    }
                }
                $Inf=strip_tags($AllArray[0]);

                //可匹配其他有用信息30条
                $mode_all_inf = "/(<strong class=\"hltip fl\">.+?<\/strong>\s+-\s+<\/div>)|(<strong class=\"hltip fl\">[\w\W]*?<\/span>)/";
                if (!preg_match_all($mode_all_inf, $string,$all_inf))
                {
                    echo "get_inf Not match,$stockcode\n";
                }
                //key_array 键值  对应中文
                //0公司名称     1所属地域  2英文名称    3所属行业    4曾 用 名
                //5公司网址     6主营业务  7产品名称    8控股股东    9实际控制人
                //10最终控制人  11董事长   12董秘      13法人代表   14总 经 理
                //15注册资金   16员工人数  17电话      18传真      19邮编
                //20办公地址    21成立日期 22发行数量   23发行价格   24上市日期
                //25发行市盈率  26预计募资  27首日开盘价 28发行中签率 29实际募资
                $key_array=array(
                    "0"=>"name","1"=>"area","2"=>"engname","3"=>"industry","4"=>"evername",
                    "5"=>"website","6"=>"operating","7"=>"product","8"=>"cholder","9"=>"rcperson",
                    "10"=>"fcperson","11"=>"chairman","12"=>"secretary","13"=>"legalperson","14"=>"manager",
                    "15"=>"regcap","16"=>"staff","17"=>"tel","18"=>"fax","19"=>"zipcode",
                    "20"=>"address","21"=>"build","22"=>"amount","23"=>"iprice","24"=>"public",
                    "25"=>"ipoperatio","26"=>"eraise","27"=>"firstopen","28"=>"successrate","29"=>"rraise");
                $mode_every_inf="/<span[\w\W]*?<\/span>/";
                foreach ($all_inf[0] as $key => $value) {
                    if ( ! preg_match($mode_every_inf, $value, $every_inf )) {
                        $every_inf[0]="--";
                        echo "Not Match: get_inf_by_code() match".$key_array[$key]." from all inf\n";
                    }
                    $tmp_inf=trim(strip_tags($every_inf[0]));
                    $inf_array[$key_array[$key]]=preg_replace("/\s+/", "", $tmp_inf);
                }
                curl_close($ch);
                break;
            }
            //如果十次都没获取成功，给出错误提示
            if ($time==10) {
                echo "cUrl Error in $stockcode company page: ".curl_error($ch)."\n";
                curl_close($ch);
            }
        }

        /*匹配同花顺上"股东股本"页面上的相关信息*/
        //$url_ths="http://stockpage.10jqka.com.cn/".$stockcode."/holder/";
        $url_ths="http://basic.10jqka.com.cn/".$stockcode."/holder.html";
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_ths);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        for ($time=1; $time <=10 ; $time++) { 
            //echo "Time ： $time...\n";
            $holder_string=curl_exec($ch);
            if(!($holder_string===false)){
                $holder_string=iconv("gbk", "utf-8", $holder_string);
                //股东总数
                //匹配所有时间的股东数
                $mode_total="/\[\".*?\]/";
                if( ! preg_match_all($mode_total, $holder_string, $tmp_holder_array))
                {
                    echo "Not match: get_inf_by_code() match tmp_holder from holder_string,$stockcode\n";
                }

                //遍历$tmp_holder_array
                $mode_everydate_holder="/[0-9a-z.-]+/";
                foreach ($tmp_holder_array[0] as $key=>$value) {
                    if( ! preg_match_all($mode_everydate_holder, $value, $tmp_every_array))
                    {   
                        echo "Not Match: get_inf_by_code() match tmp_every_array from tmp_holder,$stockcode\n";
                    }
                    array_push($holder_date, $tmp_every_array[0][0]);
                    array_push($holder_total, $tmp_every_array[0][1]);
                    array_push($holder_price, $tmp_every_array[0][2]);
                }

                //十大流通股东fholder 
                //匹配有些困难，如果很新的公司（同花顺只有一个日期的表），匹配会失败。
                $mode_fholder_all="/<div class=\"m_tab_content2 clearfix\" id=\"fher_1\" >[\w\W]*?<div class=\"m_tab_content2/";
                if( ! preg_match($mode_fholder_all,$holder_string,$fholder_all))
                {
                    echo "Not Match: get_inf_by_code() match fholder_all from holder_string,$stockcode\n";
                }
                $mode_fholder="/<span id=[\w\W]*?<\/span>/";
                if( ! preg_match_all($mode_fholder, $fholder_all[0], $tmp_fholder_array))
                {
                    echo "Not Match: get_inf_by_code() match tmp_fholder_array from fholder_all,$stockcode\n";
                }
                foreach ($tmp_fholder_array[0] as $key => $value) {
                    $fhoder_array[$key]=strip_tags($value);
                }

                //十大股东tholder  
                $mode_tholder_all="/<div class=\"m_tab_content2 clearfix\" id=\"ther_1\" >[\w\W]*?<div class=\"m_tab_content2/";
                if( ! preg_match($mode_tholder_all, $holder_string, $tholder_all))
                {
                    echo "Not Match: get_inf_by_code() match tholder_all from holder_string,$stockcode\n";
                }
                $mode_tholder="/<span id=[\w\W]*?<\/span>/";
                if( ! preg_match_all($mode_tholder, $tholder_all[0], $tmp_tholder_array))
                {
                    echo "Not Match: get_inf_by_code() match tmp_tholder_array from tholder_all,$stockcode\n";
                }
                foreach ($tmp_tholder_array[0] as $key => $value) {
                    $tholder_array[$key]=strip_tags($value);
                }
                curl_close($ch);
                break;
            }
            //如果十次都没获取成功，给出错误提示
            if ($time==10) {
                echo "cUrl Error in $stockcode company page: ".curl_error($ch)."\n";
                curl_close($ch);
            }
        }
        $result_array=array(
            "intro"=>$Inf,"inf"=>$inf_array,
            "date"=>$holder_date,"holder"=>$holder_total,"price"=>$holder_price,
            "fhoder"=>$fhoder_array,"tholder"=>$tholder_array);  
        return $result_array;
    }

    //获取股本变动
    //300184 002504 这两个同花顺上的网页不存在 因此会出错
    function get_capitalization_by_code($stockcode)
    {
        $url="http://stockpage.10jqka.com.cn/".$stockcode."/holder/";
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        for ($time=1; $time <=10 ; $time++) {
            $string=curl_exec($ch);
            if (!($string===false)) {
                $mode_get_table="/<table class=\"m_table m_hl mt15\">[\w\W]*?<\/table>/";
                if(!preg_match($mode_get_table, $string,$table))
                {
                   echo "Not Match: get_capitalization_by_code() match table from html\n";
                }
                //echo "<pre>".htmlspecialchars($table_array[0])."</pre>";

                $mode_get_row="/<tr>[\w\W]*?<\/tr>/";
                if( !preg_match_all($mode_get_row, $table[0],$row_array))
                {
                    echo "Not Match: get_capitalization_by_code() match row from table \n";
                }
                $date_array=array();
                $capital_array=array();
                $mode_get_date="/<th class=\"tc f12\">.*<\/th>/";
                $mode_get_capital="/<td>.*<\/td>/";
                foreach ($row_array[0] as $key => $value) {
                    if ($key==0) {
                        continue;
                    }
                    if(!preg_match($mode_get_date, $value, $date))
                    {
                        echo "Not Match: get_capitalization_by_code() match date from row \n";
                    }
                    array_push($date_array, strip_tags($date[0]));

                    if(!preg_match($mode_get_capital, $value, $capital))
                    {
                        echo "Not Match: get_capitalization_by_code() match capitalization from row \n";
                    }
                    array_push($capital_array, strip_tags($capital[0]));
                }
                curl_close($ch);
                break;
            }
            if ($time==10) {
                echo "cUrl Error in $stockcode company page: ".curl_error($ch)."\n";
                curl_close($ch);
            }
        }
        $result_array=array("date"=>$date_array,"cap"=>$capital_array);
        return $result_array;
    }


?>