<?php
class OSS {
    // This is a placeholder for actual OSS implementation (Aliyun/Tencent)
    // For now, we save to local disk
    public static function upload($file, $type) {
        $target_dir = __DIR__ . "/../../uploads/" . $type . "/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = uniqid() . "_" . basename($file["name"]);
        $target_file = $target_dir . $filename;
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            throw new Exception("File is not an image.");
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // Return the URL relative to the domain
            // Assuming the API is at /api, and uploads are at /uploads
            // We need to return a full URL or absolute path if possible
            // For this dev environment, we'll return a relative path that the frontend can prefix
            return "/uploads/" . $type . "/" . $filename;
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }
}
?>