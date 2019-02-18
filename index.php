<?php
require ("doc2txt.class.php");
require ('pdfVendor/autoload.php');
function pdfText($filePath){
    $pdfData=array();
    $file = $filePath;     //// filename  directory path
    $fullfile =  $file;
    $parser = new \Smalot\PdfParser\Parser();
    $document = $parser->parseFile($fullfile);
    $pages    = $document->getPages();
    $pageCount= count($pages);
    for($i=0;$i<$pageCount; $i++){
        $page     = $pages[$i];
        $content  = $page->getText();
        $out      = trim(preg_replace('/\s+/', ' ', $content));
        $pdfData [] = $out;
    }  return $pdfData;
}
?>
<html>
<head>
    <script src='https://code.responsivevoice.org/responsivevoice.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php if ((!empty($_FILES))) :
    $filename = $_FILES['filename']['name'];
    $upload_path = str_replace('\\', '/', __DIR__.'/Uploadedfiles');
    $filePath =$upload_path .'/'. $filename;
    if(move_uploaded_file($_FILES["filename"]["tmp_name"],$upload_path .'/'. $filename))
    {     $docObj = new Doc2Txt($upload_path.'/'.$filename);
    $txt = str_replace(array("\n", "\r"), '', $docObj->convertToText());
    $pdfArr=pdfText($filePath);
    $arrCount=count(pdfText($filePath));   ///Pdf Text Data
    $NewPdfMe=array();
    for($i=0; $i<$arrCount;$i++){
        $NewPdfMe[]=$pdfArr[$i];
    }
    $NewPdfMerge=implode(' ',$NewPdfMe);
    if(empty($NewPdfMerge)){   $textData=$txt;    }else{     $textData=$NewPdfMerge;    }     ?>
    <input onclick='responsiveVoice.speak(text);' type='button' value='🔊 Play' />
    <a href="" download >Download</a>
    <script type="text/javascript">
        var text ="<?= $textData; ?>";
        var downLink='http://responsivevoice.org/responsivevoice/getvoice.php?t='+ text +'&tl=en-US';
        $("a").attr("href", downLink);
    </script>
<?php
    }else{ echo "File Uploading Error";  }
    else: ?>
    <form action=<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?> enctype="multipart/form-data" method="post">
        <input type="file" id="filename" name="filename" accept="application/msword, application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
        <input type="submit">
    </form>
<?php endif; ?>
</body>
</html>