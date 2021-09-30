<?php
$TAWtheDir = get_transient('TAWdir');
//CREATE DIRECTORY
if(isset($_FILES['taw_upload_file'])){
    $pdf = $_FILES['taw_upload_file'];
  //print_r($pdf);

  //$user_dirname = $uploadCheck;
  if ( !file_exists( $TAWtheDir ) ) {
    //echo "<p>Making directory</p>";
    mkdir($TAWtheDir,0777,true);
  }

// upload
//global $TAWreferralSlug;
//$dir3 = $TAWupload_dir["basedir"]."/uploader/". $TAWreferralSlug . '/';

//echo "<p>Loading to {$TAWreferralSlug}</p>";
//echo "<p>The target directory is {$target_dir}";
//$target_file = $target_dir . basename($_FILES['taw_upload_file']["name"]);
$TAWreferralSlug = get_transient('TAWslug');
echo "<p>Referral slug: {$TAWreferralSlug}</p>";
$TAWfullPath = $TAWupload_dir['basedir'] . '/uploader/'. $TAWreferralSlug;
echo "<p>TAW path: {$TAWfullPath}</p>";
$target_file = $TAWfullPath.basename($_FILES['taw_upload_file']["name"]);

//$target_file = $TAWtheDir.'/'.$TAWreferralSlug2.'/'.basename($_FILES['taw_upload_file']["name"]);
//echo "<div><p>Uploading {$target_file}</p></div>";
//print_r($target_file);
$uploadOk = 1;
//echo "<p>upload status to {$TAWreferralSlug}:". wp_cache_get('TAWuploaded');

$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// check file structure
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}
// check file size​
if ($_FILES['taw_upload_file']["size"] > 5000000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// File formats allowed
if($FileType != "jpg" && $FileType != "png" && $FileType != "jpeg"
&& $FileType != "gif" && $FileType != "doc" && $FileType != "docx"
&& $FileType != "txt" && $FileType != "xls" && $FileType != "xlsx"
&& $FileType != "pdf" && $FileType != "mp3" && $FileType != "mp4") {
  echo "Sorry, only JPG, JPEG, PNG, GIF, DOC, DOCX, TXT, XLS, XLSX, PDF, MP3 and MP4 files are allowed.";
  $uploadOk = 0;
}

//$uploadOk = 0;
// Check if upload is ok
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
  // if ok upload file
} else {
  if (move_uploaded_file($_FILES['taw_upload_file']["tmp_name"], $target_file)) {
    echo "<p>The file ". htmlspecialchars( basename( $_FILES['taw_upload_file']["name"])). " has been uploaded.
    </p><button onClick='window.location.reload();'>Refresh Page</button>";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
}
  ?>
