
<?php

header("Content-type:text/html;charset=utf-8");
/**
 * 替换表情
 *
 * @param $t
 */
 var_dump(microtime(true));
$stime = microtime(true); //测试开始
  
function biaoqing($t){
    $emos = array(
        '[face1.gif]'=>'撇嘴',
        '[face2.gif]'=>'色',
        '[face3.gif]'=>'发呆',
        '[face4.gif]'=>'得意',
        '[face5.gif]'=>'流泪',
        '[face6.gif]'=>'害羞',
        '[face7.gif]'=>'闭嘴',
        '[face8.gif]'=>'睡',
        '[face9.gif]'=>'大哭',
        '[face10.gif]'=>'尴尬',
        '[face11.gif]'=>'发怒',
        '[face12.gif]'=>'调皮',
        '[face13.gif]'=>'龇牙',
        '[face14.gif]'=>'微笑',
        '[face15.gif]'=>'难过',
        '[face16.gif]'=>'酷',
        '[face17.gif]'=>'冷汗', //老表情 http://pub.idqqimg.com/lib/qqface/17.gif
        '[face18.gif]'=>'抓狂',
        '[face19.gif]'=>'吐',
         
        '[face20.gif]'=>'偷笑',
        '[face21.gif]'=>'可爱',
        '[face22.gif]'=>'白眼',
        '[face23.gif]'=>'傲慢',
        '[face24.gif]'=>'饥饿',
        '[face25.gif]'=>'困',
        '[face26.gif]'=>'惊恐',
        '[face27.gif]'=>'流汗',
        '[face28.gif]'=>'憨笑',
        '[face29.gif]'=>'大兵',
         
        '[face30.gif]'=>'奋斗',
        '[face31.gif]'=>'咒骂',
        '[face32.gif]'=>'疑问',
        '[face33.gif]'=>'嘘',
        '[face34.gif]'=>'晕',
        '[face35.gif]'=>'折磨',
        '[face36.gif]'=>'衰',
        '[face37.gif]'=>'骷髅',
        '[face38.gif]'=>'敲打',
        '[face39.gif]'=>'再见',
         
        '[face40.gif]'=>'企鹅发呆', //【属于老表情，新QQ表情里没有】
        '[face41.gif]'=>'发抖',
        '[face42.gif]'=>'爱情',
        '[face43.gif]'=>'跳跳',
        '[face44.gif]'=>'企鹅拿放大镜', //【属于老表情，新QQ表情里没有】
        '[face45.gif]'=>'企鹅Q妹',//【属于老表情，新QQ表情里没有】
        '[face46.gif]'=>'猪头',
        '[face47.gif]'=>'猫头',//【属于老表情，新QQ表情里没有】
        '[face48.gif]'=>'狗头',//【属于老表情，新QQ表情里没有】
        '[face49.gif]'=>'抱抱',
         
        '[face50.gif]'=>'美金标志', //【属于老表情，新QQ表情里没有】
        '[face51.gif]'=>'灯泡', //【属于老表情，新QQ表情里没有】
        '[face52.gif]'=>'冰淇淋',
        '[face53.gif]'=>'蛋糕',
        '[face54.gif]'=>'闪电',
        '[face55.gif]'=>'炸弹',
        '[face56.gif]'=>'刀',
        '[face57.gif]'=>'足球',
        '[face58.gif]'=>'音乐符号',
        '[face59.gif]'=>'便便',
         
        '[face60.gif]'=>'咖啡',
        '[face61.gif]'=>'饭',
        '[face62.gif]'=>'胶囊', //【属于老表情，新QQ表情里没有】
        '[face63.gif]'=>'玫瑰',
        '[face64.gif]'=>'凋谢',
        '[face65.gif]'=>'示爱',
        '[face66.gif]'=>'爱心',
        '[face67.gif]'=>'心碎',
        '[face68.gif]'=>'桌子', //【属于老表情，新QQ表情里没有】
        '[face69.gif]'=>'礼物',
         
        '[face70.gif]'=>'电话', //【属于老表情，新QQ表情里没有】
        '[face71.gif]'=>'钟表', //【属于老表情，新QQ表情里没有】
        '[face72.gif]'=>'信封', //【属于老表情，新QQ表情里没有】
        '[face73.gif]'=>'电视机', //【属于老表情，新QQ表情里没有】
        '[face74.gif]'=>'太阳',
        '[face75.gif]'=>'月亮',
        '[face76.gif]'=>'强',
        '[face77.gif]'=>'弱',
        '[face78.gif]'=>'握手',
        '[face79.gif]'=>'胜利',
         
        '[face80.gif]'=>'老鼠',//【属于老表情，新QQ表情里没有】
        '[face81.gif]'=>'小女孩',//【属于老表情，新QQ表情里没有】
        '[face82.gif]'=>'小男孩',//【属于老表情，新QQ表情里没有】
        '[face83.gif]'=>'墨镜男',//【属于老表情，新QQ表情里没有】
        '[face84.gif]'=>'QGG', //【属于老表情，新QQ表情里没有】
        '[face85.gif]'=>'飞吻',
        '[face86.gif]'=>'恼火',
        '[face87.gif]'=>'瓶子',//【属于老表情，新QQ表情里没有】
        '[face88.gif]'=>'可乐',//【属于老表情，新QQ表情里没有】
        '[face89.gif]'=>'西瓜',
         
        '[face90.gif]'=>'下雨天', //【属于老表情，新QQ表情里没有】
        '[face91.gif]'=>'阴转晴', //【属于老表情，新QQ表情里没有】
        '[face92.gif]'=>'雪人', //【属于老表情，新QQ表情里没有】
        '[face93.gif]'=>'星星', //【属于老表情，新QQ表情里没有】
        '[face94.gif]'=>'辫子女', //【属于老表情，新QQ表情里没有】
        '[face95.gif]'=>'分头男', //【属于老表情，新QQ表情里没有】
        '[face96.gif]'=>'冷汗',
        '[face97.gif]'=>'擦汗',
        '[face98.gif]'=>'抠鼻',
        '[face99.gif]'=>'鼓掌',
         
        '[face100.gif]'=>'糗大了',
        '[face101.gif]'=>'坏笑',
        '[face102.gif]'=>'左哼哼',
        '[face103.gif]'=>'右哼哼',
        '[face104.gif]'=>'哈欠',
        '[face105.gif]'=>'鄙视',
        '[face106.gif]'=>'委屈',
        '[face107.gif]'=>'快哭了',
        '[face108.gif]'=>'阴险',
        '[face109.gif]'=>'亲亲',
         
        '[face110.gif]'=>'吓',
        '[face111.gif]'=>'可怜',
        '[face112.gif]'=>'菜刀',
        '[face113.gif]'=>'啤酒',
        '[face114.gif]'=>'篮球',
        '[face115.gif]'=>'乒乓球',
        '[face116.gif]'=>'嘴唇',
        '[face117.gif]'=>'瓢虫',
        '[face118.gif]'=>'抱拳',
        '[face119.gif]'=>'勾引',
         
        '[face120.gif]'=>'拳头',
        '[face121.gif]'=>'差劲',
        '[face122.gif]'=>'爱你',
        '[face123.gif]'=>'NO',
        '[face124.gif]'=>'OK',
        '[face125.gif]'=>'转圈',
        '[face126.gif]'=>'磕头',
        '[face127.gif]'=>'回头',
        '[face128.gif]'=>'跳绳',
        '[face129.gif]'=>'挥手',
         
        '[face130.gif]'=>'激动',
        '[face131.gif]'=>'街舞',
        '[face132.gif]'=>'激吻',
        '[face133.gif]'=>'左太极',
        '[face134.gif]'=>'右太极',
        '[face136.gif]'=>'双喜',
        '[face137.gif]'=>'鞭炮',
        '[face138.gif]'=>'灯笼',
        '[face139.gif]'=>'发财',
        '[face140.gif]'=>'话筒',
        '[face141.gif]'=>'购物',
        '[face142.gif]'=>'邮件',
        '[face143.gif]'=>'帅',
        '[face144.gif]'=>'喝彩',
        '[face145.gif]'=>'祈祷',
        '[face146.gif]'=>'爆筋',
        '[face147.gif]'=>'棒棒糖',
        '[face148.gif]'=>'喝奶',
        '[face149.gif]'=>'下面',
        '[face150.gif]'=>'香蕉',
        '[face151.gif]'=>'飞机',
        '[face152.gif]'=>'开车',
        '[face153.gif]'=>'左车头',
        '[face154.gif]'=>'车厢',
        '[face155.gif]'=>'右车头',
        '[face156.gif]'=>'多云',
        '[face157.gif]'=>'下雨',
        '[face158.gif]'=>'钞票',
        '[face159.gif]'=>'熊猫',
        '[face160.gif]'=>'灯泡',
        '[face161.gif]'=>'风车',
        '[face162.gif]'=>'闹钟',
        '[face163.gif]'=>'红伞',
        '[face164.gif]'=>'气球',
        '[face165.gif]'=>'钻戒',
        '[face166.gif]'=>'沙发',
        '[face167.gif]'=>'纸巾',
        '[face168.gif]'=>'药',
        '[face169.gif]'=>'手枪',
        '[face170.gif]'=>'青蛙',
         
        '[image={4F161813-1E98-E664-008E-097DDC255C2F}.gif]'=>'潜水',
        '[image={3B6260FF-C1D5-0AB0-C22C-421843E091B8}.gif]'=>'敬礼',
        '[image={64A234EE-8657-DA63-B7F4-C7718460F461}.gif]'=>'石化',
        '[image={D2BD3A1C-0F04-9FE3-8B7A-766FFA55F931}.gif]'=>'安慰',
        '[image={514E8F56-87E1-5828-5039-A6897CF423C8}.gif]'=>'扮鬼脸',
        '[image={719024F2-16B9-C2E4-C3EF-952FC34925D2}.gif]'=>'无语',
        '[image={615393FB-C6E2-AAFE-0A90-8DE5D047DEAF}.gif]'=>'狂汗',
        '[image={B1AC14ED-DEF1-9DCA-5AC3-621B35A2B499}.gif]'=>'叹气',
        '[image={E00A7865-28F3-C5A2-99FE-5AF9017AC901}.gif]'=>'加油',
        '[image={D2991207-F843-5702-3098-B080E23A9549}.gif]'=>'生病',
        '[image={A4BEE470-7A78-F2C6-FD31-3419406CF1C3}.gif]'=>'拜托',
        '[image={CA51C7B7-8E8E-AF54-72B0-576C2B44E4A6}.gif]'=>'孤寂',
        '[image={A2CA8C10-8ACA-4ABD-FEF6-F12D1921ED71}.gif]'=>'惬意',
        '[image={B5F43A21-581C-6B6F-52A3-CFB9FFB264B2}.gif]'=>'烦躁',
        '[image={163F4E6F-DE25-0962-A72D-150580E4E478}.gif]'=>'牵手',
        '[image={98B392FD-ECF8-E435-879C-E1C52CE7D814}.gif]'=>'示爱',
        '[image={4EA75673-526D-F472-4628-8E0B1C2EEA36}.gif]'=>'情书',
        '[image={121340B4-E34A-084C-6B32-A704B51BA78C}.gif]'=>'月饼',
        '[image={17BA3C00-1123-312D-98D0-3D228CF547B8}.gif]'=>'玉兔',
        );
    if(!empty($t) && preg_match_all('/\[.+?\]/',$t,$matches)){
        $matches = array_unique($matches[0]);
        foreach ($matches as $data) {
            if(isset($emos[$data]))
                $t = str_replace($data,$emos[$data],$t);
        }
    }
    return $t;
}
 
