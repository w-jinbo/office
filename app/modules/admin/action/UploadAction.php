<?php

/*
 * 文件上传
 * @Author: WangJinBo <wangjb@pvc123.com>
 * @Date: 2019-07-29 14:43:15 
 * @Last Modified by: WangJinBo
 * @Last Modified time: 2019-07-29 15:51:38
 */

 namespace app\admin\action;

use herosphp\files\FileUpload;

class UploadAction extends BaseAction {
    
    public function __construct() {
        parent::__construct();
    }

    public function img() {
        $result = array(
            'code' => '-1',
            'msg' => '上传失败',
        );

        $config = array(
            "upload_dir" => "../static/upload/" . date('Y') . "/" . date('m'),
            //允许上传的文件类型
            'allow_ext' => 'jpg|jpeg|png|gif|bmp',
            //图片的最大宽度, 0没有限制
            'max_width' => 0,
            //图片的最大高度, 0没有限制
            'max_height' => 0,
            //文件的最大尺寸
            'max_size' =>  1024000,     /* 文件size的最大 1MB */
        );
        $upload = new FileUpload($config);
        $img = $upload->upload('file');
        if (!is_array($img)) {
            echo json_encode($img);
            die();
        }
        $result['data']['src'] = '../' . $img['file_path'];
        $result['code'] = 0;
        $result['msg'] = '上传成功';
        echo json_encode($result);
        die();
    }
}