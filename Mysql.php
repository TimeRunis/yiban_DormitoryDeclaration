<?php
// 数据库类
class Mysql{
    // 属性
    public $host; // 连接地址
    public $user; // 用户名
    public $pwd; // 密码
    public $charset; // 字符集
    public $db; // 数据库名
    public $link; // 数据库链接
    public $sql2; // 执行的sql语句
    public $sql=[ // 拼接的sql语句
        'from'=>'', // 表名
        'field'=>'*', // 字段
        'where'=>'', // 条件
        'group'=>'', // 分组
        'having'=>'', // 筛选
        'order'=>'', // 排序
        'limit'=>'', // 限制
        'alias'=>'', // 主表起别名
        'join'=>'' // 连表
    ];
    // 创建连接
    protected function link()
    {
        // 生成链接
        $this->link=mysqli_connect($this->host,$this->user,$this->pwd,$this->db);
        mysqli_set_charset($this->link,$this->charset);
    }
    // 构造方法 给属性进行赋值
    public function __construct($host,$user,$pwd,$charset,$db)
    {
        $this->host=$host;
        $this->user=$user;
        $this->pwd=$pwd;
        $this->charset=$charset;
        $this->db=$db;
        // 在确保属性有值的情况下 进行创建链接
        $this->link();
    }
    // 方法 增删改查
    // 查询方法
    // 最终方法
    // 查询一组数据 二维数组
    // 返回值为结果二维数组
    public function getAll()
    {
        $this->sql2="select {$this->sql['field']} from {$this->sql['from']} {$this->sql['alias']} {$this->sql['join']}
        {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        $list=mysqli_fetch_all($rt,1);
        mysqli_free_result($rt);
        $this->resetSql(); // 重置数组
        return $list;
    }
    // 最终方法
    // 查询一条数据 一维数组
    public function getOne()
    {
        $this->sql2="select {$this->sql['field']} from {$this->sql['from']} {$this->sql['alias']} {$this->sql['join']}
        {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        $one=mysqli_fetch_assoc($rt);
        mysqli_free_result($rt);
        $this->resetSql(); // 重置数组
        return $one;
    }
    protected function getError()
    {
        echo "<h1 style='color:red;font-size: 50px;'>你的sql语句错了</h1>";
        echo "错误的语句是 : {$this->sql2}<br>";
        echo "错误的信息是 : ".mysqli_error($this->link);
        exit;
    }
    // 最终方法
    // 查询一个值 string
    public function getValue()
    {
        $this->sql2="select {$this->sql['field']} from {$this->sql['from']} {$this->sql['alias']} {$this->sql['join']}
        {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        $one=mysqli_fetch_row($rt);
        mysqli_free_result($rt);
        $this->resetSql(); // 重置数组
        return $one[0];
    }
    // 析构方法中 关闭链接
    public function __destruct()
    {
        mysqli_close($this->link);
    }
    // 删除方法
    // 传入两个形参 1. 表名  2. 条件(不传的情况下 默认删除全部的数据)
    public function delete($table,$where="1=1")
    {
        $this->sql2="delete from `{$table}` where {$where}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        // 返回删除的数据条数
        return mysqli_affected_rows($this->link);
    }
    // 软删
    // 传入三个形参 1. 表名  2. 条件(不传的情况下 默认删除全部的数据)   3. 软删字段名(默认为del)
    public function softDelete($table,$where="1=1",$column='del')
    {
        $time=time();
        $this->sql2="update `{$table}` set `{$column}`={$time} where {$where}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        // 返回删除的数据条数
        return mysqli_affected_rows($this->link);
    }
    // 添加
    // 形参2个  1. 表名  2. 数据数组(键是字段,值是值)
    public function insert($table,$arr)
    {
        $column="";
        $value="";
        foreach ($arr as $k=>$v){
            $column.="`{$k}`,";
            $value.="'{$v}',";
        }
        $column=rtrim($column,","); // 将数组的键提取出来 拼接成字段们
        $value=rtrim($value,","); // 将数组的值提取出来拼接成值们
        $this->sql2="insert into `{$table}` ({$column}) values ({$value})";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        // 添加语句执行成功 返回新添加的主键
        return mysqli_insert_id($this->link);
    }
    // 修改
    // 参数 1. 表名 2.数据数组  3. 条件
    public function update($table,$arr,$where="1=1")
    {
        $str="";
        foreach ($arr as $k=>$v){
            $str.="`{$k}`='{$v}',";
        }
        $str=rtrim($str,",");
        $this->sql2="update `{$table}` set {$str} where {$where}";
        $rt=mysqli_query($this->link,$this->sql2);
        if($rt==false){
            $this->getError();
        }
        // 返回影响行数
        return mysqli_affected_rows($this->link);
    }
    // 以下是 连贯方法
    // 中间方法 拼接关键词 返回$this
    public function from($table)
    {
        $this->sql['from']=$table;
        // 方法中返回当前对象 可以在此方法执行完毕之后
        // 再继续调用其他方法
        return $this;
    }
    public function field($column)
    {
        $this->sql['field']=$column;
        return $this;
    }
    public function where($where="1=1")
    {
        $this->sql['where']="where ".$where;
        return $this;
    }
    public function group($group)
    {
        $this->sql['group']="group by ".$group;
        return $this;
    }
    public function having($having)
    {
        $this->sql['having']="having  ".$having;
        return $this;
    }
    public function order($order)
    {
        $this->sql['order']="order by ".$order;
        return $this;
    }
    public function limit($limit)
    {
        $this->sql['limit']="limit ".$limit;
        return $this;
    }
    public function alias($name)
    {
        $this->sql['alias']="as ".$name;
        return $this;
    }
    public function join($join)
    {
        $this->sql['join']=$join;
        return $this;
    }
    // 将拼接sql语句的数组重置成初始值
    public function resetSql()
    {
        $this->sql=[
            'from'=>'', // 表名
            'field'=>'*', // 字段
            'where'=>'', // 条件
            'group'=>'', // 分组
            'having'=>'', // 筛选
            'order'=>'', // 排序
            'limit'=>'', // 限制
            'alias'=>'', // 主表起别名
            'join'=>'' // 连表
        ];
    }
}