$a='[face160.gif]你好啊';
echo biaoqing($a);
 

echo (float)(microtime(true)-$stime);
 
 $kaishi = microtime(true); //测试开始
  
  
// QQ表情函数对应汉字库
// http://pub.idqqimg.com/lib/qqface/1.gif
// echo QQBQ('[face168.gif]');
function QQBQ($BQ) {
$BQ=str_replace('[face1.gif]', '撇嘴',$BQ);
$BQ=str_replace('[face2.gif]', '色',$BQ);
$BQ=str_replace('[face3.gif]', '发呆',$BQ);
$BQ=str_replace('[face4.gif]', '得意',$BQ);
$BQ=str_replace('[face5.gif]', '流泪',$BQ);
$BQ=str_replace('[face6.gif]', '害羞',$BQ);
$BQ=str_replace('[face7.gif]', '闭嘴',$BQ);
$BQ=str_replace('[face8.gif]', '睡',$BQ);
$BQ=str_replace('[face9.gif]', '大哭',$BQ);
 
$BQ=str_replace('[face10.gif]', '尴尬',$BQ);
$BQ=str_replace('[face11.gif]', '发怒',$BQ);
$BQ=str_replace('[face12.gif]', '调皮',$BQ);
$BQ=str_replace('[face13.gif]', '龇牙',$BQ);
$BQ=str_replace('[face14.gif]', '微笑',$BQ);
$BQ=str_replace('[face15.gif]', '难过',$BQ);
$BQ=str_replace('[face16.gif]', '酷',$BQ);
$BQ=str_replace('[face17.gif]', '冷汗',$BQ); //老表情 http://pub.idqqimg.com/lib/qqface/17.gif
$BQ=str_replace('[face18.gif]', '抓狂',$BQ);
$BQ=str_replace('[face19.gif]', '吐',$BQ);
 
$BQ=str_replace('[face20.gif]', '偷笑',$BQ);
$BQ=str_replace('[face21.gif]', '可爱',$BQ);
$BQ=str_replace('[face22.gif]', '白眼',$BQ);
$BQ=str_replace('[face23.gif]', '傲慢',$BQ);
$BQ=str_replace('[face24.gif]', '饥饿',$BQ);
$BQ=str_replace('[face25.gif]', '困',$BQ);
$BQ=str_replace('[face26.gif]', '惊恐',$BQ);
$BQ=str_replace('[face27.gif]', '流汗',$BQ);
$BQ=str_replace('[face28.gif]', '憨笑',$BQ);
$BQ=str_replace('[face29.gif]', '大兵',$BQ);
 
$BQ=str_replace('[face30.gif]', '奋斗',$BQ);
$BQ=str_replace('[face31.gif]', '咒骂',$BQ);
$BQ=str_replace('[face32.gif]', '疑问',$BQ);
$BQ=str_replace('[face33.gif]', '嘘',$BQ);
$BQ=str_replace('[face34.gif]', '晕',$BQ);
$BQ=str_replace('[face35.gif]', '折磨',$BQ);
$BQ=str_replace('[face36.gif]', '衰',$BQ);
$BQ=str_replace('[face37.gif]', '骷髅',$BQ);
$BQ=str_replace('[face38.gif]', '敲打',$BQ);
$BQ=str_replace('[face39.gif]', '再见',$BQ);
 
$BQ=str_replace('[face40.gif]', '企鹅发呆',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face41.gif]', '发抖',$BQ);
$BQ=str_replace('[face42.gif]', '爱情',$BQ);
$BQ=str_replace('[face43.gif]', '跳跳',$BQ);
$BQ=str_replace('[face44.gif]', '企鹅拿放大镜',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face45.gif]', '企鹅Q妹',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face46.gif]', '猪头',$BQ);
$BQ=str_replace('[face47.gif]', '猫头',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face48.gif]', '狗头',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face49.gif]', '抱抱',$BQ);
 
