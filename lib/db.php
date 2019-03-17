<?php
//数据库操作类
class Db
{
    //初始化连接数据库
    public function __construct()
    {
        //存储不同数据库连接的参数
        $this->pdo_conf =[
            'default' => ['dsn'=>'mysql:host=localhost;dbname=myblog','username'=>'root','password'=>'root'],
            'php' => ['dsn'=>'mysql:host=localhost;dbname=php','username'=>'root','password'=>'root']
        ];
        //pdo 池
        $this->pdo_list = [];
    }
    //指定pdo
    private function _get_pdo($pdo)
    {
        //判断是否存在pdo 且pdo有值
        if (isset($this->pdo_list[$pdo]) && $this->pdo_list[$pdo]){
            $this->pdo = $this->pdo_list[$pdo];
            return ;
        }
        $this->pdo = new PDO($this->pdo_conf[$pdo]['dsn'],$this->pdo_conf[$pdo]['username'],$this->pdo_conf[$pdo]['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //保存到pdo池
        $this->pdo_list[$pdo] = $this->pdo;
    }
    //指定表
    public function table($table, $pdo= 'default')
    {
        $this->_get_pdo($pdo);//指定pdo
        $this->table = $table;
        $this->field = '*';
        $this->where = [];
        $this->order = null;
        $this->limit = 0;
        $this->last_sql = '';
        return $this;
    }
    //指定字段
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }
    //指定条件
    public function where(array $where)
    {
        $this->where = $where;
        return $this;
    }
    //指定排序
    public function order($order)
    {
        $this->order = $order;
        return $this;
    }
    //指定返回数量
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    //查询一条或多条数据
    public function select()
    {
//        $sql = "SELECT {$this->field} FROM {$this->table} WHERE {$this->where} LIMIT 1;";
//        exit($sql);
        $sql = $this->_build_sql('select');
        $stmt = $this->pdo->prepare($sql);
        $this->_bind_value($stmt);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
//        $res = (count($res)==1) ? $res[0] : $res;
        return $res;
//        $res = $res ? $res[0] : 'false';//判断语句是否执行成功
//        echo '<pre>';
//        print_r($res);
    }
    //绑定参数
    private function _bind_value($stmt, $data=null)
    {
        if ($this->where){
            foreach($this->where as $key => $value){
                //判断where数组是否有符号，有则去掉符号
                if (strpos($value, '>') !== false){
                    $value = ltrim($value, '>');
                }elseif (strpos($value,'<') !== false){
                    $value = ltrim($value, '<');
                }elseif (strpos($value, 'like') !== false){
                    $value = trim(ltrim($value, 'like'),' ');
                }
//                $value = is_string($value) ? "'{$value}'" : $value;
//                echo $value.'<br>';
                $stmt->bindValue(":{$key}",$value);
            }
        }
        if ($data){
            foreach ($data as $key => $val){
                $stmt->bindValue(":{$key}",$val);
            }
        }
    }

    //插入记录
    public function insert(array $data)
    {
        $sql = $this->_build_sql('insert',$data);
        $stmt = $this->pdo->prepare($sql);
        $this->_bind_value($stmt, $data);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    //删除纪录
    public function delete()
    {
        $sql = $this->_build_sql('delete');
        $stmt = $this->pdo->prepare($sql);
        $this->_bind_value($stmt);
        $stmt->execute();
        return '成功删除了'.$stmt->rowCount().'条数据';
    }
    //更改纪录
    public function update($data)
    {
        $sql = $this->_build_sql('update',$data);
        $stmt = $this->pdo->prepare($sql);
        $this->_bind_value($stmt,$data);
        $stmt->execute();
        return $stmt->rowCount();
    }
    //查询记录数量
    public function count()
    {
        $sql = $this->_build_sql('count');
        $stmt = $this->pdo->prepare($sql);
        $this->_bind_value($stmt);
        $stmt->execute();
        return $stmt->fetchColumn(0);
    }
    //分页查询
    public function pages($page, $page_size, $path){
        //总页数
        $total = $this->count();
        $pages_sum = ceil($total/$page_size);
        $page = ($page>0&&$page<=$pages_sum) ? $page : 1;
        $this->limit = ($page-1)*$page_size.','.$page_size;//limit 偏移量，每页数量
        $data = $this->select();
        $pages = $this->_pages_html($page, $page_size, $path, $total);
        return ['total'=>$total,'data'=>$data,'pages'=>$pages];
    }
    //分页的html代码
    private function _pages_html($page, $page_size,$path, $total)
    {
        $html = '';
        $symbol = '';
//        $path = '/index.php';
       //页面上显示的7个页码
        //总页数, 最后一页
        $pages_sum = ceil($total/$page_size);
        $cur_page = ($page>0&&$page<=$pages_sum) ? $page : 1;
        $start = ($cur_page-2)>0 ? ($cur_page-2) : 1;
        $start = (($pages_sum-4) > $start) ? $start : ($pages_sum-4);
        $start = ($start > 0) ? $start : 1;
        $end = ($start+4 < $pages_sum) ? ($start+4) : $pages_sum;
//        $end = $end
        //判断符号
        if (strpos($path,'?')){
            $symbol = '&';
        }else{
            $symbol = '?';
        }
        if($cur_page>1){
            //首页k
            $html .= "<li><a href='{$path}{$symbol}page=".($cur_page-1)."'><span>上一页</span></a></li>";
        }
        if ($start != 1){
            $html .= "<li><a href='{$path}{$symbol}page=1'>1</a></li>";
            if($start != 2){$html .= "<li><a><strong>...</strong></a></li>";}
        }
        //中间页
        if ($start != $end){
            for ($i=$start; $i<=$end; $i++){
                if ($cur_page == $i){
                    $html .= "<li class='active'><a href='{$path}{$symbol}page={$i}' disabled><span>$i</span></a></li>";
                }else{$html .= "<li><a href='{$path}{$symbol}page={$i}'><span>$i</span></a></li>";}
            }
        }
        if ($end != $pages_sum){
            if($end != ($pages_sum-1)){$html .= "<li><a><strong>...</strong></a></li>";}
            $html .= "<li><a href='{$path}{$symbol}page={$pages_sum}'>{$pages_sum}</a></li>";
        }
//        if ($cur_page <= $max){
//            for($i=1;$i<= ($max>$pages_sum? $pages_sum : $max);$i++){
//                $html .= "<li><a href=''>$i</a></li>";
//            }
//            $last_page = "<li><a href=''>$pages_sum</a></li>";
//            if($max < $pages_sum){
//                $html .= "<li>...</li>".$last_page;
//            }
//        }elseif($cur_page < ($pages_sum-floor($max/2))){
//            $html .= "<li><a href=''>1</a></li><li>...</li>";
//            for ($i= ($cur_page-floor($max/2)); $i<= ($cur_page+floor($max/2)); $i++){
//                $html .= "<li><a href=''>$i</a></li>";
//            }
//            $html .= "<li><a href=''>...</a></li><li><a href=''>$pages_sum</a></li>";
//        }else{
//            $html .= "<li><a href=''>1</a></li><li>...</li>";
//            for ($i = ($pages_sum-$max+1); $i<= $pages_sum; $i++){
//                $html .= "<li><a href=''>$i</a></li>";
//            }
//        }
//         下一页
        if ($cur_page < $pages_sum){
            $html .= "<li><a href='{$path}{$symbol}page=".($cur_page+1)."'><span>下一页</span></a></li>";
        }

        return '<ul class="pagination">'.$html.'</ul';
    }
    //拼接sql语句
    private function _build_sql($type, $data = null)
    {
        $sql = '';
        if($type == 'select'){
            $sql = $this->_select_sql();
        }
        if ($type == 'insert'){
            $sql = $this->_insert_sql($data);
        }
        if ($type == 'delete'){
           $sql = $this->_delete_sql();
        }
        if ($type == 'update'){
            $sql = $this->_update_sql($data);
        }
        if($type == 'count'){
            $sql = $this->_count_sql();
        }
        $this->last_sql = $sql;
        return $sql;
    }
    //拼接select语句
    private function _select_sql()
    {
        $sql = "SELECT {$this->field} FROM {$this->table}";
        $this->where && $sql .= $this->_where_sql();
        $this->order && $sql .= ' ORDER BY '.$this->order;
        $this->limit && $sql .= ' LIMIT '.$this->limit;
        return $sql;
    }
    //拼接insert 语句
    private function _insert_sql($data)
    {
        $field = $value = [];
        $fields = $values = '';
        //处理数组$data
        foreach ($data as $key => $val){
            $field[] = "`{$key}`";
//            $value[] = is_string($val) ? '\''.$val.'\'' : $val;
            $value[] = ":{$key}";
        }
        $fields = implode(',', $field);
        $values = implode(',',$value);
        $sql = "INSERT IGNORE INTO {$this->table} ({$fields}) VALUES ({$values})";
        return $sql;
    }
    //拼接update语句
    private function _update_sql($data)
    {
        $sql = "UPDATE {$this->table} SET ";
        foreach ($data as $key => $value){
            $sql .= "{$key}=:{$key},";
        }
        $sql = rtrim($sql,',').$this->_where_sql();
        $this->limit && $sql .= ' LIMIT '.$this->limit;
        return $sql;
    }
    //拼接delete语句
    public function _delete_sql()
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->_where_sql();
        return $sql;
    }
    //拼接count语句
    private function _count_sql()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $sql .= $this->_where_sql();
        return $sql;
    }
    //拼接where语句
    private function _where_sql()
    {
        $where = '';
        if(is_array($this->where) && $this->where){
            //接受处理where数组
            foreach($this->where as $key => $value){
//          处理value中的符号
                if (strpos($value, '>') !== false){
                    $value = ">:{$key}";
                }elseif (strpos($value,'<') !== false){
                    $value = "<:{$key}";
                }elseif (strpos($value, 'like') !== false){
                    $value = " like :{$key}";
                }else{
                    $value = "=:{$key}";
                }
                $where .= "`{$key}`".$value." AND ";
            }
            $where = $where ? ' WHERE '.rtrim($where," AND ") : '';
        }
        return $where;
    }
    //获取最后一次的sql语句
    public function get_last_sql()
    {
        return $this->last_sql;
    }
}