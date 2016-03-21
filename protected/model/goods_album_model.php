<?php
class goods_album_model extends Model
{
    public $table_name = 'goods_album';
    
    /**
     * 添加并上传相册图片
     * @param array  $file_input   文件域名称          
     * @param array  $goods_id     商品ID
     */
    public function add_album_image($file_input, $goods_id)
    {
        $save_path = 'upload'.DS.'goods'.DS.'album'.DS;
        $uploader = new uploader($save_path);
        $album = $uploader->upload_file($file_input);
        foreach($album as $v)
        {
            if($v['error'] == 'success')
                $this->create(array('goods_id' => $goods_id, 'image' => $v['name']));
            else
                return $v['error'];
        }
        return TRUE;
    }
}