$BQ=str_replace('[face50.gif]', '美金标志',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face51.gif]', '灯泡',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face52.gif]', '冰淇淋',$BQ);
$BQ=str_replace('[face53.gif]', '蛋糕',$BQ);
$BQ=str_replace('[face54.gif]', '闪电',$BQ);
$BQ=str_replace('[face55.gif]', '炸弹',$BQ);
$BQ=str_replace('[face56.gif]', '刀',$BQ);
$BQ=str_replace('[face57.gif]', '足球',$BQ);
$BQ=str_replace('[face58.gif]', '音乐符号',$BQ);
$BQ=str_replace('[face59.gif]', '便便',$BQ);
 
$BQ=str_replace('[face60.gif]', '咖啡',$BQ);
$BQ=str_replace('[face61.gif]', '饭',$BQ);
$BQ=str_replace('[face62.gif]', '胶囊',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face63.gif]', '玫瑰',$BQ);
$BQ=str_replace('[face64.gif]', '凋谢',$BQ);
$BQ=str_replace('[face65.gif]', '示爱',$BQ);
$BQ=str_replace('[face66.gif]', '爱心',$BQ);
$BQ=str_replace('[face67.gif]', '心碎',$BQ);
$BQ=str_replace('[face68.gif]', '桌子',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face69.gif]', '礼物',$BQ);
 
