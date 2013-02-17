<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
require("../../config.php");
require_once("$CFG->dirroot/enrol/alipay/lib.php");
require_once($CFG->libdir.'/eventslib.php');
require_once($CFG->libdir.'/enrollib.php');
require_once("alipay_config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>支付宝即时到账交易接口</title>
	</head>
    <body>

<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];
	
 if (empty($_POST) and empty($_GET)) {
        print_error("Sorry, you can not use the script that way.");
    }

    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    //	id	seller_email	trade_no	courseid	cost	userid	payment_status	timeupdated
    $data = new stdClass();

    //应该是GET参数
    foreach ($_GET as $key => $value) {
        $data->$key = $value;
    }

    $dingdan = $data->out_trade_no;		//获取订单号
    $total   = $data->total_fee;		//获取总价格

    $enrol_info = $DB->get_record("enrol_alipay", array("trade_no"=>$dingdan));

    if (empty($enrol_info)) {
        echo "订单不存在！";
        die;
    }

    $_GET['id']=$enrol_info->courseid;

    if (! $context = get_context_instance(CONTEXT_COURSE, $enrol_info->courseid)) {
        include("$CFG->dirroot/enrol/alipay/return.php");die;
    }

    if (0!=$enrol_info->payment_status) {   // Make sure this order doesn't already pay
        include("$CFG->dirroot/enrol/alipay/return.php");die;

    }

    if($seller_email!==$data->seller_email){
        //include("$CFG->dirroot/enrol/alipay/return.php");die;
    }
    
    if (! $plugin_instance = $DB->get_record("enrol", array("enrol"=>'alipay',"courseid"=>$enrol_info->courseid,"status"=>0))) {
        
        //include("$CFG->dirroot/enrol/alipay/return.php");die;
    }

    // Check that amount paid is the correct amount
    if ( (float) $plugin_instance->cost <= 0 ) {
        $cost = (float) $plugin->get_config('cost');
    } else {
        $cost = (float) $plugin_instance->cost;
    }

    if ($total < $cost) {
        $cost = format_float($cost, 2);
        $errorreason = message_alipay_error_to_admin("Amount paid is not enough ($total < $cost))", $data);
        include("$CFG->dirroot/enrol/alipay/return.php");die;
    }
    

  
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
			    $enrol_info->payment_status = 1; //已成功交易
        $DB->update_record("enrol_alipay", $enrol_info);
        if ($plugin_instance->enrolperiod) {
            $timestart = time();
            $timeend   = $timestart + $plugin_instance->enrolperiod;
        } else {
            $timestart = 0;
            $timeend   = 0;
        }

        // Enrol user
        $plugin->enrol_user($plugin_instance, $enrol_info->userid, $plugin_instance->roleid, $timestart, $timeend);

        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                             '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }
        include("$CFG->dirroot/enrol/alipay/return.php");
        
	echo '支付成功';
	die;		
        
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	echo "开通成功<br />";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
    include("$CFG->dirroot/enrol/alipay/return.php");
}

function alipay_sorry(){
    global $CFG;
    include("$CFG->dirroot/enrol/alipay/return.php");
}
?>
    </body>
</html>
