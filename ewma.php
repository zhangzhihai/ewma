<?php

class ewma{
    
    //默认情况下，我们平均超过一一分钟的时间，这意味着平均
    //在这一时期的指标的年龄是30秒。
    const AVG_METRIC_AGE = 30.0;
    
    //从平均年龄计算衰减因子的公式
    const DECAY = 2 / 31;
    const decay = 2 / 31;
    //(1-指数2/31)^10次数 = 历史影响力度
    
    //大于多少个值开始使用平滑值，该叫样本采集出平均值
    const WARMUP_SAMPLES = 10;
    
    //添加一个值，该系列和更新的移动平均。
    public $value = 0;
    function Add($value)
    {
        if ($this->value == 0) 
        {
            $this->value = $value;
        } else {
            $this->value = ($value * self::DECAY) + ($this->value * (1 - self::DECAY));
        }
    }
    
    
    function Set($value)
    {
        $this->value = $value;
    }
    
    function Get(){
        return $this->value;
    }
    
    
    
    
    public $val=0;
    public $count = 0;

    
    function addewma($value)
    {
        //总行数小于10
        if ($this->count < self::WARMUP_SAMPLES) {
            $this->count++;
            $this->val += $value;
        //总记录数等于10时
        } else if ($this->count == self::WARMUP_SAMPLES) {
            //计算10个数据的平均值
            $this->val = $this->val / self::WARMUP_SAMPLES;
            $this->count++;
        } else {
            //超出10部分进算ewma值
            $this->val = ($value * self::decay) + ($this->val * (1 - self::decay));
        }
    }
    
    
    function getewma(){
        
        if ($this->count <= self::WARMUP_SAMPLES) {
            return 0.0;
        }
        return $this->val;
    }
    

    function setewma($value) {
        $this->value = $value;
        
        if ($this->count <= self::WARMUP_SAMPLES)
        {
            $this->count = self::WARMUP_SAMPLES + 1;
        }
    }
    
    
}


$samples = array(
    4599, 5711, 4746, 4621, 5037, 4218, 4925, 4281, 5207, 5203, 5594, 5149,
    4948, 4994, 6056, 4417, 4973, 4714, 4964, 5280, 5074, 4913, 4119, 4522,
    4631, 4341, 4909, 4750, 4663, 5167, 3683, 4964, 5151, 4892, 4171, 5097,
    3546, 4144, 4551, 6557, 4234, 5026, 5220, 4144, 5547, 4747, 4732, 5327,
    5442, 4176, 4907, 3570, 4684, 4161, 5206, 4952, 4317, 4819, 4668, 4603,
    4885, 4645, 4401, 4362, 5035, 3954, 4738, 4545, 5433, 6326, 5927, 4983,
    5364, 4598, 5071, 5231, 5250, 4621, 4269, 3953, 3308, 3623, 5264, 5322,
    5395, 4753, 4936, 5315, 5243, 5060, 4989, 4921, 4480, 3426, 3687, 4220,
    3197, 5139, 6101, 5279,
);

$ewma = new ewma();

foreach ($samples as $v){
    $ewma->Add($v);
}

$samp = $ewma->Get();




foreach ($samples as $k=>$v){
    if($k%5==0){
        print_r(($samp - $ewma->getewma())."\n");
    }
    $ewma->addewma($v);
}

//print_r($ewma->getewma());