$BQ=str_replace('[face70.gif]', '电话',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face71.gif]', '钟表',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face72.gif]', '信封',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face73.gif]', '电视机',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face74.gif]', '太阳',$BQ);
$BQ=str_replace('[face75.gif]', '月亮',$BQ);
$BQ=str_replace('[face76.gif]', '强',$BQ);
$BQ=str_replace('[face77.gif]', '弱',$BQ);
$BQ=str_replace('[face78.gif]', '握手',$BQ);
$BQ=str_replace('[face79.gif]', '胜利',$BQ);
 
$BQ=str_replace('[face80.gif]', '老鼠',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face81.gif]', '小女孩',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face82.gif]', '小男孩',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face83.gif]', '墨镜男',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face84.gif]', 'QGG',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face85.gif]', '飞吻',$BQ);
$BQ=str_replace('[face86.gif]', '恼火',$BQ);
$BQ=str_replace('[face87.gif]', '瓶子',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face88.gif]', '可乐',$BQ);//【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face89.gif]', '西瓜',$BQ);
 
$BQ=str_replace('[face90.gif]', '下雨天',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face91.gif]', '阴转晴',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face92.gif]', '雪人',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face93.gif]', '星星',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face94.gif]', '辫子女',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face95.gif]', '分头男',$BQ); //【属于老表情，新QQ表情里没有】
$BQ=str_replace('[face96.gif]', '冷汗',$BQ);
$BQ=str_replace('[face97.gif]', '擦汗',$BQ);
$BQ=str_replace('[face98.gif]', '抠鼻',$BQ);
$BQ=str_replace('[face99.gif]', '鼓掌',$BQ);
 
