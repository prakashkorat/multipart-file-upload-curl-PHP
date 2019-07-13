
<?php
/*
 This function manually constructs the multipart request body from strings and injects it 
 into the supplied curl handle, with no need to touch the file system.
*/
 
function buildMultiPartReq($ch, $boundary, $fields, $files) {

    $delimiter = '-------------' . $boundary;
    $data = '';
    foreach ($fields as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
            . $content . "\r\n";
    }
    foreach ($files as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . "\r\n\r\n"
            . $content . "\r\n";
    }
    $data .= "--" . $delimiter . "--\r\n";
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: multipart/form-data; boundary=' . $delimiter,
            'Content-Length: ' . strlen($data)
        ],
        CURLOPT_POSTFIELDS => $data
    ]);
    return $ch;
}


$ch = curl_init('URL');
$ch = buildMultiPartReq($ch, uniqid(),
    ['key' => 'value', 'key2' => 'value2'], ['file1' => $_FILES['file1'], 'file2' => _FILES['file2']]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
