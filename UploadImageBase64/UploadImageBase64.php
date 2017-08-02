<?php 
namespace Jsnlib;
/**
 * 使用圖片的 Base64 編碼上傳圖片
 * 
 * 關於繼承的部分：
 * 繼承 \Jsnlib\Copy\Urlimg 的功能，可以方便使用 parnet::resize(), parnet::org(), 
 * parnet::copy()
 */
class UploadImageBase64 extends \Jsnlib\Copy\Urlimg
{
    // 圖片資源原始碼
    protected $source;

    // 使用者的設定
    protected $setting;

    // 組合路徑與檔名的完整檔名
    protected $file;

    // 存檔紀錄方塊
    protected $box = [];

    /**
     * 設定
     * @param   $param['base64']     圖片的 Base64 編碼
     * @param   $param['filename']   檔案名稱
     * @param   $param['path']       儲存的路徑
     * @param   $param['mode']       檔案權限，如 '0777'
     */
    public function setting(array $param)
    {
        $this->setting = new \Jsnlib\Ao($param);

        if (empty($this->setting->base64)) 
            throw new \Exception("請指定參數 base64");

        if (empty($this->setting->filename)) 
            throw new \Exception("請指定參數 filename");
        
        if (empty($this->setting->path)) 
            throw new \Exception("請指定參數 path");

        if (empty($this->setting->mode)) 
            throw new \Exception("請指定參數 mode");
        
        $this->source  = base64_decode($this->setting->base64);
        
        // 組合路徑與檔名
        $path          = realpath($this->setting->path);
        $filename      = trim($this->setting->filename, "\ /");
        $this->file    = $path . DIRECTORY_SEPARATOR . $filename;

        return $this;
    }

    public function output()
    {
        header('Content-Type:image/png');
        echo $this->source;
    }

    // 存檔
    public function save()
    {
        if (empty($this->setting))
            throw new \Exception("請先使用 save()");
            
        // 存放
        $result = file_put_contents($this->file, $this->source);
        chmod($this->file, $this->setting->mode);


        $path          = $this->setting->path;
        $filename      = trim($this->setting->filename, "\ /");

        $this->box[]  = $this->setting->path . DIRECTORY_SEPARATOR . $filename;
        parent::url($this->file);
        return $this;
    }

    /**
     * 覆蓋 parent::result()
     * @return base64img array
     * @return copy array
     */
    public function result()
    {
        $return['base64img'] = $this->box;

        if (!empty(parent::result()))
        {
            $return['copy'] = parent::result();
        }

        return $return;
    }
}