$BQ=str_replace('[face100.gif]', '糗大了',$BQ);
$BQ=str_replace('[face101.gif]', '坏笑',$BQ);
$BQ=str_replace('[face102.gif]', '左哼哼',$BQ);
$BQ=str_replace('[face103.gif]', '右哼哼',$BQ);
$BQ=str_replace('[face104.gif]', '哈欠',$BQ);
$BQ=str_replace('[face105.gif]', '鄙视',$BQ);
$BQ=str_replace('[face106.gif]', '委屈',$BQ);
$BQ=str_replace('[face107.gif]', '快哭了',$BQ);
$BQ=str_replace('[face108.gif]', '阴险',$BQ);
$BQ=str_replace('[face109.gif]', '亲亲',$BQ);
 
$BQ=str_replace('[face110.gif]', '吓',$BQ);
$BQ=str_replace('[face111.gif]', '可怜',$BQ);
$BQ=str_replace('[face112.gif]', '菜刀',$BQ);
$BQ=str_replace('[face113.gif]', '啤酒',$BQ);
$BQ=str_replace('[face114.gif]', '篮球',$BQ);
$BQ=str_replace('[face115.gif]', '乒乓球',$BQ);
$BQ=str_replace('[face116.gif]', '嘴唇',$BQ);
$BQ=str_replace('[face117.gif]', '瓢虫',$BQ);
$BQ=str_replace('[face118.gif]', '抱拳',$BQ);
$BQ=str_replace('[face119.gif]', '勾引',$BQ);
 
$BQ=str_replace('[face120.gif]', '拳头',$BQ);
$BQ=str_replace('[face121.gif]', '差劲',$BQ);
$BQ=str_replace('[face122.gif]', '爱你',$BQ);
$BQ=str_replace('[face123.gif]', 'NO',$BQ);
$BQ=str_replace('[face124.gif]', 'OK',$BQ);
$BQ=str_replace('[face125.gif]', '转圈',$BQ);
$BQ=str_replace('[face126.gif]', '磕头',$BQ);
$BQ=str_replace('[face127.gif]', '回头',$BQ);
$BQ=str_replace('[face128.gif]', '跳绳',$BQ);
$BQ=str_replace('[face129.gif]', '挥手',$BQ);
 
