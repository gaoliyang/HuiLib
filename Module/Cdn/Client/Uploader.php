<?php
namespace HuiLib\Module\Cdn\Client;

use HuiLib\Error\Exception;
use HuiLib\Module\Cdn\Utility;
/**
 * HuiLib CDN Uploader库
 * 
 * @author 祝景法
 * @since 2014/04/05
 */
class Uploader extends Base
{
    /**
     * 上传一个文件
     *
     * @param array $file
     */
    public function upload($file)
    {
        //if (!file_exists($file)) {
        // return $this->format(parent::API_FAIL, 'File not exsits.');
        //}
    
        return Uploader::create()->transfer($file);
    }
    
    /**
     * 上传前端传过来的文件
     * 
     * 内容取自$_FILES
     *
     * @param array $file
     */
    public function uploadFiles($type, $files)
    {
        if (!is_array($files) || empty($type)) {
            return $this->format(self::API_FAIL, $this->getHuiLang()->_('cdn.post.empty.type.need'));
        }
        
        $post=array();
        $post['type']=$type;
        $attches=array();
        foreach ($files as $field=>$file){
            if (!empty($file['error']) || $file['size']<=0) {
                return $this->format(self::API_FAIL, $this->getHuiLang()->_('cdn.post.files.error'));
            }
            $attches[$field]='@'.$file['tmp_name'];
            $post[$field.'[name]']=$file['name'];
            $post[$field.'[type]']=$file['type'];
            $post[$field.'[error]']=$file['error'];
            $post[$field.'[size]']=$file['size'];
            $post[$field.'[sha1]']=sha1_file($file['tmp_name']);
        }

        return $this->curl($post, $attches);
    }
    
    /**
     * 发起请求
     * 
     * data['type'] 上传的文件类型：image, file
     * data['file1']='@/var/test.jpg'
     * data['file1[name]']='hello world'
     * data['file1[type]']='image/jpeg'
     * data['file1[error]']=0
     * data['file1[size]']=13245
     * 
     * @param array $post 提交的数据
     * @param array $attches 上传的附件储存地址 不参与加密
     */
    protected function curl($post, $attches)
    {
        $config=parent::getConfig();
        $post['app_id']=$config['app_id'];
        try {
            $post['hash']=Utility::encrypt($post, $config['app_secret']);
        }catch (Exception $e){
            return $this->format(self::API_FAIL, $e->getMessage());
        }
        $post+=$attches;

        $handle=curl_init();
        
        //http://rpc.iyunlin.com/cdn/upload/type
        curl_setopt($handle, CURLOPT_URL, $config['upload'].$post['type']);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        
        $result=curl_exec($handle);
        echo $result;die();
        
        return json_decode($result, TRUE);
    }
}