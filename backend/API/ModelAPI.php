<?php

class ModelAPI
{
    public function getModel($file_name)
    {
        $path = getModelFile($file_name);
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        if ($path) {
            // print_r($path);
            ob_start();              // start buffer
            include $path;           // include HTML/PHP file
            $html = trim(ob_get_clean());  // capture HTML content

            return json_encode([
                'success' => true,
                'html' => $html,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            http_response_code(404);
            return json_encode(value: [
                'success' => false,
                'error' => 'File not found',
                'path' => $path
            ]);
        }
    }
}