$BQ=str_replace('[face130.gif]', '激动',$BQ);
$BQ=str_replace('[face131.gif]', '街舞',$BQ);
$BQ=str_replace('[face132.gif]', '激吻',$BQ);
$BQ=str_replace('[face133.gif]', '左太极',$BQ);
$BQ=str_replace('[face134.gif]', '右太极',$BQ);
$BQ=str_replace('[face136.gif]', '双喜',$BQ);
$BQ=str_replace('[face137.gif]', '鞭炮',$BQ);
$BQ=str_replace('[face138.gif]', '灯笼',$BQ);
$BQ=str_replace('[face139.gif]', '发财',$BQ);
$BQ=str_replace('[face140.gif]', '话筒',$BQ);
$BQ=str_replace('[face141.gif]', '购物',$BQ);
$BQ=str_replace('[face142.gif]', '邮件',$BQ);
$BQ=str_replace('[face143.gif]', '帅',$BQ);
$BQ=str_replace('[face144.gif]', '喝彩',$BQ);
$BQ=str_replace('[face145.gif]', '祈祷',$BQ);
$BQ=str_replace('[face146.gif]', '爆筋',$BQ);
$BQ=str_replace('[face147.gif]', '棒棒糖',$BQ);
$BQ=str_replace('[face148.gif]', '喝奶',$BQ);
$BQ=str_replace('[face149.gif]', '下面',$BQ);
$BQ=str_replace('[face150.gif]', '香蕉',$BQ);
$BQ=str_replace('[face151.gif]', '飞机',$BQ);
$BQ=str_replace('[face152.gif]', '开车',$BQ);
$BQ=str_replace('[face153.gif]', '左车头',$BQ);
$BQ=str_replace('[face154.gif]', '车厢',$BQ);
$BQ=str_replace('[face155.gif]', '右车头',$BQ);
$BQ=str_replace('[face156.gif]', '多云',$BQ);
$BQ=str_replace('[face157.gif]', '下雨',$BQ);
$BQ=str_replace('[face158.gif]', '钞票',$BQ);
$BQ=str_replace('[face159.gif]', '熊猫',$BQ);
$BQ=str_replace('[face160.gif]', '灯泡',$BQ);
$BQ=str_replace('[face161.gif]', '风车',$BQ);
$BQ=str_replace('[face162.gif]', '闹钟',$BQ);
$BQ=str_replace('[face163.gif]', '红伞',$BQ);
$BQ=str_replace('[face164.gif]', '气球',$BQ);
$BQ=str_replace('[face165.gif]', '钻戒',$BQ);
$BQ=str_replace('[face166.gif]', '沙发',$BQ);
$BQ=str_replace('[face167.gif]', '纸巾',$BQ);
$BQ=str_replace('[face168.gif]', '药',$BQ);
$BQ=str_replace('[face169.gif]', '手枪',$BQ);
$BQ=str_replace('[face170.gif]', '青蛙',$BQ);
 
$BQ=str_replace('[image={4F161813-1E98-E664-008E-097DDC255C2F}.gif]', '潜水',$BQ);
$BQ=str_replace('[image={3B6260FF-C1D5-0AB0-C22C-421843E091B8}.gif]', '敬礼',$BQ);
$BQ=str_replace('[image={64A234EE-8657-DA63-B7F4-C7718460F461}.gif]', '石化',$BQ);
$BQ=str_replace('[image={D2BD3A1C-0F04-9FE3-8B7A-766FFA55F931}.gif]', '安慰',$BQ);
$BQ=str_replace('[image={514E8F56-87E1-5828-5039-A6897CF423C8}.gif]', '扮鬼脸',$BQ);
$BQ=str_replace('[image={719024F2-16B9-C2E4-C3EF-952FC34925D2}.gif]', '无语',$BQ);
$BQ=str_replace('[image={615393FB-C6E2-AAFE-0A90-8DE5D047DEAF}.gif]', '狂汗',$BQ);
$BQ=str_replace('[image={B1AC14ED-DEF1-9DCA-5AC3-621B35A2B499}.gif]', '叹气',$BQ);
$BQ=str_replace('[image={E00A7865-28F3-C5A2-99FE-5AF9017AC901}.gif]', '加油',$BQ);
$BQ=str_replace('[image={D2991207-F843-5702-3098-B080E23A9549}.gif]', '生病',$BQ);
$BQ=str_replace('[image={A4BEE470-7A78-F2C6-FD31-3419406CF1C3}.gif]', '拜托',$BQ);
$BQ=str_replace('[image={CA51C7B7-8E8E-AF54-72B0-576C2B44E4A6}.gif]', '孤寂',$BQ);
$BQ=str_replace('[image={A2CA8C10-8ACA-4ABD-FEF6-F12D1921ED71}.gif]', '惬意',$BQ);
$BQ=str_replace('[image={B5F43A21-581C-6B6F-52A3-CFB9FFB264B2}.gif]', '烦躁',$BQ);
$BQ=str_replace('[image={163F4E6F-DE25-0962-A72D-150580E4E478}.gif]', '牵手',$BQ);
$BQ=str_replace('[image={98B392FD-ECF8-E435-879C-E1C52CE7D814}.gif]', '示爱',$BQ);
$BQ=str_replace('[image={4EA75673-526D-F472-4628-8E0B1C2EEA36}.gif]', '情书',$BQ);
$BQ=str_replace('[image={121340B4-E34A-084C-6B32-A704B51BA78C}.gif]', '月饼',$BQ);
$BQ=str_replace('[image={17BA3C00-1123-312D-98D0-3D228CF547B8}.gif]', '玉兔',$BQ);
    return $BQ;
}
$a='[face160.gif]你好啊';
echo QQBQ($a);
echo (float)(microtime(true)-$kaishi);

var_dump(microtime(true));

